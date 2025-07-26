<html>
<head>
    <title>Meal Attendance Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #f2f2f2; }
        .header { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Meal Attendance Report</h2>
        <p><strong>Hostel:</strong> {{ $hostel->name }}</p>
        <p><strong>Period:</strong> {{ $dateFrom }} to {{ $dateTo }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Room</th>
                <th>Hostel</th>
                <th>Date</th>
                @foreach($mealTypes as $mealType)
                    <th>{{ $mealType }}</th>
                @endforeach
                <th>Summary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tableRows as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['email'] }}</td>
                    <td>{{ $row['room'] }}</td>
                    <td>{{ $row['hostel'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    @foreach($mealTypes as $mealType)
                        <td>{{ $row[strtolower($mealType)] }}</td>
                    @endforeach
                    <td>{{ $row['summary'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 