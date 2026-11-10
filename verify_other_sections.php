<?php

/**
 * TEMPORARY, READ-ONLY verification for InspectorSkillChart Phase 1. Delete after use.
 * Run from the project root:  php verify_other_sections.php
 *
 * Part 1 reads the database (SELECT only). Part 2 uses in-memory synthetic data only.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Exports\InspectorSkillChart;
use App\Exports\InspectorSkillChartSheets\TSF1Layout;
use Illuminate\Support\Facades\DB;

// Read-only guard: report any statement that is not a SELECT.
$writes = [];
DB::listen(function ($query) use (&$writes) {
    if (!preg_match('/^\s*select\b/i', $query->sql)) {
        $writes[] = $query->sql;
    }
});

/** Read a protected/private property. */
function prop($obj, $name)
{
    $f = Closure::bind(function () use ($name) {
        return $this->$name;
    }, $obj, get_class($obj));

    return $f();
}

/** Call a private method. */
function priv($obj, $method, array $args)
{
    $f = Closure::bind(function () use ($method, $args) {
        return $this->$method(...$args);
    }, $obj, get_class($obj));

    return $f();
}

function out($value)
{
    return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/** sheet name => [employee_no => employee as passed to TSF1] */
function sheetEmployees(InspectorSkillChart $export)
{
    $map = [];
    foreach ($export->sheets() as $sheet) {
        $map[prop($sheet, 'sheetName')] = prop($sheet, 'employees')->keyBy('employee_no')->all();
    }

    return $map;
}

function appearsOn(array $sheetMap, $employeeNo)
{
    $on = [];
    foreach ($sheetMap as $sheet => $employees) {
        if (array_key_exists($employeeNo, $employees)) {
            $on[] = $sheet;
        }
    }

    return $on;
}

/**
 * Generic structural check of other_sections for an employee on $sheet.
 * Returns a list of error strings (empty = OK).
 */
function checkOther(array $emp, $sheet)
{
    $errors = [];
    $groups = InspectorSkillChart::SHEET_GROUPS;
    $own = $groups[$sheet];
    $expectedKeys = array_values(array_diff(TSF1Layout::OTHER_SECTIONS, [$own]));

    if (!isset($emp['sections']['main']) || $emp['sections']['main']['section'] !== $sheet) {
        $errors[] = "main is not {$sheet}";
    }
    if (!isset($emp['other_sections'])) {
        return array_merge($errors, ['other_sections missing']);
    }

    $other = $emp['other_sections'];
    $actualKeys = $other->keys()->all();
    if ($actualKeys !== $expectedKeys) {
        $errors[] = 'other_sections keys ' . out($actualKeys) . ' expected ' . out($expectedKeys);
    }

    // Expected: newest station per station_to, per group/process, from non-main sections only
    $slipGroup = [];
    $expected = [];
    foreach ($emp['sections'] as $name => $section) {
        if ($name === 'main') {
            continue;
        }
        $g = (is_string($name) && isset($groups[$name])) ? $groups[$name] : null;
        foreach ($section['stations'] as $st) {
            $slipGroup[$st['slip_id']] = $g;
            if ($g === null || $g === $own) {
                continue;
            }
            foreach (TSF1Layout::OTHER_PROCESSES as $p => $names) {
                if (in_array(trim((string) $st['station_to_name']), $names, true)) {
                    $key = (string) $st['station_to'];
                    if (!isset($expected[$g][$p][$key]) || $expected[$g][$p][$key] < $st['slip_id']) {
                        $expected[$g][$p][$key] = $st['slip_id'];
                    }
                }
            }
        }
    }

    foreach ($other as $g => $processes) {
        if (array_keys($processes) !== array_keys(TSF1Layout::OTHER_PROCESSES)) {
            $errors[] = "{$g}: process keys " . out(array_keys($processes));
        }
        foreach ($processes as $p => $cell) {
            if (!is_int($cell['total'])) {
                $errors[] = "{$g}.{$p}: total is not int";
            }
            $seen = [];
            foreach ($cell['stations'] as $st) {
                $src = array_key_exists($st['slip_id'], $slipGroup) ? $slipGroup[$st['slip_id']] : 'main/unknown';
                if ($src !== $g) {
                    $errors[] = "{$g}.{$p}: station from slip {$st['slip_id']} (group " . var_export($src, true) . ') leaked in';
                }
                $key = (string) $st['station_to'];
                if (isset($seen[$key])) {
                    $errors[] = "{$g}.{$p}: duplicate station_to {$key}";
                }
                $seen[$key] = $st['slip_id'];
            }
            $want = isset($expected[$g][$p]) ? $expected[$g][$p] : [];
            ksort($want);
            ksort($seen);
            if ($want != $seen) {
                $errors[] = "{$g}.{$p}: stations (station_to => slip_id) " . out($seen) . ' expected ' . out($want);
            }
            if (empty($want) && $cell['total'] !== 0) {
                $errors[] = "{$g}.{$p}: total {$cell['total']} but no source data";
            }
        }
    }

    return $errors;
}

function report($title, $pass, array $errors, $data, $alwaysPrintData)
{
    echo "\n=== {$title} ===\n";
    if ($alwaysPrintData || !$pass) {
        echo out($data) . "\n";
    }
    echo $pass ? "RESULT: PASS\n" : "RESULT: FAIL\n  - " . implode("\n  - ", $errors) . "\n";
}

$allSheets = array_keys(InspectorSkillChart::SHEET_GROUPS);
$failures = 0;

// =====================================================================
// PART 1 - real database (read-only)
// =====================================================================
echo "########## PART 1: DATABASE (read-only) ##########\n";
echo 'OTHER_CELL_MODE = ' . TSF1Layout::OTHER_CELL_MODE . "\n";

$export = new InspectorSkillChart($allSheets);
$grouped = prop($export, 'grouped');
$sheetMap = sheetEmployees($export);

echo 'Employees loaded: ' . $grouped->count() . "\n";
foreach ($sheetMap as $sheet => $employees) {
    echo "  sheet {$sheet}: " . count($employees) . " employee(s)\n";
}

$dbCases = [
    'A' => ['title' => 'Case A: current TSF3, older TSF1 and/or CN', 'current' => 'TSF3', 'older' => ['TSF1', 'CN']],
    'B' => ['title' => 'Case B: current CN, older TSF1/TSF3', 'current' => 'CN', 'older' => ['TSF1', 'TSF3']],
    'C' => ['title' => 'Case C: current TSF1, older TSF3', 'current' => 'TSF1', 'older' => ['TSF3']],
];

foreach ($dbCases as $case) {
    $candidate = $grouped->first(function ($e) use ($case) {
        if ($e['current_section'] !== $case['current']) {
            return false;
        }
        foreach ($case['older'] as $s) {
            if ($e['sections']->has($s)) {
                return true;
            }
        }

        return false;
    });

    if ($candidate === null) {
        echo "\n=== {$case['title']} ===\nNO MATCHING EMPLOYEE IN DATABASE (skipped, not a failure)\n";
        continue;
    }

    $no = $candidate['employee_no'];
    $on = appearsOn($sheetMap, $no);
    $errors = [];
    if ($on !== [$case['current']]) {
        $errors[] = 'appears on ' . out($on) . ', expected ["' . $case['current'] . '"]';
    }
    $emp = isset($sheetMap[$case['current']][$no]) ? $sheetMap[$case['current']][$no] : null;
    if ($emp === null) {
        $errors[] = 'not on its current sheet';
    } else {
        $errors = array_merge($errors, checkOther($emp, $case['current']));
    }

    $data = [
        'employee_no'     => $no,
        'current_section' => $candidate['current_section'],
        'current_station' => $candidate['current_station'],
        'appears_on'      => $on,
        'sections_keys'   => $emp ? $emp['sections']->keys()->all() : $candidate['sections']->keys()->all(),
        'other_sections'  => $emp && isset($emp['other_sections']) ? $emp['other_sections'] : null,
    ];
    $failures += empty($errors) ? 0 : 1;
    report($case['title'], empty($errors), $errors, $data, true);
}

// =====================================================================
// PART 2 - synthetic data (in memory, no DB)
// =====================================================================
echo "\n########## PART 2: SYNTHETIC (in memory) ##########\n";

$fake = (new ReflectionClass(InspectorSkillChart::class))->newInstanceWithoutConstructor();

/** One fake slip row, turned into a station by the real buildStation(). */
function rec($slipId, $stationTo, $name, $result, $subs = null)
{
    return (object) [
        'slip_id'         => $slipId,
        'station_to'      => $stationTo,
        'station_to_name' => $name,
        'training_result' => $result,
        'sub_station'     => $subs,
    ];
}

/** Fake employee shaped like the constructor's output. $sections: name => [rec, ...] */
function emp($fake, $no, $current, array $sections)
{
    $built = collect();
    foreach ($sections as $name => $records) {
        $built->put($name, [
            'section'  => $name,
            'stations' => collect($records)
                ->sortByDesc('slip_id')
                ->unique('station_to')
                ->map(function ($r) use ($fake) {
                    return priv($fake, 'buildStation', [$r]);
                })
                ->values(),
        ]);
    }

    return [
        'employee_no'              => $no,
        'employee_name'            => 'SYNTHETIC ' . $no,
        'date_hired'               => null,
        'length_of_service_year'   => null,
        'length_of_service_months' => null,
        'sections'                 => $built,
        'current_section'          => $current,
        'current_station'          => null,
    ];
}

/** total / slip ids helper */
function cell(array $emp, $group, $process)
{
    return $emp['other_sections'][$group][$process];
}

function slips(array $cell)
{
    return array_map(function ($s) {
        return $s['slip_id'];
    }, $cell['stations']);
}

$synthetic = [
    'S1' => emp($fake, 'S1', 'CNF3', [
        'CNF3'  => [rec(400, 11, 'IPQC', '2')],
        'CN'    => [rec(300, 10, 'IQC', '3')],
        'TSF1'  => [rec(200, 10, 'IQC', '4')],
        'PPDCN' => [rec(100, 12, 'OQC', '1')],
    ]),
    'S2' => emp($fake, 'S2', 'TSF1', [
        'TSF1' => [rec(500, 10, 'IQC', '1')],
        'CNF3' => [rec(450, 20, 'IQC', '3')],
        'CN'   => [rec(350, 20, 'IQC', '2'), rec(350, 21, 'IPQC', '4')],
    ]),
    'S3' => emp($fake, 'S3', 'YF', [
        'YF'    => [rec(600, 30, 'OQC', '2')],
        'TSF1'  => [rec(550, 31, 'IPQC', '3')],
        'CN'    => [rec(520, 32, 'IQC', '2')],
        'PPDTS' => [rec(510, 33, 'OQC', '4')],
    ]),
    'S4' => emp($fake, 'S4', 'PPDF3', [
        'PPDF3' => [rec(700, 40, 'IQC', '1')],
        'PPDCN' => [rec(650, 41, 'IQC', '2')],
        'PPDTS' => [rec(640, 42, 'OQC', '3')],
        'TSF1'  => [rec(630, 43, 'IQC', '4')],
    ]),
    'S5' => emp($fake, 'S5', 'TSF3', [
        'TSF3' => [rec(800, 50, 'IQC', '2')],
        'XYZ'  => [rec(790, 51, 'IQC', '3')],
        ''     => [rec(780, 52, 'OQC', '4')],
    ]),
    'S6' => emp($fake, 'S6', 'XYZ', ['XYZ' => [rec(900, 60, 'IQC', '3')]]),
    'S7' => emp($fake, 'S7', '', ['' => [rec(910, 61, 'IQC', '3')]]),
];

// One minimal employee per SHEET_GROUPS key (coverage check)
foreach ($allSheets as $sheet) {
    $synthetic['K_' . $sheet] = emp($fake, 'K_' . $sheet, $sheet, [$sheet => [rec(1000, 70, 'IQC', '1')]]);
}

$selected = array_merge($allSheets, ['BOGUS']);   // unknown sheet name must be skipped
Closure::bind(function ($grouped, $selected) {
    $this->grouped = $grouped;
    $this->selectedSheets = $selected;
    $this->processStationDetails = collect();
}, $fake, InspectorSkillChart::class)(collect(array_values($synthetic)), $selected);

try {
    $synMap = sheetEmployees($fake);
} catch (Throwable $e) {
    echo 'CRASH in sheets(): ' . get_class($e) . ': ' . $e->getMessage() . "\n";
    exit(1);
}

$synCases = [
    'S1' => ['Case 1: current CNF3, older CN/TSF1/PPDCN', 'CNF3', function ($e) {
        $err = [];
        if (cell($e, 'TS', 'IQC')['total'] !== 4) {
            $err[] = 'TS.IQC total != 4';
        }
        if (cell($e, 'PPD', 'OQC')['total'] !== 1) {
            $err[] = 'PPD.OQC total != 1';
        }
        foreach ($e['other_sections'] as $g => $ps) {
            foreach ($ps as $p => $c) {
                if (in_array(300, slips($c), true)) {
                    $err[] = "CN slip 300 leaked into {$g}.{$p}";
                }
            }
        }
        foreach ($e['other_sections']['YF'] as $p => $c) {
            if ($c['total'] !== 0 || !empty($c['stations'])) {
                $err[] = "YF.{$p} not empty";
            }
        }

        return $err;
    }],
    'S2' => ['Case 2: current TSF1, CNF3 + CN both with IQC', 'TSF1', function ($e) {
        $err = [];
        $iqc = cell($e, 'CN', 'IQC');
        if (slips($iqc) !== [450]) {
            $err[] = 'CN.IQC stations slips ' . out(slips($iqc)) . ' expected [450]';
        }
        if ($iqc['total'] !== 3) {
            $err[] = "CN.IQC total {$iqc['total']} expected 3 (not 5)";
        }
        if (cell($e, 'CN', 'IPQC')['total'] !== 4) {
            $err[] = 'CN.IPQC total != 4 (CN data not merged)';
        }

        return $err;
    }],
    'S3' => ['Case 3: current YF, older TSF1/CN/PPDTS', 'YF', function ($e) {
        $err = [];
        if (cell($e, 'TS', 'IPQC')['total'] !== 3) {
            $err[] = 'TS.IPQC != 3';
        }
        if (cell($e, 'CN', 'IQC')['total'] !== 2) {
            $err[] = 'CN.IQC != 2';
        }
        if (cell($e, 'PPD', 'OQC')['total'] !== 4) {
            $err[] = 'PPD.OQC != 4';
        }

        return $err;
    }],
    'S4' => ['Case 4: current PPDF3, older PPDCN/PPDTS (+TSF1)', 'PPDF3', function ($e) {
        $err = [];
        foreach ($e['other_sections'] as $g => $ps) {
            foreach ($ps as $p => $c) {
                if (array_intersect([650, 640], slips($c))) {
                    $err[] = "PPD slip leaked into {$g}.{$p}";
                }
            }
        }
        if (cell($e, 'TS', 'IQC')['total'] !== 4) {
            $err[] = 'TS.IQC != 4';
        }

        return $err;
    }],
    'S5' => ['Case 5a: current TSF3 with XYZ and empty-name sections', 'TSF3', function ($e) {
        $err = [];
        foreach ($e['other_sections'] as $g => $ps) {
            foreach ($ps as $p => $c) {
                if ($c['total'] !== 0 || !empty($c['stations'])) {
                    $err[] = "{$g}.{$p} has data from an unknown/empty section";
                }
            }
        }

        return $err;
    }],
];

foreach ($synCases as $no => $case) {
    list($title, $sheet, $extra) = $case;
    $on = appearsOn($synMap, $no);
    $errors = [];
    if ($on !== [$sheet]) {
        $errors[] = 'appears on ' . out($on) . ", expected [\"{$sheet}\"]";
    }
    $e = isset($synMap[$sheet][$no]) ? $synMap[$sheet][$no] : null;
    if ($e === null) {
        $errors[] = "not on sheet {$sheet}";
    } else {
        $errors = array_merge($errors, checkOther($e, $sheet));
        if (empty($errors)) {
            $errors = $extra($e);
        }
    }
    $failures += empty($errors) ? 0 : 1;
    report($title, empty($errors), $errors, [
        'employee_no'    => $no,
        'appears_on'     => $on,
        'sections_keys'  => $e ? $e['sections']->keys()->all() : null,
        'other_sections' => $e && isset($e['other_sections']) ? $e['other_sections'] : null,
    ], false);
}

// Case 5b: current section unknown / empty -> on no sheet, no crash
$errors = [];
foreach (['S6', 'S7'] as $no) {
    $on = appearsOn($synMap, $no);
    if (!empty($on)) {
        $errors[] = "{$no} appears on " . out($on);
    }
}
$failures += empty($errors) ? 0 : 1;
report('Case 5b: current section XYZ / empty -> on no sheet', empty($errors), $errors, null, false);

// Coverage: every SHEET_GROUPS key, unknown sheet skipped, OTHER_SECTIONS
$errors = [];
$built = array_keys($synMap);
if ($built !== $allSheets) {
    $errors[] = 'sheets built ' . out($built) . ' expected ' . out($allSheets) . ' (BOGUS must be skipped)';
}
foreach ($allSheets as $sheet) {
    $no = 'K_' . $sheet;
    $on = appearsOn($synMap, $no);
    if ($on !== [$sheet]) {
        $errors[] = "{$no} appears on " . out($on);
        continue;
    }
    foreach (checkOther($synMap[$sheet][$no], $sheet) as $msg) {
        $errors[] = "{$sheet}: {$msg}";
    }
}
foreach (array_unique(array_values(InspectorSkillChart::SHEET_GROUPS)) as $g) {
    if (!in_array($g, TSF1Layout::OTHER_SECTIONS, true)) {
        $errors[] = "group {$g} not in TSF1Layout::OTHER_SECTIONS";
    }
}
if (TSF1Layout::OTHER_SECTIONS !== ['TS', 'CN', 'PPD', 'YF']) {
    $errors[] = 'TSF1Layout::OTHER_SECTIONS is ' . out(TSF1Layout::OTHER_SECTIONS);
}
$failures += empty($errors) ? 0 : 1;
report('Coverage: all 8 SHEET_GROUPS keys, BOGUS skipped, OTHER_SECTIONS = TS,CN,PPD,YF', empty($errors), $errors, null, false);

echo "\nNon-SELECT statements executed: " . (empty($writes) ? 'none' : out($writes)) . "\n";
echo $failures === 0 ? "\nALL CHECKS PASSED\n" : "\n{$failures} CHECK(S) FAILED\n";
