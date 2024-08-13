<?php include '../admin/db_connect.php'; ?>

<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM program WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-cogs"></i> Manage Programs</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="#"> Program Management</a></li>
                <li class="breadcrumb-item active">Manage Programs</li>
            </ol>
        </section>
        <!-- End Section Header -->

        <!-- Program Form Panel -->
        <section class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Form</h6>
                </div>
                <div class="card-body">
                    <form id="manage-program">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Program Code</label>
                            <input type="text" class="form-control" name="program_code">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Program Name</label>
                            <input type="text" class="form-control" name="program_name">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Department</label>
                            <input type="text" class="form-control" name="department">
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
        <!-- End Program Form Panel -->

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
            .card-body {
                max-height: 60vh;
                overflow-y: auto;
            }
        </style>

        <section class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Program Code</th>
                                    <th class="text-center">Program Name</th>
                                    <th class="text-center">Department</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $program = $conn->query("SELECT program.* FROM program");
                                if (!$program) {
                                    die('Invalid query: ' . $conn->error);
                                }
                                while ($row = $program->fetch_assoc()) :
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++ ?></td>
                                        <td class=""><?php echo $row['program_code'] ?></td>
                                        <td class=""><?php echo $row['program_name'] ?></td>
                                        <td class=""><?php echo $row['department'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit_program" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-program_code="<?php echo $row['program_code'] ?>" data-program_name="<?php echo $row['program_name'] ?>" data-department="<?php echo $row['department'] ?>">Edit</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item delete_program" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Delete</a>
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
        $('#manage-program').get(0).reset()
        $('#manage-program input,#manage-program textarea').val('')
    }
    $('#manage-program').submit(function(e) {
        e.preventDefault()
        start_load()
        $.ajax({
            url: '../admin/ajax.php?action=save_program',
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
                    }, 100)

                } else if (resp == 2) {
                    alert_toast("Data successfully updated", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 100)

                }
            }
        })
    })
    $('.edit_program').click(function() {
        start_load()
        var cat = $('#manage-program')
        cat.get(0).reset()
        cat.find("[name='id']").val($(this).attr('data-id'))
        cat.find("[name='program_code']").val($(this).attr('data-program_code'))
        cat.find("[name='program_name']").val($(this).attr('data-program_name'))
        cat.find("[name='department']").val($(this).attr('data-department'))
        end_load()
    })
    $('.delete_program').click(function() {
        _conf("Are you sure to delete this program?", "delete_program", [$(this).attr('data-id')])
    })

    function delete_program($id) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=delete_program',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 100)

                }
            }
        })
    }
    $('table').dataTable()
</script>