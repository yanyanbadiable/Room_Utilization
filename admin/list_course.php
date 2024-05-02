<?php
include 'db_connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if program code is set in the URL
if (isset($_GET['program_id'])) {
    $program_code = $_GET['program_id'];
    $program_year = $_GET['year'];

    $program_query = $conn->prepare("SELECT * FROM program WHERE id = ?");
    $program_query->bind_param("s", $program_code);
    $program_query->execute();
    $program_result = $program_query->get_result();
    $program = $program_result->fetch_assoc();

    $courses_query = $conn->prepare("SELECT * FROM courses WHERE program_id = ? AND year = ?");
    $courses_query->bind_param("ss", $program_code, $program_year);
    $courses_query->execute();
    $courses_result = $courses_query->get_result();
    $courses = $courses_result->fetch_all(MYSQLI_ASSOC);
}
?>

<section class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-default">
                <div class="box-header">
                    <?php
                    $displayedYears = array(); // Array to store displayed years
                    foreach ($courses as $course) :
                        // Check if the year has already been displayed
                        if (!in_array($course['year'], $displayedYears)) {
                            array_push($displayedYears, $course['year']); // Add year to displayed years
                    ?>
                            <h3 class="box-title"><?php echo $program['program_name'] . ' - ' . $course['year'] . ' - ' . ($course['year'] + 1); ?></h3>
                    <?php
                        } // End if
                    endforeach;
                    ?>
                    <div class="box-tools pull-right">
                        <!--<a  target="_blank" href="{{url('/registrar_college/print_course',array($program_code,$course_year))}}" class='btn btn-flat btn-primary'><i class='fa fa-print'></i> Print course</a>-->
                    </div>
                </div>
                <div class="box-body">
                    <?php $totalUnits = 0; ?>
                    <div class='table-responsive'>
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th><?php echo $course['period'] ?></th>
                                    <th><?php echo $course['level'] ?></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class='col-sm-2'>Course Code</th>
                                    <th class='col-sm-6'>Course Name</th>
                                    <th class='col-sm-1'>LEC</th>
                                    <th class='col-sm-1'>LAB</th>
                                    <th class='col-sm-1'>UNITS</th>
                                    <th class='col-sm-1'>COMPLAB</th>
                                </tr>
                                <?php
                                $totalLec = 0;
                                $totalLab = 0;
                                $totalUnits = 0;
                                ?>
                                <?php foreach ($courses as $course) : ?>
                                    <tr>
                                        <td>
                                            <?php echo $course['course_code'] ?>
                                        </td>
                                        <td><?php echo $course['course_name'] ?></td>
                                        <td><?php if ($course['lec'] != 0) {
                                                echo $course['lec'];
                                                $totalLec += $course['lec'];
                                            } ?></td>
                                        <td><?php if ($course['lab'] != 0) {
                                                echo $course['lab'];
                                                $totalLab += $course['lab'];
                                            } ?></td>
                                        <td><?php if ($course['units'] != 0) {
                                                echo $course['units'];
                                                $totalUnits += $course['units'];
                                            } ?></td>
                                        <td><?php if ($course['is_comlab'] == 1) {
                                                echo "<span class='text-info'><i class='fa fa-check-circle-o'></i></span>";
                                            } ?></td>
                                    </tr>
                                <?php endforeach; ?>
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
</section>