<table>

    <!-- Header -->
    <tr>
        <th>No.</th>
        <th>Emp. Name</th>
        <th>Emp. No.</th>
        <th>Date Hired</th>

        @foreach($productLines as $productLine)
            <th>{{ $productLine }}</th>
        @endforeach
    </tr>

    <!-- Employees -->
    @foreach($employees as $employee)

        <tr>
            <td>{{ $loop->iteration }}</td>

            <td>{{ $employee['empName'] }}</td>

            <td>{{ $employee['empNo'] }}</td>

            <td>{{ $employee['dateHired'] }}</td>

           @foreach($productLines as $productLineIndex => $productLine)
                {{-- <td>qweqweqwe</td> --}}
                {{-- {{ $productLineIndex }} --}}
            @endforeach

        </tr>

    @endforeach

    <!-- Legend Title -->
    <tr>
        <td colspan="{{ 4 + count($productLines) }}">
            <strong>Legend:</strong>
        </td>
    </tr>

    <!-- Legend -->
    <tr>
        <td></td>
        <td>
            <strong>Level 1 -</strong><br>
            Awareness on the Process
        </td>

        <td></td>
        <td>
            <strong>Level 2 -</strong><br>
            Knowledgeable on the Process
        </td>

        <td></td>
        <td>
            <strong>Level 3 -</strong><br>
            Can perform with less supervision
        </td>

        <td></td>
        <td>
            <strong>Level 4 -</strong><br>
            Can mentor co-employee
        </td>
    </tr>

</table>
