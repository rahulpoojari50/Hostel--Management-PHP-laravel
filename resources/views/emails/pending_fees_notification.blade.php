<p>Dear Parent,</p>
<p>This is to inform you that your child <strong>{{ $student->name }}</strong> has the following pending hostel fees:</p>
<ul>
    @foreach($pendingFees as $fee)
        <li><strong>{{ ucwords(str_replace('_', ' ', $fee->fee_type)) }}</strong>: ₹{{ number_format($fee->amount, 2) }}</li>
    @endforeach
</ul>
<p>Please ensure the fees are paid at the earliest to avoid any inconvenience.</p>
<p>Thank you,<br>Hostel Management</p> 