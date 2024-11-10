<?php include '../admin/db_connect.php'; ?>
<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM building WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-cogs"></i> Manage Buildings</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="#"> Program Management</a></li>
                <li class="breadcrumb-item active">Manage Buildings</li>
            </ol>
        </section>
        <!-- End Section Header -->

        <!-- bu$building Form Panel -->
        <section class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Building Form</h6>
                </div>
                <div class="card-body">
                    <form id="manage-building">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Building Name</label>
                            <input type="text" class="form-control" name="building">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Program</label>
                            <select class="form-control" name="program_id">
                                <option value="">Please Select Here</option>
                                <?php
                                $program_code = $conn->query("SELECT id, program_code FROM program");
                                while ($row = $program_code->fetch_assoc()) :
                                ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['program_code'] ?></option>
                                <?php endwhile; ?>
                            </select>
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
                /* max-height: 60vh; */
                overflow-y: auto;
            }
        </style>

        <section class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Building List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Building Name</th>
                                    <th class="text-center">Department</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $building = $conn->query("SELECT building.*, program.id, program.program_name, program.program_code FROM building INNER JOIN program ON building.program_id = program.id");
                                if (!$building) {
                                    die('Invalid query: ' . $conn->error);
                                }
                                while ($row = $building->fetch_assoc()) :
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++ ?></td>
                                        <td class=""><?php echo $row['building'] ?></td>
                                        <td class=""><?php echo $row['program_name'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit_building" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-building="<?php echo $row['building'] ?>" data-program_id="<?php echo $row['program_id'] ?>">Edit</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item delete_building" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Delete</a>
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
        $('#manage-building').get(0).reset()
        $('#manage-building input,#manage-building textarea').val('')
    }
    $('#manage-building').submit(function(e) {
        e.preventDefault()
        var building = $("input[name='building']").val();
        var program_id = $("select[name='program_id']").val();

        if (!building || !program_id ) {
            alert_toast("Please fill in all fields!", 'warning');
            return;
        }
        start_load()
        $.ajax({
            url: '../admin/ajax.php?action=save_building',
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
    $('.edit_building').click(function() {
        start_load()
        var cat = $('#manage-building')
        cat.get(0).reset()
        cat.find("[name='id']").val($(this).attr('data-id'))
        cat.find("[name='building']").val($(this).attr('data-building'))
        cat.find("[name='program_id']").val($(this).attr('data-program_id'))
        end_load()
    })
    $('.delete_Building').click(function() {
        _conf("Are you sure to delete this Building?", "delete_building", [$(this).attr('data-id')])
    })

    function delete_building($id) {
        start_load()
        $.ajax({
            url: '../admin/ajax.php?action=delete_building',
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