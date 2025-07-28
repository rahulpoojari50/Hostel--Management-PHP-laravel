@extends('layouts.admin')

@section('title', 'Student Profile & Parent Details')

@section('content')
<div class="container-fluid py-4">
    @include('components.breadcrumb', [
        'pageTitle' => 'Student Profile & Parent Details',
        'breadcrumbs' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Students', 'url' => route('warden.hostels.students', $assignment->room->hostel->id ?? 1)],
            ['name' => $student->name, 'url' => '']
        ]
    ])

    <!-- Student Basic Information -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user"></i> Student Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $student->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $student->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>USN:</strong></td>
                                    <td>{{ $student->usn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $student->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $student->address ?? '-' }}</td>
                                </tr>
                                @if($profile)
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td>{{ $profile->gender ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date of Birth:</strong></td>
                                    <td>{{ $profile->dob ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Blood Group:</strong></td>
                                    <td>{{ $profile->blood_group ?? '-' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if($assignment)
                                <tr>
                                    <td><strong>Hostel:</strong></td>
                                    <td>{{ $assignment->room->hostel->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Room Number:</strong></td>
                                    <td>{{ $assignment->room->room_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Floor:</strong></td>
                                    <td>{{ $assignment->room->floor ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Room Type:</strong></td>
                                    <td>{{ $assignment->room->roomType->type ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Assignment Date:</strong></td>
                                    <td>{{ $assignment->created_at->format('d M Y') ?? '-' }}</td>
                                </tr>
                                @else
                                <tr>
                                    <td><strong>Room Assignment:</strong></td>
                                    <td><span class="text-warning">Not assigned</span></td>
                                </tr>
                                @endif
                                @if($profile)
                                <tr>
                                    <td><strong>Admission Date:</strong></td>
                                    <td>{{ $profile->admission_date ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Aadhaar ID:</strong></td>
                                    <td>{{ $profile->aadhaar_id ?? '-' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parent Details Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users"></i> Parent Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Father's Information -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-male"></i> Father's Information
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $parentDetails['father_name'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Occupation:</strong></td>
                                    <td>{{ $parentDetails['father_occupation'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>
                                        @if($parentDetails['father_email'] != '-')
                                            <a href="mailto:{{ $parentDetails['father_email'] }}">{{ $parentDetails['father_email'] }}</a>
                                        @else
                                            {{ $parentDetails['father_email'] }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile:</strong></td>
                                    <td>
                                        @if($parentDetails['father_mobile'] != '-')
                                            <a href="tel:{{ $parentDetails['father_mobile'] }}">{{ $parentDetails['father_mobile'] }}</a>
                                        @else
                                            {{ $parentDetails['father_mobile'] }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Mother's Information -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-female"></i> Mother's Information
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $parentDetails['mother_name'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Occupation:</strong></td>
                                    <td>{{ $parentDetails['mother_occupation'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>
                                        @if($parentDetails['mother_email'] != '-')
                                            <a href="mailto:{{ $parentDetails['mother_email'] }}">{{ $parentDetails['mother_email'] }}</a>
                                        @else
                                            {{ $parentDetails['mother_email'] }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile:</strong></td>
                                    <td>
                                        @if($parentDetails['mother_mobile'] != '-')
                                            <a href="tel:{{ $parentDetails['mother_mobile'] }}">{{ $parentDetails['mother_mobile'] }}</a>
                                        @else
                                            {{ $parentDetails['mother_mobile'] }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Additional Contact Information -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="font-weight-bold text-primary mb-3">
                                <i class="fas fa-phone"></i> Additional Contact Information
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Primary Parent Mobile:</strong></td>
                                    <td>
                                        @if($parentDetails['parent_mobile'] != '-')
                                            <a href="tel:{{ $parentDetails['parent_mobile'] }}">{{ $parentDetails['parent_mobile'] }}</a>
                                        @else
                                            {{ $parentDetails['parent_mobile'] }}
                                        @endif
                                    </td>
                                    <td><strong>Primary Parent Email:</strong></td>
                                    <td>
                                        @if($parentDetails['parent_email'] != '-')
                                            <a href="mailto:{{ $parentDetails['parent_email'] }}">{{ $parentDetails['parent_email'] }}</a>
                                        @else
                                            {{ $parentDetails['parent_email'] }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Alternate Mobile:</strong></td>
                                    <td>
                                        @if($parentDetails['alternate_mobile'] != '-')
                                            <a href="tel:{{ $parentDetails['alternate_mobile'] }}">{{ $parentDetails['alternate_mobile'] }}</a>
                                        @else
                                            {{ $parentDetails['alternate_mobile'] }}
                                        @endif
                                    </td>
                                    <td><strong>Emergency Contact:</strong></td>
                                    <td>
                                        @if($profile && $profile->emergency_phone)
                                            <a href="tel:{{ $profile->emergency_phone }}">{{ $profile->emergency_phone }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Information -->
    @if($profile)
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map-marker-alt"></i> Address Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Present Address -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-success mb-3">Present Address</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>State:</strong></td>
                                    <td>{{ $profile->present_state ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>City:</strong></td>
                                    <td>{{ $profile->present_city ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $profile->present_address ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Permanent Address -->
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-info mb-3">Permanent Address</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>State:</strong></td>
                                    <td>{{ $profile->permanent_state ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>City:</strong></td>
                                    <td>{{ $profile->permanent_city ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $profile->permanent_address ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Additional Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Religion:</strong></td>
                                    <td>{{ $profile->religion ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Caste Category:</strong></td>
                                    <td>{{ $profile->caste_category ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Caste:</strong></td>
                                    <td>{{ $profile->caste ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Admission Quota:</strong></td>
                                    <td>{{ $profile->admission_quota ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mother Tongue:</strong></td>
                                    <td>{{ $profile->mother_tongue ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nationality:</strong></td>
                                    <td>{{ $profile->nationality ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Marital Status:</strong></td>
                                    <td>{{ $profile->marital_status ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Passport No:</strong></td>
                                    <td>{{ $profile->passport_no ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Document:</strong></td>
                                    <td>
                                        @if($profile->document_path)
                                            <a href="{{ asset('storage/' . $profile->document_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i> View Document
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-body">
                    <a href="{{ route('warden.students.edit', $student->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Student
                    </a>
                    @if($assignment)
                        <a href="{{ route('warden.hostels.students', $assignment->room->hostel->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Students List
                        </a>
                    @else
                        <a href="{{ route('warden.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 