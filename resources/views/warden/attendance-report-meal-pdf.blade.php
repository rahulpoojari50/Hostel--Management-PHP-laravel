@php
$mealTypes = ['Breakfast', 'Lunch', 'Snacks', 'Dinner'];
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Meal Attendance Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 4px; text-align: center; }
        th { background: #e3e3e3; }
    </style>
</head>
<body>
<h2>Meal Attendance Report</h2>
<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Room</th>
            <th>Hostel</th>
            @foreach($dates as $date)
                <th>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</th>
            @endforeach
            <th>Report</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tableRows as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html> 