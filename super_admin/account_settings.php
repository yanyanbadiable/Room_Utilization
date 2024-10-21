
<section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
    <h2><i class="fa fa-key"></i> Account Setting</h2>
    <ol class="breadcrumb bg-transparent p-0 m-0">
        <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
        <li class="breadcrumb-item active">Account Setting</li>
    </ol>
</section> <br>
<section class="content ">
    <div class="row ">
        <div class='col-sm-6'>
            <div class="card card-default shadow mb-4">
                <div class="card-header bg-secondary">
                    <h3 class="card-title mb-0 text-white">Change Account Settings</h3>
                </div>
                <div class="card-body">
                    <form id="change_password" method='post'>
                        <input type='hidden' value='<?php echo $_SESSION['login_id']; ?>' name='idno'>
                        <br>
                        <div class='form-group'>
                            <div class="input-group">
                                <span class="input-group-prepend">
                                    <span class="input-group-text m-0"><i class="fa fa-user "></i></span>
                                </span>
                                <input type="text" class="form-control" name='username' placeholder="Username">
                            </div>
                        </div>
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
                            <button type="button" onclick="changePassword()" class="btn btn-primary btn-block">Update Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function _reset() {
        $('#change_password').get(0).reset();
        var username = $('[name="username"]').val();
        var password = $('[name="password"]').val();
        var password_confirmation = $('[name="password_confirmation"]').val();
        $('#change_password input, #change_password textarea').val('');
    }

    function changePassword() {
        var formData = new FormData(document.getElementById('change_password'));
        $.ajax({
            type: "POST",
            url: "../admin/ajax.php?action=account_setting",
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.trim() === 'Account updated successfully.') {
                    alert_toast('Account updated successfully!', 'success');
                    _reset();
                } else if (data.trim() === 'No changes made.') {
                    alert_toast('No updates were made.', 'info');
                    _reset();
                } else {
                    alert_toast(data.trim(), 'danger');
                    _reset();
                }
            },
            error: function() {
                alert_toast('Something went wrong!', 'danger');
            }
        });
    }
</script>