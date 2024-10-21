<?php
include 'db_connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['program_code']) && isset($_GET['year'])) {
    $program_code = $_GET['program_code'];
    $year = $_GET['year'];

    $program_query = "SELECT id, program_name FROM program WHERE program_code = ?";
    $program_stmt = $conn->prepare($program_query);
    $program_stmt->bind_param("s", $program_code);
    $program_stmt->execute();
    $program_result = $program_stmt->get_result();
    $program = $program_result->fetch_assoc();
    $program_id = $program['id'];
    $program_name = $program['program_name'];

    $level_query = "SELECT DISTINCT level, period FROM courses WHERE program_id = ? AND year = ? ORDER BY level ASC, period ASC";
    $level_stmt = $conn->prepare($level_query);
    $level_stmt->bind_param("is", $program_id, $year);
    $level_stmt->execute();
    $level_result = $level_stmt->get_result();
}
?>

<div class="container-fluid p-3">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fa  fa-folder-open"></i> List of Courses</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> Course Management</li>
                <li class="breadcrumb-item active"><a href="index.php?page=view_course&program_code=<?php echo $program_code ?>"> View Course</a></li>
                <li class="breadcrumb-item active">List of Course</li>
            </ol>
        </section>
        <section class="content col-md-12">

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-default shadow mb-4">
                        <div class="card-header bg-transparent">
                            <h3 class="card-title mb-0"><?php echo $program['program_name'] . ' - ' . $year . ' - ' . ($year + 1); ?></h3>
                        </div>
                        <?php while ($level = $level_result->fetch_assoc()) : ?>
                            <?php
                            $totalLec = 0;
                            $totalLab = 0;
                            $totalUnits = 0;
                            ?>
                            <div class="card-body pt-0">
                                <div class='table-responsive'>
                                    <table class="table table-bordered">
                                        <thead>
                                            <th class="pl-0" colspan="6" style="border: none;"><?php echo $level['level'] . ' ( ' . $level['period'] . ' )'; ?></th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th rowspan="2" class="col-sm-2 align-middle text-center p-2">Course No.</th>
                                                <th rowspan="2" class="col-sm-6 align-middle text-center p-2">Descriptive Title</th>
                                                <th colspan="2" class="col-sm-2 align-middle text-center p-2">No. of Hrs/Week</th>
                                                <th rowspan="2" class='col-sm-1 align-middle text-center p-2'>UNITS</th>
                                                <th rowspan="2" class='col-sm-1 align-middle text-center p-2'>COMPLAB</th>
                                            </tr>
                                            <tr>
                                                <th class="col-sm-1 align-middle text-center p-1">Lec</th>
                                                <th class="col-sm-1 align-middle text-center p-1">Lab</th>
                                            </tr>
                                            <?php
                                            $courses_query = "SELECT * FROM courses WHERE program_id = (SELECT id FROM program WHERE program_code = ?) AND year = ? AND level = ? AND period = ?";
                                            $courses_stmt = $conn->prepare($courses_query);
                                            $courses_stmt->bind_param("ssss", $program_code, $year, $level['level'], $level['period']);
                                            $courses_stmt->execute();
                                            $courses_result = $courses_stmt->get_result();
                                            ?>
                                            <?php while ($courses = $courses_result->fetch_assoc()) : ?>
                                                <tr>
                                                    <td>
                                                        <a onclick="editmodal('<?php echo $courses['id']; ?>')" href="#" title="Click to Edit"><?php echo $courses['course_code']; ?></a>
                                                    </td>
                                                    <td><?php echo $courses['course_name']; ?></td>
                                                    <td class="align-middle text-center">
                                                        <?php echo $courses['lec']; ?>
                                                        <?php $totalLec += $courses['lec']; ?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <?php echo $courses['lab']; ?>
                                                        <?php $totalLab += $courses['lab']; ?>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <?php if ($courses['units'] != 0) : ?>
                                                            <?php echo $courses['units']; ?>
                                                        <?php endif; ?>
                                                        <?php $totalUnits += $courses['units']; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if (isset($courses['is_comlab']) && $courses['is_comlab'] == 1) : ?>
                                                            <span class='text-info'><i class='fas fa-check-circle'></i></span>
                                                        <?php else : ?>
                                                            <span class='text-danger'><i class='fas fa-times-circle'></i></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <div align='right'>Total:</div>
                                                </td>
                                                <td class="align-middle text-center"><?php echo $totalLec; ?></td>
                                                <td class="align-middle text-center"><?php echo $totalLab; ?></td>
                                                <td class="align-middle text-center"><?php echo $totalUnits; ?></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </section>
        <div id="displayeditmodal">

        </div>
    </div>
</div>
<script>
    function editmodal(id) {
        var array = {};
        array['course_id'] = id;
        $.ajax({
            type: "GET",
            url: "edit_course.php",
            data: array,
            success: function(data) {
                $('#displayeditmodal').html(data).fadeIn();
                $('#editModal').modal('toggle');
            },
            error: function() {
                alert_toast('Something Went Wrong!', 'danger');
            }
        })
    }
</script>
<style>
    .card-header {
        border-bottom: none;
    }
</style>