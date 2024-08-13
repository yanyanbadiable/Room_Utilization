<?php
include('db_connect.php');

$user_program_id = $_SESSION['login_program_id'];
$query = "SELECT faculty.*, unit_loads.designation, program.program_code, program.program_name FROM faculty INNER JOIN program ON faculty.program_id = program.id INNER JOIN unit_loads ON faculty.designation = unit_loads.id WHERE program_id = $user_program_id";

$result = mysqli_query($conn, $query);

if ($result) {
    
    $instructors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $instructors[] = $row;
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-users"></i> List of Instructors</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> List of Instructors</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="card-title">List of Instructors</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID Number</th>
                                    <th>Name</th>
                                    <th width="40%">Program</th>
                                    <th width="20%">Designation</th>
                                    <th width="5%">Profile</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($instructors as $instructor) : ?>
                                    <tr>
                                        <td><?php echo $instructor['id_number']; ?></td>
                                        <td><?php echo strtoupper($instructor['lname']) . ', ' . strtoupper($instructor['fname']); ?></td>
                                        <td><?php echo $instructor['program_name'] . ' (' . $instructor['program_code'] . ')'; ?></td>
                                        <td><?php echo $instructor['designation']; ?></td>
                                        <td class="text-center"><a href="index.php?page=view_faculty&id=<?php echo $instructor['id'] ?>" class="btn btn-flat btn-success"><i class="fa fa-user"></i></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .card-header {
        border-bottom: none;
    }
</style>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>