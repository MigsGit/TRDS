<table>
    <!-- Title Section -->
    <tr>
        <td colspan="13" style="font-size: 14pt; font-weight: bold;">LIST OF CERTIFIED PERSONNEL</td>
    </tr>
    <tr></tr>
    {{-- <tr>
        <td style="font-style: italic;">updated as of</td>
        <td></td>
        <td style="font-weight: bold; text-align: center; text-decoration: underline;">{{ $updated_as }}</td>
        <td style="font-weight: bold;">Revision No.</td>
        <td style="font-weight: bold; text-align: center;">{{ $revision_no }}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td style="font-weight: bold;">Frequency of Updating:</td>
        <td style="font-weight: bold; text-align: center;">{{ $frequency }}</td>
    </tr>
    <tr></tr> --}}

    <!-- Table Header (2 Rows) -->
    <thead>
        <tr>
            <!-- Left Header Columns (Peach/Orange #FDB95B Fill, Merged Vertically) -->
            <th rowspan="2" style="background-color: #FDB95B; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Employee No.</th>
            <th rowspan="2" style="background-color: #FDB95B; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Employee Name</th>
            <th rowspan="2" style="background-color: #FDB95B; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Product Line</th>
            <th rowspan="2" style="background-color: #FDB95B; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Station/s</th>
            <th rowspan="2" style="background-color: #FDB95B; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Category</th>
            <th rowspan="2" style="background-color: #FDB95B; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Date Hired</th>
            
            <!-- PRODUCTION Header (Blue #00A3E0) -->
            <th colspan="2" style="background-color: #00A3E0; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">PRODUCTION</th>
            
            <!-- ENGINEERING Header (Red #FF0000) -->
            <th colspan="2" style="background-color: #FF0000; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">ENGINEERING</th>
            
            <!-- LINE QC Header (Yellow #FFFF00) -->
            <th colspan="2" style="background-color: #FFFF00; color: #000000; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">LINE QC</th>
            
            <!-- Remarks Column (Peach/Orange #FDB95B) -->
            <th rowspan="2" style="background-color: #FDB95B; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Remarks</th>
        </tr>
        <tr>
            <!-- Sub-headers Row 2 -->
            <th style="background-color: #00A3E0; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Trained by<br>(MH and/or Supervisor)</th>
            <th style="background-color: #00A3E0; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Date Trained</th>
            
            <th style="background-color: #FF0000; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Qualified by<br>(Technician and/or Process Engineer)</th>
            <th style="background-color: #FF0000; color: #FFFFFF; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Date Qualified</th>
            
            <th style="background-color: #FFFF00; color: #000000; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Certified by<br>(QC Inspector and/or Supervisor)</th>
            <th style="background-color: #FFFF00; color: #000000; font-weight: bold; text-align: center; vertical-align: center; border: 1px solid #000000;">Date Certified</th>
        </tr>
    </thead>

    <!-- Data Rows -->
    <tbody>
        @foreach($rows as $row)
            <tr>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['emp_no'] }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['emp_name'] }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['product_line'] }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['station'] }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['category'] }}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['date_hired'] }}</td>
                <td style="text-align: center; border: 1px solid #000000; white-space: pre-wrap; word-wrap: break-word;">{!! nl2br(e($row['prod_name'])) !!}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['prod_date'] }}</td>
                <td style="text-align: center; border: 1px solid #000000; white-space: pre-wrap; word-wrap: break-word;">{!! nl2br(e($row['eng_name'])) !!}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['eng_date'] }}</td>
                <td style="text-align: center; border: 1px solid #000000; white-space: pre-wrap; word-wrap: break-word;">{!! nl2br(e($row['qc_name'])) !!}</td>
                <td style="text-align: center; border: 1px solid #000000;">{{ $row['qc_date'] }}</td>
                @if ($row['remarks'] === 'PASSED')
                    <td style="text-align: center; font-weight: bold; color: #006100; background-color: #C6EFCE; border: 1px solid #000000;">{{ $row['remarks'] }}</td>
                @else
                    <td style="text-align: center; font-weight: bold; color: #9C0006; background-color: #FFC7CE; border: 1px solid #000000;">{{ $row['remarks'] }}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>