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

        <section class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">Academic Rank List</h5>
                    <span>
                        <button class="btn btn-primary btn-md" id="new_rank"><i class="fa fa-plus mr-2"></i> New Academic Rank</button>
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Academic <br> Rank</th>
                                    <th class="text-center">Administrative</th>
                                    <th class="text-center">Research</th>
                                    <th class="text-center">Extension<br> Services</th>
                                    <th class="text-center">Consultation</th>
                                    <th class="text-center">Instructional<br> Functions</th>
                                    <th class="text-center">Others</th>
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
                                        <td><?php echo $row['administrative'] ?></td>
                                        <td><?php echo $row['research'] ?></td>
                                        <td><?php echo $row['ext_service'] ?></td>
                                        <td><?php echo $row['consultation'] ?></td>
                                        <td><?php echo $row['hours'] ?></td>
                                        <td><?php echo $row['others'] ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit_academic_rank" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Edit</a>
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
    $(document).ready(function() {
        $('#new_rank').click(function() {
            uni_modal('New Academic Rank', 'manage_rank.php');
        });

        $('table').on('click', '.edit_academic_rank', function() {
            uni_modal('Edit Academic Rank', 'manage_rank.php?id=' + $(this).data('id'));
        });

        $('table').on('click', '.delete_academic_rank', function() {
            _conf("Are you sure to delete this Designation?", "delete_academic_rank", [$(this).attr('data-id')]);
        });

        function delete_academic_rank($id) {
            start_load();
            $.ajax({
                url: '../admin/ajax.php?action=delete_academic_rank',
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

        $('table').DataTable();
    });
</script>
