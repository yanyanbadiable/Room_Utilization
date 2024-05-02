<?php include('db_connect.php'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <!-- Section Header -->
            <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
                <h3><i class="fa fa-archive"></i> Room Reports</h3>
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"> Report Management</li>
                    <li class="breadcrumb-item active"> Room Reports</li>
                </ol>
            </section>
            <div class="container-fluid" style="margin-top: 15px;">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5>Search Filters</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label>Rooms</label>
                                    <select class="form-control select2" id="room">
                                        <option>Please Select</option>
                                        <?php foreach ($rooms as $room) : ?>
                                            <option value="<?php echo $room->room; ?>"><?php echo $room->room; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button onclick="searchdata(document.getElementById('room').value)" class="btn-block btn btn-flat btn-primary"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="displaydata"></div>
            </div>

            <!-- <script src="<?php echo asset('plugins/datatables/jquery.dataTables.js'); ?>"></script>
            <script src="<?php echo asset('plugins/datatables/dataTables.bootstrap.js'); ?>"></script> -->
            <script>
                function searchdata(room) {
                    var array = {};
                    array['room'] = room;
                    $.ajax({
                        type: "GET",
                        url: "/ajax/admin/reports/get_rooms_occupied",
                        data: array,
                        success: function(data) {
                            $('#displaydata').html(data).fadeIn();
                        },
                        error: function() {
                            toast.error('Something Went Wrong!', 'Notification!');
                        }
                    })
                }
            </script>

        </div>
    </div>
</div>