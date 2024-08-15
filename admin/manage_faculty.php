<?php
include 'db_connect.php';

$program_id = $_SESSION['login_program_id'];

$query = "SELECT id, program_name, department FROM program WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $program_id);
$stmt->execute();
$result = $stmt->get_result();

$program = $result->fetch_assoc();

$programs = [];
if ($program) {
    $programs[] = $program;
}

$query = "SELECT DISTINCT id, designation FROM unit_loads";
$result = mysqli_query($conn, $query);

if ($result) {
    $designations = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $designations[] = $row;
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

?>

<div class="container-fluid p-3">
    <section class="content-header row d-flex align-items-center justify-content-between mb-3">
        <div class="col">
            <h3><i class="fa fa-user-plus"></i> Add New Instructor</h3>
        </div>
        <div class="col-auto">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Add New Instructor</li>
            </ol>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <form class="form-horizontal needs-validation col-md-12" method="post" id="instructorForm" novalidate>
                <div class="card card-default shadow mb-4">
                    <div class="card-header">
                        <h3 class="card-title"><b>Personal Information</b></h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label><b>ID Number</b></label>
                                <input class="form-control" name="id_number" placeholder="ID Number*" value="<?php echo isset($instructor_id) ? $instructor_id : ''; ?>" type="text" required>
                                <div class="invalid-feedback">ID Number is required.</div>
                            </div>
                            <div class="col-md-9">
                                <label><b>Email</b></label>
                                <input class="form-control" name="email" placeholder="Email Address*" type="email" required>
                                <div class="invalid-feedback">Valid Email Address is required.</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label><b>Name</b></label>
                                <input class="form-control" name="fname" placeholder="First Name*" type="text" required>
                                <div class="invalid-feedback">First Name is required.</div>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <input class="form-control" name="middlename" placeholder="Middle Name" type="text">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <input class="form-control" name="lastname" placeholder="Last Name*" type="text" required>
                                <div class="invalid-feedback">Last Name is required.</div>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <input class="form-control" name="extensionname" placeholder="Extension Name" type="text">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label><b>Address</b></label>
                                <input class="form-control" name="street" placeholder="Street Address" type="text">
                            </div>
                            <div class="col-md-6">
                                <label>&nbsp;</label>
                                <input class="form-control" name="barangay" placeholder="Barangay*" type="text" required>
                                <div class="invalid-feedback">Barangay is required.</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <input class="form-control" name="municipality" placeholder="Municipality/City*" type="text" required>
                                <div class="invalid-feedback">Municipality/City is required.</div>
                            </div>
                            <div class="col-md-6">
                                <input class="form-control" name="province" placeholder="Province*" type="text" required>
                                <div class="invalid-feedback">Province is required.</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label><b>Sex</b></label>
                                <select class="form-control" name="gender" required>
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <div class="invalid-feedback">Gender is required.</div>
                            </div>

                            <div class="col-md-6">
                                <label><b>Contact Number</b></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+63</span>
                                    </div>
                                    <input class="form-control" name="contact" placeholder="Contact Number*" type="number" required pattern="^\+639\d{9}$">
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
                            <div class="col-md-6">
                                <label><b>Department</b></label>
                                <input type="text" class='form-control' name="program" value="<?php echo $program['department']; ?>" disabled>
                                <input type="hidden" name="program_id" value="<?php echo $program['id']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label><b>Employee Status</b></label>
                                <select name="designation" class="form-control" required>
                                    <option value="" disabled selected hidden>Select Employee Type</option>
                                    <?php foreach ($designations as $designation) : ?>
                                        <option value="<?php echo $designation['id']; ?>"><?php echo $designation['designation']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Employee Status is required.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <input type="submit" class="btn btn-primary btn-block" value="SAVE">
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {

        $('#instructorForm').submit(function(event) {
            event.preventDefault();

            if (this.checkValidity() === false) {
                $(this).addClass('was-validated');
                return;
            }

            $('input[type="submit"]').prop('disabled', true);
            addInstructor();
        });
    });

    function addInstructor() {
        var array = {
            id_number: $("input[name='id_number']").val(),
            firstname: $("input[name='fname']").val(),
            middlename: $("input[name='middlename']").val(),
            lastname: $("input[name='lastname']").val(),
            extensionname: $("input[name='extensionname']").val(),
            program_id: $("input[name='program_id']").val(),
            gender: $("select[name='gender']").val(),
            designation: $("select[name='designation']").val(),
            street: $("input[name='street']").val(),
            barangay: $("input[name='barangay']").val(),
            municipality: $("input[name='municipality']").val(),
            province: $("input[name='province']").val(),
            contact: $("input[name='contact']").val(),
            email: $("input[name='email']").val()
        };

        $.ajax({
            type: "POST",
            url: "ajax.php?action=save_faculty",
            data: array,
            success: function(data) {
                if (data.trim() === '1') {
                    alert_toast('Instructor Successfully Saved', 'success');
                    resetForm();
                } else {
                    alert_toast('Failed to save instructor', 'danger');
                    $('input[type="submit"]').prop('disabled', false);
                }
            },
            error: function() {
                alert_toast('Something Went Wrong!', 'danger');
                $('input[type="submit"]').prop('disabled', false);
            }
        });
    }

    function resetForm() {
        $('#instructorForm')[0].reset();
        $('#instructorForm').removeClass('was-validated');
        $('input[type="submit"]').prop('disabled', false);
    }
</script>