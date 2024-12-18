<?php
include('db_connect.php');

$user_department_id = $_SESSION['login_department_id'];
$query = "
    SELECT 
        faculty.*, 
        unit_loads.academic_rank, 
        designation.designation, 
        department.department_name,
        program.program_code, 
        program.program_name 
    FROM 
        faculty 
    LEFT JOIN program ON faculty.program_id = program.id 
    INNER JOIN department ON faculty.department_id = department.id 
    LEFT JOIN unit_loads ON faculty.academic_rank = unit_loads.id 
    LEFT JOIN designation ON faculty.designation = designation.id 
    WHERE 
        faculty.department_id = ?   
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_department_id);
$stmt->execute();
$result = $stmt->get_result();

$instructors = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }
}
?>

<div class="container-fluid p-3">
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
                                    <th>Program</th>
                                    <th>Academic Rank</th>
                                    <th>Designation</th>
                                    <th width="5%">Profile</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($instructors as $instructor) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($instructor['id_number']); ?></td>
                                        <td><?php
                                            echo strtoupper(htmlspecialchars($instructor['lname'])) . ', ' .
                                                strtoupper(htmlspecialchars($instructor['fname'])) . ' ' .
                                                strtoupper(substr(htmlspecialchars($instructor['mname']), 0, 1)) . '.';
                                            ?></td>
                                        <td><?php
                                            if (empty($instructor['program_id']) || is_null($instructor['program_id'])) {
                                                echo htmlspecialchars($instructor['department_name']);
                                            } else {
                                                echo htmlspecialchars($instructor['program_name']) . ' (' . htmlspecialchars($instructor['program_code']) . ')';
                                            }
                                            ?></td>
                                        <td><?php echo htmlspecialchars($instructor['academic_rank']); ?></td>
                                        <td><?php echo !empty($instructor['designation']) ? htmlspecialchars($instructor['designation']) : 'No Designation'; ?></td>
                                        <td class="text-center">
                                            <a href="index.php?page=view_faculty&id=<?php echo htmlspecialchars($instructor['id']); ?>" class="btn btn-flat btn-success">
                                                <i class="fa fa-user"></i>
                                            </a>
                                        </td>
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