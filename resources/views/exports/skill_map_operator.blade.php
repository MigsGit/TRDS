<table>
    <!-- Title -->
    <tr>
        <td colspan="8">
            <strong>Employee Skill Matrix</strong>
        </td>
    </tr>

    <!-- Header -->
    <tr>
        <th rowspan="2">NO.</th>
        <th rowspan="2">EMPLOYEE NAME</th>
        <th rowspan="2">EMP NO.</th>
        <th rowspan="2">DATE HIRED</th>

        <th colspan="4">
            {{ $productLine }}
        </th>
    </tr>

    <!-- Skill Header -->
    <tr>
        <th>PARTS<br>PREP.</th>
        <th>VISUAL<br>INSPECTION</th>
        <th>ASSEMBLY<br>PROCESS</th>
        <th>MACHINE<br>OPERATION</th>
    </tr>

    <!-- Employees -->
    @foreach($employees as $employee)

        <tr>
            <td>{{ $loop->iteration }}</td>

            <td>{{ $employee['empName'] }}</td>

            <td>{{ $employee['empNo'] }}</td>

            <td>{{ $employee['dateHired'] }}</td>

            {{-- Images will be inserted here by drawings() --}}
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

    @endforeach

 <!-- Legend Title -->
<tr>
    <td colspan="8">
        <strong>Legend:</strong>
    </td>
</tr>

<!-- Legend -->
<tr>

    <!-- LEVEL 1 -->
    <td align="center"></td>
    <td>
        <strong>Level 1 -</strong><br>
        Certified (at least 1 series / products)
    </td>

    <!-- LEVEL 2 -->
    <td align="center"></td>
    <td>
        <strong>Level 2 -</strong><br>
        Certified (at least 2~3 series / products)
    </td>

    <!-- LEVEL 3 -->
    <td align="center"></td>
    <td>
        <strong>Level 3 -</strong><br>
        Certified (at least 4~6 series / products)
    </td>

    <!-- LEVEL 4 -->
    <td align="center"></td>
    <td>
        <strong>Level 4 -</strong><br>
        Certified (more than 6 series / products)
    </td>

</tr>
</table>
