<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Employee Skill Card (PDF)</title>

        <style>
            @page {
                size: 140mm 100mm;
                margin: 0;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                width: 140mm;
                height: 100mm;
            }

            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 6px;
            }

            * {
                box-sizing: border-box;
            }


            /* =========================================================
            MAIN 140mm
            ========================================================= */

            .main {
                width: 140mm;
                height: 100mm;

                margin: 0;
                padding: 0;

                border-collapse: collapse;
                table-layout: fixed;
            }


            /* =========================================================
            EACH SIDE = 70mm
            ========================================================= */

            .half-page {
                width: 70mm;
                height: 100mm;

                padding: 0;
                margin: 0;

                vertical-align: top;
            }


            /* =========================================================
            INNER PAGE
            ========================================================= */

            .page-layout {
                width: 70mm;
                height: 100mm;

                padding: 0;
                margin: 0;

                border-collapse: collapse;
                table-layout: fixed;
            }


            /* =========================================================
            REMOVE SPACE AROUND NESTED TABLES
            ========================================================= */

            .employee-wrapper,
            .station-wrapper,
            .assessment-wrapper,
            .functional-wrapper,
            .performance-wrapper,
            .approved-wrapper {
                padding: 0;
                margin: 0;
            }
        </style>
    </head>
    <body>
        <!-- Main Table -->
        <table class="main">
            <tr>

                {{-- =====================================================
                    LEFT SIDE - 70mm
                ====================================================== --}}

                <td class="half-page">

                    <table class="page-layout">

                        {{-- TOP / EMPLOYEE INFORMATION --}}
                        <tr>
                            <td class="employee-wrapper">

                                @include('pdf.parts.employee')

                            </td>
                        </tr>

                        {{-- STATION ASSIGNED --}}
                        <tr>
                            <td class="station-wrapper">

                                @include('pdf.parts.stations')

                            </td>
                        </tr>

                    </table>

                </td>


                {{-- =====================================================
                    RIGHT SIDE - 70mm
                ====================================================== --}}

                <td class="half-page">

                    <table class="page-layout">

                        {{-- HEADER + REFERENCE --}}
                        <tr>
                            <td class="assessment-wrapper">

                                @include('pdf.parts.assessment')

                            </td>
                        </tr>

                        {{-- FUNCTIONAL COMPETENCIES --}}
                        <tr>
                            <td class="functional-wrapper">

                                @include('pdf.parts.functional')

                            </td>
                        </tr>

                        {{-- PERFORMANCE --}}
                        <tr>
                            <td class="performance-wrapper">

                                @include('pdf.parts.performance')

                            </td>
                        </tr>

                        {{-- APPROVED --}}
                        <tr>
                            <td class="approved-wrapper">

                                @include('pdf.parts.approved')

                            </td>
                        </tr>

                    </table>

                </td>

            </tr>

        </table>
    </body>
</html>