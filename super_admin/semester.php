<?php include '../admin/db_connect.php'; ?>
<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM semester WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-cogs"></i> Manage Semester</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"></li>Other Management</li>
                <li class="breadcrumb-item active">Manage Semester</li>
            </ol>
        </section>
        <!-- End Section Header -->

        <!-- Designation Form Panel -->
        <section class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Semester Form</h6>
                </div>
                <div class="card-body">
                    <form id="manage-semester">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Semester Name</label>
                            <input type="text" class="form-control" name="sem_name">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Date Start</label>
                            <input type="date" class="form-control" name="start_date">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Date End</label>
                            <input type="date" class="form-control" name="end_date">
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
                    <h6 class="m-0 font-weight-bold text-primary">Semester List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Semester Name</th>
                                    <th class="text-center">Date Start</th>
                                    <th class="text-center">Date End</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $semester = $conn->query("SELECT * FROM semester");
                                if (!$semester) {
                                    die('Invalid query: ' . $conn->error);
                                }
                                while ($row = $semester->fetch_assoc()) :
                                ?>
                                    <tr class="text-center">
                                        <td><?php echo $i++ ?></td>
                                        <td><?php echo $row['sem_name'] ?></td>
                                        <td><?php echo $row['start_date'] ?></td>
                                        <td><?php echo $row['end_date'] ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit_semester" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-sem_name="<?php echo $row['sem_name'] ?>" data-start_date="<?php echo $row['start_date'] ?>" data-end_date="<?php echo $row['end_date'] ?>">Edit</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item delete_semester" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Delete</a>
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
        $('#manage-semester').get(0).reset()
        $('#manage-semester input,#manage-semester textarea').val('')
    }
    $('#manage-semester').submit(function(e) {
        e.preventDefault()
        start_load()

        $.ajax({
            url: '../admin/ajax.php?action=save_semester',
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
                    console.log('Before setTimeout');
                    setTimeout(function() {
                        console.log('Reloading page...');
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
    $('.edit_semester').click(function() {
        start_load()
        var cat = $('#manage-semester')
        cat.get(0).reset()
        cat.find("[name='id']").val($(this).attr('data-id'))
        cat.find("[name='sem_name']").val($(this).attr('data-sem_name'))
        cat.find("[name='start_date']").val($(this).attr('data-start_date'))
        cat.find("[name='end_date']").val($(this).attr('data-end_date'))
        end_load()
    })
    $('.delete_semester').click(function() {
        _conf("Are you sure to delete this Semester?", "delete_semester", [$(this).attr('data-id')])
    })

    function delete_semester($id) {
        start_load()
        $.ajax({
            url: '../admin/ajax.php?action=delete_semester',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1000)

                }
            }
        })
    }
    $('table').dataTable()
</script>