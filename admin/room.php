<?php include('db_connect.php'); ?>
<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM rooms WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-cogs"></i> Manage Rooms</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="#"> Room Management</a></li>
                <li class="breadcrumb-item active">Manage Rooms</li>
            </ol>
        </section>
        <!-- End Section Header -->

        <!-- Room Form Panel -->
        <section class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Room Form</h6>
                </div>
                <div class="card-body">
                    <form id="manage-room">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Room</label>
                            <input type="text" class="form-control" name="room">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Description</label>
                            <input type="text" class="form-control" name="description">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Building</label>
                            <select class="form-control" name="building_id">
                                <?php
                                $building = $conn->query("SELECT id, building FROM building");
                                while ($row = $building->fetch_assoc()) :
                                ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['building'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-sm btn-primary col-sm-3 offset-md-3">Save</button>
                                    <button class="btn btn-sm btn-light col-sm-3" type="button" onclick="_reset()">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <!-- End Room Form Panel -->

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
                    <h6 class="m-0 font-weight-bold text-primary">Room List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Room</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Building</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $room = $conn->query("SELECT rooms.*, building.building FROM rooms INNER JOIN building ON rooms.building_id = building.id;");
                                if (!$room) {
                                    die('Invalid query: ' . $conn->error);
                                }
                                while ($row = $room->fetch_assoc()) :
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $i++ ?></td>
                                        <td class=""><?php echo $row['room'] ?></td>
                                        <td class=""><?php echo $row['description'] ?></td>
                                        <td class=""><?php echo $row['building'] ?></td>
                                        <td class="text-center">
                                            <?php if ($row['is_available'] == 1) : ?>
                                                <span class="badge badge-success" style="font-size: 16px;">Active</span>
                                            <?php else : ?>
                                                <span class="badge badge-danger" style="font-size: 16px;">Inactive</span>
                                            <?php endif; ?>

                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary edit_room" type="button" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-room="<?php echo $row['room'] ?>" data-description="<?php echo $row['description'] ?>" data-building="<?php echo $row['building_id'] ?>">Edit</button>
                                            <button class="btn btn-sm btn-danger delete_room" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
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
        $('#manage-room').get(0).reset();
        // Validate input fields
        var room = $('[name="room"]').val();
        var description = $('[name="description"]').val();
        var building_id = $('[name="building_id"]').val();

        if (!room || !description || !building_id) {
            alert_toast("Please fill in all fields", 'error');
            return; // Exit function if any field is empty
        }
        $('#manage-room input, #manage-room textarea, #manage-room select').val('');
    }
    $('#manage-room').submit(function(e) {
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_room',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully added", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 100);

                } else if (resp == 2) {
                    alert_toast("Data successfully updated", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 100);
                } else {
                    alert_toast("Room Already Exists", 'danger');
                }
            }
        });
    });
    $('.edit_room').click(function() {
        start_load();
        var cat = $('#manage-room');
        cat.get(0).reset();
        cat.find("[name='id']").val($(this).attr('data-id'));
        cat.find("[name='room']").val($(this).attr('data-room'));
        cat.find("[name='description']").val($(this).attr('data-description'));
        cat.find("[name='building_id']").val($(this).attr('data-building'));
        end_load();
    });
    $('.delete_room').click(function() {
        _conf("Are you sure to delete this room?", "delete_room", [$(this).attr('data-id')]);
    });

    function delete_room($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_room',
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
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>