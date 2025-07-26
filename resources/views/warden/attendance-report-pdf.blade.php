<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Attendance Report</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Room Number</th>
                <th>Email</th>
                <th>Hostel Name</th>
                @foreach($dates as $date)
                    <th>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</th>
                @endforeach
                <th>Total Present</th>
                <th>Total Absent</th>
                <th>Total On Leave</th>
                <th>Total Holiday</th>
                <th>Attendance %</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                @php
                    $present = 0;
                    $absent = 0;
                    $onLeave = 0;
                    $holiday = 0;
                    $badge = 'danger';
                @endphp
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>{{ optional($student->roomAssignments->first()->room ?? null)->room_number }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ optional($student->roomAssignments->first()->room->hostel ?? null)->name }}</td>
                    @foreach($dates as $date)
                        @php $status = $attendanceData[$student->id][$date] ?? null; @endphp
                        <td>
                            @if($status === 'Taken')
                                @php $present++; @endphp
                                Present
                            @elseif($status === 'Skipped')
                                @php $absent++; @endphp
                                Absent
                            @elseif($status === 'On Leave')
                                @php $onLeave++; @endphp
                                On Leave
                            @elseif($status === 'Holiday')
                                @php $holiday++; @endphp
                                Holiday
                            @elseif($status)
                                {{ $status }}
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    @php
                        $total = count($dates);
                        $percent = $total ? round(($present / $total) * 100) : 0;
                        if($percent >= 90) {
                            $statusText = 'Excellent'; $badge = 'success';
                        } elseif($percent >= 75) {
                            $statusText = 'Good'; $badge = 'warning';
                        } else {
                            $statusText = 'Poor'; $badge = 'danger';
                        }
                    @endphp
                    <td>{{ $present }}</td>
                    <td>{{ $absent }}</td>
                    <td>{{ $onLeave }}</td>
                    <td>{{ $holiday }}</td>
                    <td>{{ $percent }}%</td>
                    <td>{{ $statusText }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 