<?php include '../admin/db_connect.php'; ?>

<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM department WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-cogs"></i> Manage Departments</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="#"> Department Management</a></li>
                <li class="breadcrumb-item active">Manage Departments</li>
            </ol>
        </section>
        <!-- End Section Header -->

        <!-- Department Form Panel -->
        <section class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Form</h6>
                </div>
                <div class="card-body">
                    <form id="manage-department">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Department Code</label>
                            <input type="text" class="form-control" name="department_code">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Department Name</label>
                            <input type="text" class="form-control" name="department_name">
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-sm btn-primary col-sm-3 offset-md-3">Save</button>
                                    <button class="btn btn-sm btn-outline-secondary col-sm-3" type="button" onclick="_reset()">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <!-- End Department Form Panel -->

        <style>
            /* Custom CSS for better visibility */
            th,
            td {
                vertical-align: middle !important;
            }

            th {
                white-space: nowrap;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Add a max-height to the card-body to avoid excessive height */
            /* .card-body {
                max-height: 60vh;
                overflow-y: auto;
            } */
        </style>

        <section class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Department Code</th>
                                    <th class="text-center">Department Name</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $department = $conn->query("SELECT department.* FROM department");
                                if (!$department) {
                                    die('Invalid query: ' . $conn->error);
                                }
                                while ($row = $department->fetch_assoc()) :
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++ ?></td>
                                        <td class=""><?php echo $row['department_code'] ?></td>
                                        <td class=""><?php echo $row['department_name'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit_department" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-department_code="<?php echo $row['department_code'] ?>" data-department_name="<?php echo $row['department_name'] ?>">Edit</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item delete_department" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Delete</a>
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
        </section>
        <!-- End Table Panel -->
    </div>
</div>

<style>
    td {
        vertical-align: middle !important;
    }
</style>
<script>
    function _reset() {
        $('#manage-department').get(0).reset()
        $('#manage-department input,#manage-department textarea').val('')
    }
    $('#manage-department').submit(function(e) {
        e.preventDefault()

        var departmentCode = $("input[name='department_code']").val();
        var departmentName = $("input[name='department_name']").val();

        if (!departmentCode || !departmentName) {
            alert_toast("Please fill in all fields!", 'warning');
            return; 
        }
        start_load()
        $.ajax({
            url: '../admin/ajax.php?action=save_department',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                console.log(resp)
                if (resp == 1) {
                    alert_toast("Data successfully added", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                } else if (resp == 2) {
                    alert_toast("Data successfully updated", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                }
            }
        })
    })
    $('.edit_department').click(function() {
        start_load()
        var cat = $('#manage-department')
        cat.get(0).reset()
        cat.find("[name='id']").val($(this).attr('data-id'))
        cat.find("[name='department_code']").val($(this).attr('data-department_code'))
        cat.find("[name='department_name']").val($(this).attr('data-department_name'))
        end_load()
    })
    $('.delete_department').click(function() {
        _conf("Are you sure to delete this department?", "delete_department", [$(this).attr('data-id')])
    })

    function delete_department($id) {
        start_load()
        $.ajax({
            url: '../admin/ajax.php?action=delete_department',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                }
            }
        })
    }
    $('table').dataTable()
</script>