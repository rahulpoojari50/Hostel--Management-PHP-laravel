@extends('layouts.admin')

@section('title', 'Student Fee Status')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <!-- Breadcrumb Navigation -->
        @include('components.breadcrumb-nav', ['breadcrumbs' => $breadcrumbs])
    </div>
    <div>
        {{-- Action buttons can go here --}}
    </div>
</div>

<!-- Page Title -->
<div class="mb-4">
    <h5 class="mb-0 text-gray-800">Student Fee Status</h5>
</div>

<!-- Student Search Filters -->
<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Student Search Filters</h5>
    </div>
    <form method="GET" action="" autocomplete="off">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="hostel_id">Select Hostel</label>
                <select class="form-control" id="hostel_id" name="hostel_id" onchange="this.form.submit()">
                    <option value="">-- Select Hostel --</option>
                    @foreach($hostels as $hostel)
                        <option value="{{ $hostel->id }}" {{ $selectedHostelId == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                    @endforeach
                </select>
            </div>
            @if($selectedHostel)
            <div class="col-md-4 mb-3">
                <label>Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, or USN" value="{{ request('search') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>Show</label>
                <select name="per_page" class="form-control">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
        @if($selectedHostel)
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
        @endif
    </form>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">All Students - Paid & Pending Fees</h6>
                <div class="btn-group" role="group">
                    <a href="{{ route('warden.fees.student_status.export.csv', request()->query()) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-csv"></i> CSV
                    </a>
                    <a href="{{ route('warden.fees.student_status.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('warden.fees.student_status.export.word', request()->query()) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-file-word"></i> Word
                    </a>
                </div>
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
                                    <th>USN</th>
                                    <th>Email</th>
                                    <th>Parent Email</th>
                                    @foreach($feeTypes as $type)
                                        <th>{{ ucwords(str_replace('_', ' ', $type)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td><input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox" onclick="event.stopPropagation();"></td>
                                    <td>
                                        <a href="#" class="student-name-clickable text-primary" data-student-id="{{ $student->id }}" style="text-decoration: none; cursor: pointer;">
                                            <i class="fas fa-user mr-1"></i>{{ $student->name }}
                                        </a>
                                    </td>
                                    <td>{{ $student->usn ?? '-' }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->studentProfile->father_email ?? $student->parent_email ?? '-' }}</td>
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

@include('components.student-profile-modal')


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