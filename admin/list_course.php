<?php
include 'db_connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if program code is set in the URL
if (isset($_GET['program_code']) && isset($_GET['year'])) {
    $program_code = $_GET['program_code'];
    $year = $_GET['year'];

    // Fetching program name and id from the program table
    $program_query = "SELECT id, program_name FROM program WHERE program_code = ?";
    $program_stmt = $conn->prepare($program_query);
    $program_stmt->bind_param("s", $program_code);
    $program_stmt->execute();
    $program_result = $program_stmt->get_result();
    $program = $program_result->fetch_assoc();
    $program_id = $program['id'];
    $program_name = $program['program_name'];

    // Fetching levels from the courses table using program_id
    $level_query = "SELECT DISTINCT level, period FROM courses WHERE program_id = ? AND year = ? ORDER BY level ASC, period ASC";
    $level_stmt = $conn->prepare($level_query);
    $level_stmt->bind_param("is", $program_id, $year);
    $level_stmt->execute();
    $level_result = $level_stmt->get_result();
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fa  fa-folder-open"></i> List of Courses</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> Course Management</li>
                <li class="breadcrumb-item active"> View Course</li>
                <li class="breadcrumb-item active">List of Course</li>
            </ol>
        </section>
        <section class="content">
            <?php while ($level = $level_result->fetch_assoc()) : ?>
                <?php
                $totalLec = 0;
                $totalLab = 0;
                $totalUnits = 0;
                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-default shadow mb-4">
                            <div class="card-header bg-transparent">
                                <h3 class="card-title"><?php echo $program['program_name'] . ' - ' . $year . ' - ' . ($year + 1); ?></h3>
                                <div class="card-tools pull-right">
                                    <!--<a  target="_blank" href="{{url('/registrar_college/print_course',array($program_code,$course_year))}}" class='btn btn-flat btn-primary'><i class='fa fa-print'></i> Print course</a>-->
                                </div>
                            </div>
                            <div class="card-body">
                                <div class='table-responsive'>
                                    <table class="table table-condensed">
                                        <thead>
                                            <th><?php echo $level['period']; ?></th>
                                            <th><?php echo $level['level']; ?></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th class='col-sm-2'>Course Code</th>
                                                <th class='col-sm-6'>Course Description</th>
                                                <th class='col-sm-1'>LEC</th>
                                                <th class='col-sm-1'>LAB</th>
                                                <th class='col-sm-1'>UNITS</th>
                                                <th class='col-sm-1 '>COMPLAB</th>
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
                                                    <td>
                                                        <?php if ($courses['lec'] != 0) : ?>
                                                            <?php echo $courses['lec']; ?>
                                                        <?php endif; ?>
                                                        <?php $totalLec += $courses['lec']; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($courses['lab'] != 0) : ?>
                                                            <?php echo $courses['lab']; ?>
                                                        <?php endif; ?>
                                                        <?php $totalLab += $courses['lab']; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($courses['units'] != 0) : ?>
                                                            <?php echo $courses['units']; ?>
                                                        <?php endif; ?>
                                                        <?php $totalUnits += $courses['units']; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if (isset($courses['is_comlab']) && $courses['is_comlab'] == 1) : ?>
                                                            <span class='text-info'><i class='fas fa-check-circle'></i></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <th>
                                                    <div align='right'>Total</div>
                                                </th>
                                                <th><?php echo $totalLec; ?></th>
                                                <th><?php echo $totalLab; ?></th>
                                                <th><?php echo $totalUnits; ?></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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