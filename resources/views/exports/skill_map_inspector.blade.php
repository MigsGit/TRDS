<table>
    <tr>
        <td colspan="10">
            <strong>Employee Skill Inspector</strong>
        </td>
    </tr>

    <tr>
        <th rowspan="2">NO.</th>
        <th rowspan="2">EMPLOYEE NAME</th>
        <th rowspan="2">EMP NO.</th>
        <th rowspan="2">DATE HIRED</th>

        <th colspan="6">
            {{ $productLine }}
        </th>
    </tr>

    <tr>
        <th>IQC</th>
        <th>IPQC</th>
        <th>OQC</th>
        <th>PACKING</th>
        <th>FUNCTION<br>TEST</th>
        <th>AUDITING /<br>MONITORING</th>
    </tr>

    @foreach($employees as $employee)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $employee['empName'] }}</td>
            <td>{{ $employee['empNo'] }}</td>
            <td>{{ $employee['dateHired'] }}</td>

            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach

    <tr>
        <td colspan="10">
            <strong>Legend:</strong>
        </td>
    </tr>

    <tr>
        <td></td>
        <td colspan="2">
            <strong>Level 1 -</strong><br>
            Certified (at least 1 series / products)
        </td>

        {{-- <td></td> --}}
        {{-- <td></td> --}}
        <td colspan="2">
            <strong>Level 2 -</strong><br>
            Certified (at least 2~3 series / products)
        </td>

        <td></td>
        <td colspan="2">
            <strong>Level 3 -</strong><br>
            Certified (at least 4~6 series / products)
        </td>
        <td></td>
        <td colspan="2">
            <strong>Level 4 -</strong><br>
            Certified (more than 6 series / products)
        </td>
    </tr>
</table>
