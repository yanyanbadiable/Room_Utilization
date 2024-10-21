<?php
include '../admin/db_connect.php';
$type = array("Admin", "Instructor", "Super Admin");
$users = $conn->query("SELECT * FROM users ORDER BY name ASC");
?>

<div class="container-fluid">
    <section class="content-header row d-flex align-items-center justify-content-between mb-3">
        <div class="col">
            <h3><i class="fa fa-user mr-2"></i>Manage Admin</h3>
        </div>
        <div class="col-auto">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Admin Management</li>
            </ol>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-default shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-primary">Admin List</h5>
                        <span>
                            <button class="btn btn-primary btn-md" id="new_user"><i class="fa fa-plus mr-2"></i> New Admin</button>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Username</th>
                                        <th>Type</th>
                                        <th>Program</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $user = $conn->query("SELECT users.*, program.program_name
                            FROM users INNER JOIN program ON users.program_id = program.id WHERE users.type = 0 ");
                                    if (!$user) {
                                        die('Invalid query: ' . $conn->error);
                                    }
                                    while ($row = $user->fetch_assoc()) :
                                    ?>
                                        <tr class="text-center">
                                            <td class="text-center"><?php echo $i++ ?></td>
                                            <td><?php echo $row['username'] ?></td>
                                            <td><?php echo $type[$row['type']] ?></td>
                                            <td><?php echo $row['program_name'] ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-primary edit_user mr-1" type="button" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Edit</button>
                                                <button class="btn btn-sm btn-danger delete_user" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        $('#new_user').click(function() {
            uni_modal('New Admin', 'manage_user.php');
        });

        $('.edit_user').click(function() {
            uni_modal('Edit Admin', 'manage_user.php?id=' + $(this).data('id'));
        });

        $('.delete_user').click(function() {
            _conf("Are you sure to delete this user?", "delete_user", [$(this).attr('data-id')]);
        });

    });

    function delete_user($id) {
        start_load();
        $.ajax({
            url: '../admin/ajax.php?action=delete_user',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 100);
                }
            }
        });
    }

    $('table').dataTable()
</script>