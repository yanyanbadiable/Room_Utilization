<?php
include 'db_connect.php';

$query = "SELECT DISTINCT id, program_name, program_code FROM program";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result) {
    // Fetch all rows from the result set
    $programs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[] = $row;
    }
} else {
    // Handle query error
    echo "Error: " . mysqli_error($conn);
}

?>

<div class="container-fluid">
    <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
        <h3><i class="fa fa-user-plus"></i> Add New Instructor</h3>
        <ol class="breadcrumb bg-transparent p-0 m-0">
            <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
            <li class="breadcrumb-item active">Add New Instructor</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <form class="form-horizontal needs-validation" method='post' id="instructorForm" novalidate>
                <div class="col-md-12">
                    <div class="card card-default shadow mb-4">
                        <div class="card-header">
                            <h3 class="card-title"><b>Personal Information</b></h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label><b>ID Number</b></label>
                                    <input class="form-control" name="username" placeholder="ID Number*" value="<?php echo isset($instructor_id) ? $instructor_id : ''; ?>" type="text" required>
                                    <div class="invalid-feedback">ID Number is required.</div>
                                </div>
                                <div class="col-sm-9">
                                    <label><b>Email</b></label>
                                    <input class="form-control" name='email' placeholder='Email Address*' type="email" required>
                                    <div class="invalid-feedback">Valid Email Address is required.</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label><b>First Name</b></label>
                                    <input class="form-control" name='fname' placeholder='First Name*' type="text" required>
                                    <div class="invalid-feedback">First Name is required.</div>
                                </div>
                                <div class="col-sm-3">
                                    <label>&nbsp;</label>
                                    <input class="form-control" name='middlename' placeholder='Middle Name' type="text">
                                </div>
                                <div class="col-sm-3">
                                    <label>&nbsp;</label>
                                    <input class="form-control" name='lastname' placeholder='Last Name*' type="text" required>
                                    <div class="invalid-feedback">Last Name is required.</div>
                                </div>
                                <div class="col-sm-3">
                                    <label>&nbsp;</label>
                                    <input class="form-control" name='extensionname' placeholder='Extension Name' type="text">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label><b>Address</b></label>
                                    <input class="form-control" name='street' placeholder='Street Address' type="text">
                                </div>
                                <div class="col-sm-6">
                                    <label>&nbsp;</label>
                                    <input class="form-control" name='barangay' placeholder='Barangay*' type="text" required>
                                    <div class="invalid-feedback">Barangay is required.</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <input class="form-control" name='municipality' placeholder='Municipality/City*' type="text" required>
                                    <div class="invalid-feedback">Municipality/City is required.</div>
                                </div>
                                <div class="col-sm-6">
                                    <input class="form-control" name='province' placeholder='Province*' type="text" required>
                                    <div class="invalid-feedback">Province is required.</div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label><b>Gender</b></label>
                                    <select class="form-control" name='gender' required>
                                        <option value=''>Select Gender</option>
                                        <option value='Male'>Male</option>
                                        <option value='Female'>Female</option>
                                    </select>
                                    <div class="invalid-feedback">Gender is required.</div>
                                </div>

                                <div class="col-sm-6">
                                    <label><b>Contact Number</b></label>
                                    <div class="input-group">
                                        <input class="form-control" name='contact' placeholder='+63' type="text" required pattern="^\+639\d{9}$">
                                        <div class="invalid-feedback">Valid Contact Number is required (12 digits starting with +639).</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h3 class="card-title"><b>Other Information</b></h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label><b>Department</b></label>
                                    <select name="program" class="form-control" required>
                                        <option value="">Select Department</option>
                                        <?php foreach ($programs as $program) : ?>
                                            <option value="<?php echo $program['id']; ?>"><?php echo $program['program_code']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Department is required.</div>
                                </div>
                                <div class="col-sm-6">
                                    <label><b>Employee Status</b></label>
                                    <select name="designation" class="form-control" required>
                                        <option value="">Select Employee Type</option>
                                        <option value="Full Time">Full Time</option>
                                        <option value="Part Time">Part Time</option>
                                    </select>
                                    <div class="invalid-feedback">Employee Status is required.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h3 class="card-title"><b>Account Information</b></h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label><b>Password</b></label>
                                    <input type="password" class="form-control" placeholder="Password*" name="password" id="password" required>
                                    <div class="invalid-feedback">Password is required.</div>
                                </div>
                                <div class="col-sm-6">
                                    <label><b>Confirm Password</b></label>
                                    <input type="password" class="form-control" placeholder="Confirm Password*" name="confirm_password" id="confirm_password" required>
                                    <div class="invalid-feedback">Please confirm your password.</div>
                                    <div id="passwordError" class="invalid-feedback" style="display: none;">Passwords do not match.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class='form-group row'>
                        <div class='col-sm-12'>
                            <input type='submit' class='col-sm-12 btn btn-primary' value='SAVE'>
                        </div>
                    </div>
            </form>
        </div>
    </section>
