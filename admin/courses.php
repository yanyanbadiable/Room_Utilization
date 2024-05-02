<?php include('db_connect.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fa fa-folder"></i> View Courses</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> Course Management</li>
                <li class="breadcrumb-item active">View Course</li>
            </ol>
        </section>
        
        <!-- End Section Header -->

        <!-- Course Table -->
        <section class="content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-default shadow mb-4">
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title">Academic Programs</h4>
                            <div>
                                <a href="index.php?page=manage_course" class="btn btn-flat btn-success"><i class="fa fa-upload"></i> New Curriculum</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class='table-responsive'>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Program Code</th>
                                            <th>Program Name</th>
                                            <th>View</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $program = $conn->query("SELECT * FROM program");
                                        if (!$program) {
                                            die('Invalid query: ' . $conn->error);
                                        }
                                        while ($row = $program->fetch_assoc()) :
                                        ?>
                                            <tr>
                                                <td><?php echo $row['program_code'] ?></td>
                                                <td><?php echo $row['program_name'] ?></td>
                                                <td class="text-center">
                                                    <a href="index.php?page=view_course&program_id=<?php echo $row['id'] ?>" class="btn btn-flat btn-primary"><i class="fa fa-eye"></i></a>
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
        <!-- End Course Table -->
    </div>
</div>

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

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>