@extends('layouts.admin')

@section('title', 'Student Fee Status')

@section('content')
@include('components.breadcrumb', [
    'pageTitle' => 'Student Fee Status',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Fees', 'url' => route('warden.fees.index')],
        ['name' => 'Student Status', 'url' => '']
    ]
])
<form method="GET" action="" class="mb-3 d-flex align-items-center flex-wrap" style="max-width: 600px;">
    <input type="text" name="search" class="form-control mr-2" placeholder="Search student by name or email" value="{{ request('search') }}" style="max-width:200px;">
    <button type="submit" class="btn btn-primary mr-2">Search</button>
    <div class="d-flex align-items-center" style="white-space:nowrap;">
        <label class="mb-0 mr-2">Show</label>
        <select name="per_page" class="form-control mr-2" style="width:auto;display:inline-block;min-width:60px;">
            @foreach([10, 25, 50, 100] as $size)
                <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
            @endforeach
        </select>
        <span class="ml-1"></span>
    </div>
</form>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Students - Paid & Pending Fees</h6>
            </div>
            <div class="card-body">
                <form id="notify-form" method="POST" action="{{ route('warden.warden.fees.notify-parents') }}">
                    @csrf
                    <div class="mb-2">
                        <button type="submit" class="btn btn-warning btn-sm" id="notify-selected-btn">Notify Selected</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Parent Email</th>
                                    <th>Hostel Name</th>
                                    @foreach($feeTypes as $type)
                                        <th>{{ ucwords(str_replace('_', ' ', $type)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox" onclick="event.stopPropagation();"></td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->parent_email ?? '-' }}</td>
                                    <td>
                                        @php
                                            $assignment = $student->roomAssignments->where('status', 'active')->first();
                                            $hostelName = $assignment && $assignment->room && $assignment->room->hostel ? $assignment->room->hostel->name : '-';
                                        @endphp
                                        {{ $hostelName }}
                                    </td>
                                    @foreach($feeTypes as $type)
                                        @php
                                            $fee = $student->studentFees->where('fee_type', $type)->first();
                                        @endphp
                                        <td>
                                            @if($fee)
                                                <span class="badge badge-{{ $fee->status == 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($fee->status) }}
                                                </span>
                                                <br>
                                                <small>₹{{ number_format($fee->amount, 2) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
                <div class="d-flex justify-content-center mt-3">
                    {{ $students->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
    .pagination .page-link {
        font-size: 1.25rem;
        padding: 0.5rem 1rem;
    }
    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
        color: #fff;
    }
    .pagination .page-link svg {
        width: 1.5em;
        height: 1.5em;
        vertical-align: middle;
    }
</style>
@endpush
@push('scripts')
<script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = checked);
    });
</script>
@endpush 