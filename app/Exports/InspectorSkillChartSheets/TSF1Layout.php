<?php
namespace App\Exports\InspectorSkillChartSheets;

/**
 * All fixed facts about the Inspector Skill Chart template in one place.
 * Change a position, color, label, date format, or formula flag here only -
 * TSF1 never hard-codes them.
 */
class TSF1Layout
{
    const TITLE_ROW_1 = 1;
    const TITLE_ROW_2 = 2;
    const HEADER_ROW_1 = 3; // skill category labels
    const HEADER_ROW_2 = 4; // process labels
    const HEADER_ROW_3 = 5; // product line sub-labels / legend numbers
    const EMPLOYEE_START_ROW = 6;

    // Fixed (non-dynamic) columns A-G.
    const COL_NO = 1;
    const COL_EMPLOYEE_NO = 2;
    const COL_NAME = 3;
    const COL_DATE_HIRED = 4;
    const COL_SERVICE_YEARS = 5;
    const COL_SERVICE_MONTHS = 6;
    const COL_PRESENT_ALLOCATION = 7;

    // First column used for dynamic skill categories (column H).
    const DYNAMIC_START_COLUMN = 8;

    const DATE_FORMAT = 'M d, Y';

    const REPORT_SUBTITLE_DATE_FORMAT = 'F Y';

    const OTHER_SECTIONS = array('TS', 'CN', 'PPD', 'YF');

    /**
     * Per-employee "also trained in other sections" data has no column in the
     * template yet. Hook exists (see TSF1::renderOtherSectionsHook()) but stays
     * a no-op until a real column mapping is designed and this flips to true.
     */
    const OTHER_SECTIONS_DATA_ENABLED = false;

    /** Legend scale shown under "Total number of certified QC Inspectors per skill". */
    const LEGEND = array(
        1 => 'Awareness and understanding only',
        2 => 'Can do with assistance',
        3 => 'Skilled, no supervision required, can lead and review work of others',
        4 => 'Expert / Can perform without supervision',
    );

    /** Alternating fill colors applied per process/product-line column, keyed by category name. */
    const CATEGORY_COLOR_MAP = array(
        'PROCESS / SYSTEM SKILLS' => array(
            'header2' => array('grayFill', 'orangeFill', 'greenFill', 'blueFill', 'blueFill'),
            'header3' => array('lightGrayFill', 'lightOrangeFill', 'lightGreenFill'),
        ),
    );

    const COLORS = array(
        'yellow' => 'FFFF00',
        'lightYellow' => 'FFFFCC',
        'gray' => 'AEAAAA',
        'lightGray' => 'EDEDED',
        'orange' => 'F4B084',
        'lightOrange' => 'FCE4D6',
        'green' => 'A9D08E',
        'lightGreen' => 'E2EFDA',
        'blue' => '9BC2E6',
    );

    const COLUMN_WIDTHS = array(
        'A' => 6,
        'B' => 22.29,
        'C' => 28,
        'D' => 14,
        'E' => 10,
        'F' => 10,
        'G' => 20.29,
    );

    const DYNAMIC_COLUMN_WIDTH = 12;

    /**
     * Single-line "enable" switches for the deferred formulas in
     * TSF1::emitDeferredFormulas(). Flip a value to true to turn that
     * formula's static placeholder into a live Excel formula.
     */
    const FORMULA_FLAGS = array(
        'skill_level_count' => false,
    );

    /**
     * Documentation-only mirror of FORMULA_FLAGS, so an auditor can see what each
     * flag does without reading TSF1::emitDeferredFormulas() line by line.
     */
    const FORMULAS = array(
        'skill_level_count' => array(
            'target' => 'Summary block total row for each legend level (1-4), per dynamic skill column',
            'formula' => '=COUNTIF({range},"{level}")',
            'meaning' => 'How many employees scored {level} in that specific skill column',
        ),
    );
}
