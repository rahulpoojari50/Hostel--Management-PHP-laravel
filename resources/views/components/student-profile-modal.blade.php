<!-- Student Profile Modal -->
<!-- Modal Test: This comment confirms the modal component is loaded -->
<div class="modal fade" id="studentProfileModal" tabindex="-1" role="dialog" aria-labelledby="studentProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentProfileModalLabel">
                    <i class="fas fa-user"></i> Student Profile
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3" id="loadingSpinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading student information...</p>
                </div>
                
                <div id="studentProfileContent" style="display: none;">
                    <div class="row">
                        <!-- Student Photo -->
                        <div class="col-md-3 text-center">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px; border: 3px solid #e3e6f0; overflow: hidden; position: relative; box-shadow: 0 2px 8px rgba(0,0,0,0.07);">
                                <img id="studentPhoto" src="" alt="Student Photo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: none; image-rendering: auto; background: #f8f9fc;" onerror="this.style.display='none'; document.getElementById('photoIcon').style.display='flex';">
                                <span id="photoIcon" class="d-flex align-items-center justify-content-center w-100 h-100" style="position: absolute; top: 0; left: 0; display: none;">
                                    <i class="fas fa-user fa-3x text-muted"></i>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Student Information -->
                        <div class="col-md-9">
                            <h4 id="studentName" class="text-primary mb-3"></h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td id="studentEmail"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>USN:</strong></td>
                                            <td id="studentUsn"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td id="studentPhone"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td id="studentAddress"></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Hostel:</strong></td>
                                            <td id="studentHostel"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Room Number:</strong></td>
                                            <td id="studentRoom"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Room Type:</strong></td>
                                            <td id="studentRoomType"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Joining Date:</strong></td>
                                            <td id="studentJoiningDate"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Parent Information -->
                            <div class="mt-4">
                                <h5 class="text-secondary mb-3">
                                    <i class="fas fa-users"></i> Parent Information
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-info">Father's Details</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td id="fatherName"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td id="fatherEmail"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Mobile:</strong></td>
                                                <td id="fatherMobile"></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-info">Mother's Details</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td id="motherName"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td id="motherEmail"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Mobile:</strong></td>
                                                <td id="motherMobile"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a id="viewFullProfile" href="#" class="btn btn-primary" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Full Profile
                </a>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showStudentProfile(studentId) {
    console.log('showStudentProfile called with studentId:', studentId);
    
    // Show loading spinner
    $('#loadingSpinner').show();
    $('#studentProfileContent').hide();
    
    // Show modal
    $('#studentProfileModal').modal('show');
    
    console.log('Making AJAX request to:', `/warden/students/${studentId}/profile-data`);
    
    // Fetch student data via AJAX
    $.ajax({
        url: `/warden/students/${studentId}/profile-data`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            console.log('AJAX success, data received:', data);
            
            // Hide loading spinner
            $('#loadingSpinner').hide();
            $('#studentProfileContent').show();
            
                                    // Populate modal with student data
                        if (data.photo && data.photo !== '{{ asset("admin-assets/img/undraw_profile.svg") }}') {
                            $('#studentPhoto').attr('src', data.photo).show();
                            $('#photoIcon').hide();
                        } else {
                            $('#studentPhoto').hide();
                            $('#photoIcon').show();
                        }
            $('#studentName').text(data.name);
            $('#studentEmail').text(data.email);
            $('#studentUsn').text(data.usn);
            $('#studentPhone').text(data.phone);
            $('#studentAddress').text(data.address);
            $('#studentHostel').text(data.hostel_name);
            $('#studentRoom').text(data.room_number);
            $('#studentRoomType').text(data.room_type);
            $('#studentJoiningDate').text(data.joining_date);
            
            // Parent information
            $('#fatherName').text(data.father_name);
            $('#fatherEmail').text(data.father_email);
            $('#fatherMobile').text(data.father_mobile);
            $('#motherName').text(data.mother_name);
            $('#motherEmail').text(data.mother_email);
            $('#motherMobile').text(data.mother_mobile);
            
            // Set link to full profile
            $('#viewFullProfile').attr('href', `/warden/students/${studentId}`);
        },
        error: function(xhr, status, error) {
            console.log('AJAX error:', status, error);
            console.log('Response:', xhr.responseText);
            
            $('#loadingSpinner').hide();
            $('#studentProfileContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Error loading student information. Please try again.<br>
                    <small>Status: ${status}, Error: ${error}</small>
                </div>
            `);
            $('#studentProfileContent').show();
        }
    });
}

// Make student names clickable
$(document).ready(function() {
    console.log('Student profile modal script loaded');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Bootstrap modal available:', typeof $.fn.modal);
    
    // Add click event to student names
    $(document).on('click', '.student-name-clickable', function(e) {
        e.preventDefault();
        console.log('Student name clicked:', $(this).data('student-id'));
        const studentId = $(this).data('student-id');
        showStudentProfile(studentId);
    });
});
</script>
@endpush 

<style>
#studentProfileModal i.fas.fa-user.fa-3x.text-muted::before {
  content: "";
  display: none;
}
</style> 