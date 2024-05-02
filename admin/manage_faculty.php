<?php include 'db_connect.php'; ?>
<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM faculty WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>


<div class="container-fluid">
    <form action="" id="manage-faculty">
        <div id="msg"></div>
        <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>" class="form-control">
        <div class="row form-group">
            <div class="col-md-4">
                <label class="control-label">ID No.</label>
                <input type="text" name="id_no" class="form-control" value="<?php echo isset($id_no) ? $id_no : ''; ?>">
                <small><i>Leave this blank if you want to auto-generate ID no.</i></small>
            </div>
            <div class="col-md-4">
                <label class="control-label">Designation</label>
                <select name="designation" class="form-control" required>
                    <?php
                    $enum_values = ["Head", "Coordinator", "Full Faculty"];
                    foreach ($enum_values as $value) {
                        echo '<option value="' . $value . '"' . (isset($designation) && $designation == $value ? ' selected' : '') . '>' . $value . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-4">
                <label class="control-label">Last Name</label>
                <input type="text" name="lastname" class="form-control" value="<?php echo isset($lastname) ? $lastname : ''; ?>" required>
                <div class="invalid-feedback">
                    Please enter the last name.
                </div>
            </div>
            <div class="col-md-4">
                <label class="control-label">First Name</label>
                <input type="text" name="firstname" class="form-control" value="<?php echo isset($firstname) ? $firstname : ''; ?>" required>
                <div class="invalid-feedback">
                    Please enter the first name.
                </div>
            </div>
            <div class="col-md-4">
                <label class="control-label">Middle Name</label>
                <input type="text" name="middlename" class="form-control" value="<?php echo isset($middlename) ? $middlename : ''; ?>">
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-4">
                <label class="control-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                <div class="invalid-feedback">
                    Please enter a valid email address.
                </div>
            </div>
            <div class="col-md-4">
                <label class="control-label">Contact #</label>
                <input type="text" name="contact" class="form-control" value="<?php echo isset($contact) ? $contact : ''; ?>" required pattern="09[0-9]{9}">
                <small><i>Must start with "09" and contain 11 digits.</i></small>
                <div class="invalid-feedback">
                    Please enter a valid contact number starting with "09" and containing 11 digits.
                </div>
            </div>
            <div class="col-md-4">
                <label class="control-label">Gender</label>
                <select name="gender" required class="form-control">
                    <option value="Male" <?php echo isset($gender) && $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo isset($gender) && $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                </select>
                <div class="invalid-feedback">
                    Please select a gender.
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-12">
                <label class="control-label">Address</label>
                <textarea name="address" class="form-control" required><?php echo isset($address) ? $address : ''; ?></textarea>
                <div class="invalid-feedback">
                    Please enter the address.
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $('#manage-faculty').submit(function(e) {
        e.preventDefault();
        var isValid = this.checkValidity();
        if (isValid) {
            start_load();
            $.ajax({
                url: 'ajax.php?action=save_faculty',
                method: 'POST',
                data: $(this).serialize(),
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast("Data successfully saved.", 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else if (resp == 2) {
                        $('#msg').html('<div class="alert alert-danger">ID No. already exists.</div>');
                        end_load();
                    }
                }
            });
        } else {
            // Show specific error messages
            $('#msg').html('<div class="alert alert-danger">Please fill in all required fields correctly.</div>');
            $('.form-control:invalid').each(function() {
                var feedback = $(this).siblings('.invalid-feedback');
                if (feedback.length > 0) {
                    feedback.show();
                }
            });
        }
    });
</script>