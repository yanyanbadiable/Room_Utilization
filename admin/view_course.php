<?php
include('db_connect.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if program code is set in the URL
if (isset($_GET['program_id'])) {
    // Get the program code from the URL parameter
    $program_code = $_GET['program_id'];

    // Query to fetch the program name based on program code
    $program_query = $conn->prepare("SELECT * FROM program WHERE id = ?");
    $program_query->bind_param("s", $program_code);
    $program_query->execute();
    $program_result = $program_query->get_result();
    $program = $program_result->fetch_assoc();

    // Query to fetch the courses for the specified program code
    $courses_query = $conn->prepare("SELECT DISTINCT year FROM courses WHERE program_id = ?");
    $courses_query->bind_param("s", $program_code);
    $courses_query->execute();
    $courses_result = $courses_query->get_result();
    $years = $courses_result->fetch_all(MYSQLI_ASSOC);
}
?>

<section class="content">
    <div class="row">
        <div class="col-sm-12" id="displaycurriculum">
            <div class="box box-default">
                <div class="box-header">
                    <h3 class="box-title"><?php echo $program['program_name']; ?></h3>
                </div>
                <div class="box-body">
                    <div class='table-responsive'>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Curriculum Year</th>
                                    <th class="text-center" width="30%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($years as $year) : ?>
                                    <tr>
                                        <td><?php echo $year['year'] . ' - ' . ($year['year'] + 1); ?></td>
                                        <td class="text-center">
                                            <a href="index.php?page=list_course&program_id=<?php echo $program_code; ?>&year=<?php echo $year['year']; ?>" class="btn btn-flat btn-success"><i class="fa fa-eye"></i></a>
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
</section>