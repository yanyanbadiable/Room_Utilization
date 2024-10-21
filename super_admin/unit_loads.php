<?php include '../admin/db_connect.php'; ?>
<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM unit_loads WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-cogs"></i> Manage Academic Rank</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"></li>Other Management</li>
                <li class="breadcrumb-item active">Manage Academic Rank</li>
            </ol>
        </section>
        <!-- End Section Header -->

        <!-- Academic Rank Form Panel -->
        <section class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Academic Rank Form</h6>
                </div>
                <div class="card-body">
                    <form id="manage-academic_rank">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Academic Rank</label>
                            <input type="text" class="form-control" name="academic_rank">
                        </div>
                        <!-- <div class="form-group">
                            <label class="control-label">Units</label>
                            <input type="number" class="form-control" name="units">
                        </div> -->
                        <div class="form-group">
                            <label class="control-label">Hours</label>
                            <input type="number" class="form-control" name="hours">
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
            /* .card-body {
                max-height: 60vh;
                overflow-y: auto;
            } */
        </style>

        <section class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Academic Rank List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Academic Rank</th>
                                    <!-- <th class="text-center">Units Assigned</th> -->
                                    <th class="text-center">Regular Hours Assigned</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $academic_rank = $conn->query("SELECT * FROM unit_loads");
                                if (!$academic_rank) {
                                    die('Invalid query: ' . $conn->error);
                                }
                                while ($row = $academic_rank->fetch_assoc()) :
                                ?>
                                    <tr class="text-center">
                                        <td><?php echo $i++ ?></td>
                                        <td><?php echo $row['academic_rank'] ?></td>
                                        <td><?php echo $row['hours'] ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit_academic_rank" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-academic_rank="<?php echo $row['academic_rank'] ?>" data-hours="<?php echo $row['hours'] ?>">Edit</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item delete_academic_rank" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>Delete</a>
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
        $('#manage-academic_rank').get(0).reset()
        $('#manage-academic_rank input,#manage-academic_rank textarea').val('')
    }
    $('#manage-academic_rank').submit(function(e) {
        e.preventDefault()
        var academic_rank = $("input[name='academic_rank']").val();
        var hours = $("input[name='hours']").val();

        if (!academic_rank || !hours) {
            alert_toast("Please fill in all fields!", 'warning');
            return;
        }
        start_load()
        $.ajax({
            url: '../admin/ajax.php?action=save_academic_rank',
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
    $('.edit_academic_rank').click(function() {
        start_load()
        var cat = $('#manage-academic_rank')
        cat.get(0).reset()
        cat.find("[name='id']").val($(this).attr('data-id'))
        cat.find("[name='academic_rank']").val($(this).attr('data-academic_rank'))
        cat.find("[name='hours']").val($(this).attr('data-hours'))
        end_load()
    })
    $('.delete_academic_rank').click(function() {
        _conf("Are you sure to delete this Designation?", "delete_academic_rank", [$(this).attr('data-id')])
    })

    function delete_academic_rank($id) {
        start_load()
        $.ajax({
            url: '../admin/ajax.php?action=delete_academic_rank',
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