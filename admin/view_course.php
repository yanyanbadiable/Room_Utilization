<?php
include('db_connect.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Initialize variables
$curricula = [];

// Check if program code is set in the URL
if (isset($_GET['program_code'])) {
    // Get the program code from the URL parameter
    $program_code = $_GET['program_code'];

    // Fetch the program id corresponding to the program code
    $program_query = "SELECT id, program_name FROM program WHERE program_code = ?";
    $stmt = $conn->prepare($program_query);
    $stmt->bind_param("s", $program_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $program = $result->fetch_assoc();

    if ($program) {
        // Program found, fetch curriculum years
        $program_id = $program['id'];

        // Prepare and execute the SQL query
        $query = "SELECT DISTINCT year FROM courses WHERE program_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $program_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch the results into an array
        while ($row = $result->fetch_assoc()) {
            $curricula[] = $row['year'];
        }
    } else {
        // Program not found, handle error if needed
        echo "Program not found!";
    }
}
?>
<!-- HTML Content -->
<div class="container-fluid">
    <!-- Section Header -->
    <div class="row">
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="far fa-folder"></i> View Course</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> Course Management</li>
                <li class="breadcrumb-item active">View Course</li>
            </ol>
        </section>
    </div>
    <!-- End Section Header -->

    <!-- Display Curriculum -->
    <div class="row">
        <section class="content">
            <div class="col-sm-12" id="displaycurriculum">
                <div class="card card-default shadow mb-4">
                    <div class="card-header bg-transparent">
                        <h3 class="card-title"><?php echo $program['program_name']; ?></h3>
                    </div>
                    <div class="card-body">
                        <div class='table-responsive'>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Curriculum Year</th>
                                        <th class="text-center" width="30%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($curricula as $year) : ?>
                                        <tr>
                                        <td><?php echo $year . ' - ' . ($year + 1); ?></td>
                                            <td class="text-center">
                                                <a href="index.php?page=list_course&program_code=<?php echo $program_code; ?>&year=<?php echo $year; ?>" class="btn btn-flat btn-success"><i class="fa fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- End Display Curriculum -->
</div>
<!-- End HTML Content -->
<style>
    .card-header {
        border-bottom: none;
    }
</style>