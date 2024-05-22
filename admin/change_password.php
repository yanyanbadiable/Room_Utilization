<section class="content-header mb-4">
    <h1><i class="fa fa-key"></i> Account</h1>
</section>

<section class="content ">
    <div class='col-sm-4'>
        <div class="card card-default shadow mb-4">
            <div class="card-header">
                <h3 class="card-title">Change Password</h3>
            </div>
            <div class="card-body">
                <form id="change_password" method='post'>
                    <input type='hidden' value='<?php echo $_SESSION['login_id']; ?>' name='idno'>
                    <br>
                    <div class='form-group'>
                        <div class="input-group">
                            <span class="input-group-prepend">
                                <span class="input-group-text m-0"><i class="fa fa-lock "></i></span>
                            </span>
                            <input type="password" class="form-control" name='password' placeholder="Password">
                        </div>
                    </div>
                    <div class='form-group mb-4'>
                        <div class="input-group">
                            <span class="input-group-prepend ">
                                <span class="input-group-text m-0"><i class="fas fa-sync-alt"></i></span>
                            </span>
                            <input type="password" class="form-control" name='password_confirmation' placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class='form-group'>
                        <button type="button" onclick="changePassword()" class="btn btn-primary btn-block">Change</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>
<script>
    function changePassword() {
        var formData = new FormData(document.getElementById('change_password'));

        $.ajax({
            type: "POST",
            url: "ajax.php?action=change_password",
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.trim() === 'Password updated successfully!') {
                    alert_toast('Password changed successfully!', 'success');
                } else {
                    alert_toast('Failed to change password', 'danger');
                }
            },
            error: function() {
                alert_toast('Something went wrong!', 'danger');
            }
        });
    }
</script>