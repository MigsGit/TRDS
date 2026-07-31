<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
@page{
    size:A4 landscape;
    margin:20px 15px 55px 15px;
}

body{
    font-family:DejaVu Sans,sans-serif;
    font-size:14px;
    margin:0;
    padding:0;
}

.skill-table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

.skill-table th,
.skill-table td{
    border:1px solid #000;
    padding:5px;
    text-align:center;
    vertical-align:middle;
}

.title{
    font-size:18px;
    font-weight:bold;
    text-align:left;
    padding:8px;
}

.header th{
    background:#d9d9d9;
    font-weight:bold;
}

.department{
    color:#0056b3;
    font-weight:bold;
    font-size:18px;
}

.skill-header th{
    font-size: 11px;   /* Adjust to fit */
    font-weight: bold;
    line-height: 1.2;
    padding: 6px 2px;
}

.skill-table img{
    width:60px;
    height: auto;
}

.legend-table{
    width:100%;
    margin-top:15px;
    border-collapse:collapse;
    table-layout:fixed;
    border:1px solid #000;

}

.legend-table td{
    padding:8px;
    text-align:center;
    vertical-align:middle;
    font-size:14px;
}

td.legend-title{
    font-size:18px !important;
    font-weight:bold;
    text-align:center;
}

.legend-table img{
    width:60px;
    height: auto;
    margin-bottom:3px;
}

.footer{
    position:fixed;
    left:0;
    right:0;
    bottom:-25px;
}

.legend-item{
    width:100%;
    border-collapse:collapse;
    border:none;
}

