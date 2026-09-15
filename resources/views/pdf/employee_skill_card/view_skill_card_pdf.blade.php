<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Employee Skill Card (PDF)</title>
         
        <!-- <style>
            html,
            body{
                width:100%;
                height:100%;
                margin:0;
                padding:0;
            }

            .main{
                width:100%;
                height:200mm;          /* or calc from your page */
                border-collapse:collapse;
                table-layout:fixed;
            }

            .main > tbody > tr{
                height:100%;
            }

            .half-page{
                width:50%;
                height:100%;
                padding:0;
                vertical-align:top;
            }

            .page-layout{
                width:100%;
                height:100%;
                border-collapse:collapse;
                table-layout:fixed;
            }

            .page-layout > tbody > tr{
                height:100%;
            }
        </style> -->

        <style>
            /* ==============================
            HEADER PART 1
            ================================ */

            @page{
                margin:8px;
            }

            body{
                font-family: DejaVu Sans, sans-serif;
                font-size:10px;
            }

            table{
                border-collapse:collapse;
                width:100%;
            }

            td,th{
                border:1px solid #444;
                padding:3px;
            }

            .center{
                text-align:center;
            }

            .middle{
                vertical-align:middle;
            }

            /* ==============================
            COMPETENCY MATRIX PART 2
            ================================ */

            .matrix-header{
                background:#F7F7F7;
                font-size:9px;
                text-align:center;
                font-style:italic;
            }

            .skill-box{
                width:2px;
                height:2px;
                text-align:center;
                padding:0;
            }

            .skill-fill{
                background:#18EAF2;
            }

            .matrix-row td{
                height:56px;
            }

            /* ==============================
            RIGHT SIDE PART 3
            ================================ */

            .legend-table td,
            .legend-table th{
                border:1px solid #444;
                padding:4px;
                font-size:9px;
            }

            .legend-header{
                background:#B8F4CF;
                font-weight:bold;
                text-align:center;
            }

            .level-circle-small{
                width:26px;
                height:26px;
                border-radius:50%;
                display:inline-block;
            }

            .level1{
                background:#E91E63;
            }

            .level2{
                background:#FBC02D;
            }

            .level3{
                background:#29B6F6;
            }

            .level4{
                background:#4CAF50;
            }

            .signature-box{
                height:65px;
                text-align:center;
                vertical-align:bottom;
            }

            .signature-line{
                border-top:1px solid #000;
                margin-top:50px;
                padding-top:3px;
                font-size:9px;
            }

            .main{
                width:100%;
                height: 300px;
                table-layout:fixed;
                border-collapse:collapse;
            }

            /* Give padding to cells inside the nested table */
            .main td.no_padding {
                padding: 0;
            }

            .half-page{
                width:50%;
                vertical-align:top;
                padding:0;
                /* border: none; */
            }

            .page-layout{
                width:100%;
                table-layout:fixed;
                border-collapse:collapse;
            }
        </style>
    </head>
    <body>
        <!-- Main Table -->
        <table class="main">
            <tr>
                <!-- LEFT HALF -->
                <td class="half-page">
                    <table class="page-layout">
                        <tr>
                            <td class="no_padding">
                                <table class="employee-section">
                                    <tr>
                                        <td height="39.5px" colspan="4">
                                            <span class="title">
                                                PRICON MICROELECTRONICS, INC.
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="center subtitle" colspan="4">
                                            PERSONNEL SKILL CARD
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" class="center header-green section-title">
                                            YF SECTION
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td width="20%" class="info-label gray">
                                            Name
                                        </td>

                                        <td width="30%" class="info-value">
                                            {{ 'Clark' }}
                                        </td>

                                        <td width="25%" rowspan="6" class="center">
                                            <div class="level-wrapper">
                                                <div class="level-circle-small">
                                                    LEVEL
                                                    <br>
                                                    {{ 2 }}
                                                </div>
                                            </div>
                                        </td>

                                        <td width="25%" rowspan="6" class="center">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="info-label gray">
                                            Position
                                        </td>

                                        <td class="info-value">
                                            {{ 'IT' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="info-label gray">
                                            Date Hired
                                        </td>

                                        <td class="info-value">
                                            {{ 04/23/2022 }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="info-label gray">
                                            Section
                                        </td>

                                        <td class="info-value">
                                            {{ 'ISS' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="info-label gray">
                                            Date Certified
                                        </td>

                                        <td class="info-value">
                                            {{ '03/09/1999' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="info-label gray">
                                            Validity Period
                                        </td>

                                        <td class="info-value">
                                            {{ '12/31/2026' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" class="total-rating">
                                            TOTAL RATINGS :
                                            <b>{{ 40 }} PTS</b>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="no_padding">
                                <table class="matrix-section">
                                    <tr>
                                        <td rowspan="2" colspan="2" class="center">
                                            YF PRODUCTS
                                        </td>

                                        <td colspan="7" class="center">
                                            FUNCTIONAL COMPETENCIES
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="matrix-header">
                                            Testing only
                                        </td>

                                        <td class="matrix-header">
                                            Material Prep. / Mgt.
                                        </td>

                                        <td class="matrix-header">
                                            Documents Prep. / Mgt.
                                        </td>

                                        <td class="matrix-header">
                                            Visual Inspection
                                        </td>

                                        <td class="matrix-header">
                                            Assembly Process
                                        </td>

                                        <td class="matrix-header">
                                            Parts Prep.
                                        </td>

                                        <td class="matrix-header">
                                            Machine Operation
                                        </td>
                                    </tr>

                                    @foreach($products as $product)
                                    <tr class="matrix-row">
                                        @if($product == $products[0])
                                            <td rowspan="{{ count($products) }}" class="no_padding">
                                                <table style="border: none;">
                                                    <tr>
                                                        <td class="center">
                                                            LEVEL 1
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="center">
                                                            Date Certified
                                                            <br>
                                                            <b>JAN. 2026</b>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="center">
                                                            Valid Until
                                                            <br>
                                                            <b>JULY 2026</b>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        @endif

                                        @if( count($products) > 1)
                                            <td>
                                                {{ $product['name'] }}
                                            </td>
                                        @endif

                                        @foreach($product['skills'] as $skill)
                                            <td class="skill-box {{ $skill ? 'skill-fill' : '' }}">
                                                <table style="width:100%; border-collapse:collapse;">
                                                    <tr>
                                                        <td style="width:50%; border:0.1px solid #030101;">1</td>
                                                        <td style="width:50%; border:0.1px solid #030101;">2</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:0.1px solid #030101;">3</td>
                                                        <td style="border:0.1px solid #030101;">4</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach

                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                
                <!-- RIGHT HALF -->
                <td class="half-page">
                    <table class="page-layout">
                        <tr>
                            <td height="65px" width="40%" class="center">
                                <span class="title">
                                    Pricon Logo
                                </span>
                            </td>

                            <td width="60%" class="center subtitle">
                                COMPETENCY SKILLS ASSESSMENT
                            </td>
                        </tr>

                        <tr>
                            <td width="40%" valign="top" class="no_padding">
                                <table>
                                    <tr class="center_no_border">
                                        <td height="67px;" class="center">
                                            FUNCTIONAL <br> COMPETENCIES
                                        </td>

                                        <td class="center">
                                            TARGET <br> RATING
                                        </td>
                                    </tr>

                                    <tr class="func-row">
                                        <td>
                                            {{ 'Material Prep 1' }}
                                        </td>

                                        <td class="center bold">
                                            {{ '4' }}
                                        </td>
                                    </tr>

                                    <tr class="func-row">
                                        <td>
                                            {{ 'Material Prep 2' }}
                                        </td>

                                        <td class="center bold">
                                            {{ '3' }}
                                        </td>
                                    </tr>

                                    <tr class="func-row">
                                        <td>
                                            {{ 'Material Prep 3' }}
                                        </td>

                                        <td class="center bold">
                                            {{ '2' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <td width="60%" class="no_padding">
                                {{-- RATINGS TABLE --}}
                                <table>
                                    <tr>
                                        <th height="30px" colspan="4" class="center">
                                            REFERENCE
                                        </th>
                                    </tr>

                                    <tr>
                                        <th height="30px" colspan="4" class="legend-header">
                                            RATINGS
                                        </th>
                                    </tr>

                                    <tr>
                                        <td height="5%">{{-- image 1 --}}</td>
                                        <td height="5%">{{-- image 2 --}}</td>
                                        <td height="5%">{{-- image 3 --}}</td>
                                        <td height="5%">{{-- image 4 --}}</td>
                                    </tr>

                                    <tr>
                                        <td height="5%" class="center">Awareness</td>
                                        <td height="5%" class="center">Assistance</td>
                                        <td height="5%" class="center">Knowledge</td>
                                        <td height="5%" class="center">Expert</td>
                                    </tr>
                                </table>

                                {{-- PERFORMANCE LEVEL TABLE --}}
                                <table>
                                    <tr>
                                        <th height="5%" colspan="4" class="legend-header">
                                            PERFORMANCE LEVEL
                                        </th>
                                    </tr>

                                    <tr>
                                        <td height="10%">
                                            <div class="center">LEVEL 1</div><br>
                                            <div class="center"><span class="level-circle-small level1"></span></div><br>
                                            <div class="center">1–18 pts</div>
                                        </td>

                                        <td height="10%">
                                            <div class="center">LEVEL 2</div><br>
                                            <div class="center"><span class="level-circle-small level2"></span></div><br>
                                            <div class="center">19–36 pts</div>
                                        </td>

                                        <td height="10%">
                                            <div class="center">LEVEL 3</div><br>
                                            <div class="center"><span class="level-circle-small level3"></span></div><br>
                                            <div class="center">37–54 pts</div>
                                        </td>

                                        <td height="10%">
                                            <div class="center">LEVEL 4</div><br>
                                            <div class="center"><span class="level-circle-small level4"></span></div><br>
                                            <div class="center">55–72 pts</div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- APPROVAL -->
                        <tr>
                            <th colspan="2" class="legend-header">
                                APPROVED BY
                            </th>
                        </tr>

                        <tr>
                            <td width="50%" class="signature-box">
                                @if(!empty($approver1_signature))
                                {{-- <img src="{{ public_path($approver1_signature) }}" height="45"> --}}
                                    
                                @endif

                                <div class="signature-line">
                                    {{ 'Clark' }}
                                    <br>
                                    QC SR. MANAGER
                                </div>
                            </td>

                            <td width="50%" class="signature-box">
                                @if(!empty($approver2_signature))
                                    {{-- <img src="{{ public_path($approver2_signature) }}" height="45"> --}}
                                @endif

                                <div class="signature-line">
                                    {{ 'Miguel' }}
                                    <br>
                                    QMD GEN. MANAGER
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>