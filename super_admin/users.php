<?php
include 'db_connect.php';
$type = array("Super_admin", "Admin", "Staff");
$users = $conn->query("SELECT * FROM users ORDER BY name ASC");
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <b>User List</b>
                    <span class="float-right">
                        <button class="btn btn-primary btn-sm" id="new_user"><i class="fa fa-plus"></i> New user</button>
                    </span>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Username</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Department</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $user = $conn->query("SELECT users.*, department.department_code
                            FROM users INNER JOIN department ON users.department_id = department.id;");
                            if (!$user) {
                                die('Invalid query: ' . $conn->error);
                            }
                            while ($row = $user->fetch_assoc()) :
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td><?php echo ucwords($row['name']) ?></td>
                                    <td><?php echo $row['username'] ?></td>
                                    <td><?php echo $type[$row['type']] ?></td>
									<td><?php echo $row['department_code'] ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item edit_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Edit</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item delete_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Delete</a>
                                            </div>
                                        </div>
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

<script>

function delete_user($id) {
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_user',
        method: 'POST',
        data: {
            id: $id
        },
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Data successfully deleted", 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        }
    });
}
    
    $(document).ready(function() {
        $('#new_user').click(function() {
            uni_modal('New User', 'manage_user.php');
        });

        $('.edit_user').click(function() {
            uni_modal('Edit User', 'manage_user.php?id=' + $(this).data('id'));
        });

        

            $('.delete_user').click(function() {
                _conf("Are you sure to delete this user?", "delete_user", [$(this).data('id')]);
            });

            
    });
</script>