.legend-item td{
    border:none !important;
    padding:2px;
    vertical-align:middle;
}


    @page{
        margin:20px;
    }

    body{
        font-family: DejaVu Sans, sans-serif;
        font-size:11px;
        color:#222;
    }

    table{
        border-collapse:collapse;
        width:100%;
    }

    .main-table,
    .legend-table{
        border:1px solid #444;
    }

    .main-table th,
    .main-table td,
    .legend-table td{
        border:1px solid #444;
        padding:6px;
        text-align:center;
        vertical-align:middle;
    }

    .title{
        font-size:24px;
        font-weight:bold;
        text-align:left;
        padding:12px;
    }

    .department{
        font-size:20px;
        color:#003cff;
        font-weight:bold;
    }

    .header{
        background:#efefef;
        font-weight:bold;
    }

    .skill-header{
        background:#f7f7f7;
        font-weight:bold;
        font-size:10px;
    }

    .employee{
        text-align:left;
    }

    .circle{
        width:18px;
        height:18px;
        border-radius:50%;
        display:inline-block;
        background:#ff7300;
    }

    .legend-title{
        font-size:18px;
        font-weight:bold;
        text-align:left;
    }

    .legend-circle{
        width:20px;
        height:20px;
        border-radius:50%;
        display:inline-block;
        margin-right:8px;
    }

    .l1{ background:#ffdcdc; }
    .l2{ background:#ffc266; }
    .l3{ background:#ff8c00; }
    .l4{ background:#ff5500; }

    .footer{
        margin-top:15px;
        font-size:10px;
        text-align:right;
        color:#666;
    }
</style>

</head>

<body>

@php
    $chunks = collect($employees)->chunk(7); // 7 rows per page
@endphp

@foreach($chunks as $pageIndex => $page)
<table class="skill-table" width="100%" cellspacing="0" cellpadding="4">
<table class="main-table">

    <!-- Title -->
    <tr>
        <td colspan="8" class="title">
            Employee Skill Matrix
        </td>
    </tr>

    <!-- Header -->
    <tr class="header">
        <th rowspan="2" width="5%">NO.</th>
        <th rowspan="2" width="30%">EMPLOYEE NAME</th>
        <th rowspan="2" width="15%">EMP NO.</th>
        <th rowspan="2" width="10%">DATE HIRED</th>

        <th colspan="4" class="department">
            {{ $productLine }}
        </th>
    </tr>

    <!-- Skill Header -->
    <tr class="skill-header">
        @foreach($productStation as $station)
            <th width="{{ 40 / count($productStation) }}%">
                {{ strtoupper($station['dropdown_masters_details']) }}
            </th>
        @endforeach
    </tr>


    @foreach($page  as $employee)

    <tr>

        <td align="center">{{ ($pageIndex * 7) + $loop->iteration }}</td>

        <td>{{ $employee['empName'] }}</td>

        <td align="center">{{ $employee['empNo'] }}</td>

        <td align="center">{{ $employee['dateHired'] }}</td>

       @foreach($productStation as $station)
            <td align="center">
                @if(!empty($employee['stations'][$station['id']]))
                    <img src="{{ public_path('images/'.$employee['stations'][$station['id']]) }}">
                @endif
            </td>
        @endforeach

    </tr>

    @endforeach
    <!-- Main Header -->
    <tr class="header">

        <th rowspan="2" width="5%">
            NO.
        </th>

        <th rowspan="2" width="30%">
            EMPLOYEE NAME
        </th>

        <th rowspan="2" width="10%">
            EMP NO.
        </th>

        <th rowspan="2" width="15%">
            DATE HIRED
        </th>

        <th colspan="4" class="department">
            DEPARTMENT NAME
        </th>

    </tr>

    <!-- Skill Headers -->
    <tr class="skill-header">

        <th width="10%">
            ASSEMBLY<br>PROCESS
        </th>

        <th width="10%">
            VISUAL<br>INSPECTION
        </th>

        <th width="10%">
            PARTS<br>PREP.
        </th>

        <th width="10%">
            MACHINE<br>OPERATION
        </th>

    </tr>

    <!-- Sample Rows -->
    @for($i = 1; $i <= 15; $i++)

    <tr>

        <td>{{ $i }}</td>

        <td class="employee"></td>

        <td></td>

        <td></td>

        <td>
            <span class="circle"></span>
        </td>

        <td>
            <span class="circle"></span>
        </td>

        <td>
            <span class="circle"></span>
        </td>

        <td>
            <span class="circle"></span>
        </td>

    </tr>

    @endfor

</table>

<br>

<div class="footer">
    <table class="legend-table">
        <tr>
            <td width="12%" class="legend-title">
                Legend:
            </td>

            <td width="22%">
                <table class="legend-item">
                    <tr>
                        <td width="30%" align="center">
                            <img src="{{ public_path('images/level1.png') }}">
                        </td>
                        <td width="70%">
                            <strong>Level 1 -</strong><br>
                            Awareness on the Process
                        </td>
                    </tr>
                </table>
            </td>

            <td width="22%">
                <table class="legend-item">
                    <tr>
                        <td width="30%" align="center">
                            <img src="{{ public_path('images/level2.png') }}">
                        </td>
                        <td width="70%">
                            <strong>Level 2 -</strong><br>
                            Knowledgeable on the Process
                        </td>
                    </tr>
                </table>
            </td>

            <td width="22%">
                <table class="legend-item">
                    <tr>
                        <td width="30%" align="center">
                            <img src="{{ public_path('images/level3.png') }}">
                        </td>
                        <td width="70%">
                            <strong>Level 3 -</strong><br>
                            Can perform with less supervision
                        </td>
                    </tr>
                </table>
            </td>

            <td width="22%">
                <table class="legend-item">
                    <tr>
                        <td width="30%" align="center">
                            <img src="{{ public_path('images/level4.png') }}">
                        </td>
                        <td width="70%">
                            <strong>Level 4 -</strong><br>
                            Can mentor co-employee
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>


@if(!$loop->last)
    <div style="page-break-after: always;"></div>
@endif
@endforeach

<table class="legend-table">

    <tr>

        <td width="20%" class="legend-title">
            Legend
        </td>

        <td width="20%">
            <span class="legend-circle l1"></span>
            Level 1 - Awareness
        </td>

        <td width="20%">
            <span class="legend-circle l2"></span>
            Level 2 - Knowledgeable
        </td>

        <td width="20%">
            <span class="legend-circle l3"></span>
            Level 3 - Can Perform Independently
        </td>

        <td width="20%">
            <span class="legend-circle l4"></span>
            Level 4 - Can Train Others
        </td>

    </tr>

</table>

<div class="footer">
    Generated by Training Records Database System
</div>


</body>
</html>
