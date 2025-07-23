@extends('layouts.admin')

@section('title', 'Apply for Hostel')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Apply for {{ $hostel->name }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.applications.store', $hostel->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="room_type_id">Select Room Type</label>
                            <select class="form-control" id="room_type_id" name="room_type_id" required onchange="updatePrice()">
                                <option value="">-- Select Room Type --</option>
                                @foreach($hostel->roomTypes as $type)
                                    <option value="{{ $type->id }}" data-price="{{ $type->price_per_month }}">
                                        {{ $type->type }} (₹{{ $type->price_per_month }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="fee-breakdown" class="mb-2" style="display:none;"></div>
                        <div id="fee-checkboxes" class="mb-2" style="display:none;"></div>
                        <div class="form-group">
                            <label for="amount">Total Price / Rent</label>
                            <input type="text" class="form-control" id="amount" name="amount" readonly required>
                        </div>
                        <input type="hidden" name="fees_paid_now" id="fees_paid_now">
                        <input type="hidden" name="fees_pay_later" id="fees_pay_later">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-credit-card"></i> Pay & Apply
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function updatePrice() {
    var sel = document.getElementById('room_type_id');
    var price = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].getAttribute('data-price') : '';
    var fees = @json($hostel->fees ?? []);
    var feeSum = 0;
    var breakdown = '';
    var feeCheckboxes = '';
    if (sel.value) {
        // Room Rent checkbox
        feeCheckboxes += '<div class="form-check"><input class="form-check-input fee-check" type="checkbox" checked id="feeCheckRoomRent" data-amount="' + price + '" data-type="room_rent"><label class="form-check-label" for="feeCheckRoomRent">Room Rent (₹' + (price || 0) + ')</label></div>';
    }
    if (fees.length > 0) {
        breakdown += '<strong>Hostel Fees:</strong><ul style="margin-bottom:0;">';
        fees.forEach(function(fee, idx) {
            breakdown += '<li>' + (fee.type ? fee.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Fee') + ': ₹' + fee.amount + '</li>';
            feeCheckboxes += '<div class="form-check"><input class="form-check-input fee-check" type="checkbox" checked id="feeCheck' + idx + '" data-amount="' + fee.amount + '" data-type="' + fee.type + '"><label class="form-check-label" for="feeCheck' + idx + '">' + (fee.type ? fee.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Fee') + ' (₹' + fee.amount + ')</label></div>';
        });
        breakdown += '</ul>';
    }
    var total = 0;
    document.getElementById('amount').value = '';
    var feeBreakdownDiv = document.getElementById('fee-breakdown');
    var feeCheckboxesDiv = document.getElementById('fee-checkboxes');
    if (sel.value && (price || fees.length > 0)) {
        feeBreakdownDiv.style.display = 'block';
        feeBreakdownDiv.innerHTML = '<strong>Room Rent:</strong> ₹' + (price || 0) + '<br>' + (breakdown ? breakdown : '');
        feeCheckboxesDiv.style.display = 'block';
        feeCheckboxesDiv.innerHTML = feeCheckboxes;
    } else {
        feeBreakdownDiv.style.display = 'none';
        feeBreakdownDiv.innerHTML = '';
        feeCheckboxesDiv.style.display = 'none';
        feeCheckboxesDiv.innerHTML = '';
    }
    updateTotal();
    // Add event listeners to checkboxes
    document.querySelectorAll('.fee-check').forEach(function(cb) {
        cb.addEventListener('change', updateTotal);
    });
}
function updateTotal() {
    var total = 0;
    var paidNow = [];
    var payLater = [];
    document.querySelectorAll('.fee-check').forEach(function(cb) {
        var fee = { type: cb.getAttribute('data-type'), amount: parseFloat(cb.getAttribute('data-amount')) };
        if (cb.checked) {
            total += fee.amount;
            paidNow.push(fee);
        } else {
            payLater.push(fee);
        }
    });
    document.getElementById('amount').value = total ? total.toFixed(2) : '';
    document.getElementById('fees_paid_now').value = JSON.stringify(paidNow);
    document.getElementById('fees_pay_later').value = JSON.stringify(payLater);
}
// Initialize on page load if a room type is preselected
if(document.getElementById('room_type_id').value) updatePrice();
</script>
@endsection 