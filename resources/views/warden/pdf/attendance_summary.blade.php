<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Summary Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h2 { text-align: center; margin-bottom: 0; }
        h4 { text-align: center; margin-top: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .summary-table { margin: 0 auto; width: 70%; }
    </style>
</head>
<body>
    <h2>Attendance Summary Report</h2>
    <h4>{{ $hostel->name }}<br>Date Range: {{ $startDate }} to {{ $endDate }}</h4>
    <table class="summary-table">
        <tbody>
        @foreach($summary as $row)
            <tr>
                <th>{{ $row[0] }}</th>
                <td>{{ $row[1] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html> 