</div>
<script>
    var formIsValid = true; // Flag to track form validity

    document.getElementById("instructorForm").onsubmit = function(event) {
        var form = document.getElementById("instructorForm");
        var password = document.getElementById("password").value;
        var confirm_password = document.getElementById("confirm_password").value;
        var passwordError = document.getElementById("passwordError");

        if (password !== confirm_password) {
            passwordError.style.display = "block"; 
            formIsValid = false; 
            return false;
        } else {
            passwordError.style.display = "none"; 
            formIsValid = true; 
        }

        if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            formIsValid = false; 
            formIsValid = true; 
        }
        form.classList.add('was-validated');
    };

    $('#instructorForm').submit(function(event) {
        // Prevent default form submission behavior
        event.preventDefault();

        if (!formIsValid) {
            return; 
        }

        $('input[type="submit"]').prop('disabled', true);

        addUser();
        addInstructor();
    });

    function addUser() {
        if (!formIsValid) {
            return; 
        }
        var array = {};
        array['username'] = $("input[name='username']").val();
        array['firstname'] = $("input[name='fname']").val();
        array['middlename'] = $("input[name='middlename']").val();
        array['lastname'] = $("input[name='lastname']").val();
        array['extensionname'] = $("input[name='extensionname']").val();
        array['password'] = $("input[name='password']").val();
        array['program_id'] = $("select[name='program']").val();
        $.ajax({
            type: "POST",
            url: "ajax.php?action=save_user",
            data: array,
            success: function(data) {
                if (data.trim() === '1') {
                    alert_toast('User Successfully Saved', 'success');
                    resetForm(); 
                    reloadPage(); 
                } else {
                    alert_toast('Failed to add user', 'danger');
                    resetForm(); 
                    reloadPage(); 
                }
            },
            error: function() {
                alert_toast('Something Went Wrong!', 'danger');
                resetForm(); 
                reloadPage(); 
            }
        });
    }

    function addInstructor() {
        if (!formIsValid) {
            return; // Do not proceed if form is not valid
        }
        var array = {};
        array['program_id'] = $("select[name='program']").val();
        array['gender'] = $("select[name='gender']").val();
        array['designation'] = $("select[name='designation']").val();
        array['street'] = $("input[name='street']").val();
        array['barangay'] = $("input[name='barangay']").val();
        array['municipality'] = $("input[name='municipality']").val();
        array['province'] = $("input[name='province']").val();
        array['contact'] = $("input[name='contact']").val();
        array['email'] = $("input[name='email']").val();

        $.ajax({
            type: "POST",
            url: "ajax.php?action=save_faculty",
            data: array,
            success: function(data) {
                if (data.trim() === '1') {
                    alert_toast('Instructor Successfully Saved', 'success');
                    resetForm(); // Reset the form
                    reloadPage(); // Call reloadPage() instead of location.reload()
                } else {
                    alert_toast('Failed to save instructor', 'danger');
                    resetForm(); // Reset the form
                    reloadPage(); // Call reloadPage() instead of location.reload()
                }
            },
            error: function() {
                alert_toast('Something Went Wrong!', 'danger');
                resetForm(); // Reset the form
                reloadPage(); // Call reloadPage() instead of location.reload()
            }
        });
    }

    function resetForm() {
        document.getElementById("instructorForm").reset(); // Reset the form
        $('input[type="submit"]').prop('disabled', false); // Enable the submit button
    }
</script>