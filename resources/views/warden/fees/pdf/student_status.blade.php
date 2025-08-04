<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Fees Status Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .amount {
            text-align: right;
            font-family: monospace;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #333;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-label {
            font-weight: bold;
        }
        .summary-value {
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Fees Status Report</h1>
        <p>Generated on: {{ $generatedAt }}</p>
        <p>Hostel Management System</p>
    </div>

    <div class="summary">
        <h3>Report Summary</h3>
        <div class="summary-row">
            <span class="summary-label">Total Students:</span>
            <span class="summary-value">{{ $students->count() }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Fee Types:</span>
            <span class="summary-value">{{ count($feeTypes) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Report Date:</span>
            <span class="summary-value">{{ now()->format('d M Y') }}</span>
        </div>
    </div>

    <table>
        <thead>
                            <tr>
                    <th style="width: 20%;">Student Name</th>
                    <th style="width: 15%;">USN</th>
                    <th style="width: 25%;">Email</th>
                    <th style="width: 25%;">Parent Email</th>
                @foreach($feeTypes as $type)
                    <th style="width: 7%;">{{ ucwords(str_replace('_', ' ', $type)) }} Status</th>
                    <th style="width: 7%;">{{ ucwords(str_replace('_', ' ', $type)) }} Amount</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->usn ?? '-' }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->studentProfile->father_email ?? $student->parent_email ?? '-' }}</td>
                    @foreach($feeTypes as $type)
                        @php
                            $fee = $student->studentFees->where('fee_type', $type)->first();
                        @endphp
                        <td class="status-{{ $fee ? $fee->status : 'none' }}">
                            {{ $fee ? ucfirst($fee->status) : '-' }}
                        </td>
                        <td class="amount">
                            {{ $fee ? '₹' . number_format($fee->amount, 2) : '-' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the Hostel Management System.</p>
        <p>For any queries, please contact the hostel administration.</p>
    </div>
</body>
</html> 