<?php
include 'db_connect.php';

if (isset($_GET['faculty_id'])) {
    $faculty_id = $_GET['faculty_id'];
    $program_id = $_SESSION['login_program_id'];

    // Fetch program information
    $program_query = "SELECT * FROM program WHERE id = ?";
    $program_stmt = $conn->prepare($program_query);
    $program_stmt->bind_param("i", $program_id);
    $program_stmt->execute();
    $program_result = $program_stmt->get_result();
    $program = $program_result->fetch_assoc();

    $faculty_query = "SELECT faculty.*, designation.designation, unit_loads.academic_rank FROM faculty INNER JOIN unit_loads ON faculty.academic_rank = unit_loads.id LEFT JOIN designation ON faculty.designation = designation.id WHERE faculty.id = ?";
    $faculty_stmt = $conn->prepare($faculty_query);
    $faculty_stmt->bind_param("i", $faculty_id);
    $faculty_stmt->execute();
    $faculty_result = $faculty_stmt->get_result();
    $faculty = $faculty_result->fetch_assoc();

    $head_query = "SELECT * FROM faculty WHERE designation = 1";
    $head_result = mysqli_query($conn, $head_query);
    $head = $head_result->fetch_assoc();

    $num_class_query = "
    SELECT COUNT(DISTINCT course_offering_info_id) AS num_class 
    FROM schedules
    WHERE faculty_id = ? AND is_active = 1
    ";
    $num_class_stmt = $conn->prepare($num_class_query);
    $num_class_stmt->bind_param('i', $faculty_id);
    $num_class_stmt->execute();
    $num_class_result = $num_class_stmt->get_result();
    $num_class_row = $num_class_result->fetch_assoc();
    $num_class = $num_class_row['num_class'];

    $prep_class_query = "
    SELECT COUNT(DISTINCT c.id) AS prep_class
    FROM schedules s
    JOIN course_offering_info coi ON s.course_offering_info_id = coi.id
    JOIN courses c ON coi.courses_id = c.id
    WHERE s.faculty_id = ? AND s.is_active = 1
    ";
    $prep_class_stmt = $conn->prepare($prep_class_query);
    $prep_class_stmt->bind_param('i', $faculty_id);
    $prep_class_stmt->execute();
    $prep_class_result = $prep_class_stmt->get_result();
    $prep_class_row = $prep_class_result->fetch_assoc();
    $prep_class = $prep_class_row['prep_class'];

    $regular_schedules_query = "
    SELECT 
        schedules.course_offering_info_id, 
        schedules.time_start, 
        schedules.time_end, 
        schedules.total_hours,
        GROUP_CONCAT(DISTINCT schedules.day ORDER BY FIELD(schedules.day, 'M', 'T', 'W', 'TH', 'F', 'S') SEPARATOR '') AS combined_days, 
        schedules.room_id, 
        rooms.room 
    FROM schedules
    INNER JOIN rooms ON schedules.room_id = rooms.id
    WHERE schedules.is_active = 1 
    AND schedules.is_overload = 0 
    AND schedules.faculty_id = ?
    GROUP BY schedules.course_offering_info_id, schedules.time_start, schedules.time_end, schedules.room_id
    ";
    $regular_schedules_stmt = $conn->prepare($regular_schedules_query);
    $regular_schedules_stmt->bind_param('i', $faculty_id);
    $regular_schedules_stmt->execute();
    $regular_schedules_result = $regular_schedules_stmt->get_result();

    $regular_schedules = [];
    while ($row = $regular_schedules_result->fetch_assoc()) {
        $regular_schedules[] = $row;
    }

    $overload_schedules_query = "
        SELECT 
        schedules.course_offering_info_id, 
        schedules.time_start, 
        schedules.time_end,
        schedules.total_hours, 
        GROUP_CONCAT(DISTINCT schedules.day ORDER BY FIELD(schedules.day, 'M', 'T', 'W', 'TH', 'F', 'S') SEPARATOR '') AS combined_days, 
        schedules.room_id, 
        rooms.room 
    FROM schedules
    INNER JOIN rooms ON schedules.room_id = rooms.id
    WHERE schedules.is_active = 1 
    AND schedules.is_overload = 1 
    AND schedules.faculty_id = ?
    GROUP BY schedules.course_offering_info_id, schedules.time_start, schedules.time_end, schedules.room_id
    ";
    $overload_schedules_stmt = $conn->prepare($overload_schedules_query);
    $overload_schedules_stmt->bind_param('i', $faculty_id);
    $overload_schedules_stmt->execute();
    $overload_schedules_result = $overload_schedules_stmt->get_result();

    $overload_schedules = [];
    while ($row = $overload_schedules_result->fetch_assoc()) {
        $overload_schedules[] = $row;
    }

    // Fetch active semester
    $currentMonthDay = date('m-d');
    $semester_query = "
        SELECT * FROM semester 
        WHERE 
            DATE_FORMAT(start_date, '%m-%d') <= '$currentMonthDay' 
            AND DATE_FORMAT(end_date, '%m-%d') >= '$currentMonthDay'
        LIMIT 1
    ";
    $semester_result = mysqli_query($conn, $semester_query);

    if ($semester_result && $semester_row = $semester_result->fetch_assoc()) {
        $semester_name = $semester_row['sem_name'];
    } else {
        $semester_name = "No Active Semester";
    }

    // Totals
    $total_units_regular = 0;
    $total_lec_regular = 0;
    $total_lab_regular = 0;
    $total_units_overload = 0;
    $total_lec_overload = 0;
    $total_lab_overload = 0;
}
?>

<style>
    .underline-text {
        font-size: 14px;
        border-bottom: 1px solid black;
        padding-inline: 10px;
        padding-bottom: 1px;
    }

    .underlined {
        border: none;
        border-bottom: 1px solid black;
        width: auto;
        text-align: center;
    }
</style>
<div class="container-fluid p-3">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fa fa-download"></i> Generate Teaching Load</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="index.php?page=faculty_loading">View Faculty Loading</a></li>
                <li class="breadcrumb-item active">Generate Teaching Load</li>
            </ol>
        </section>
        <section class="content col-sm-12">
            <div class="card shadow p-5 mb-4">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="4" class="text-center align-middle" style="width: 150px;"><img src="../assets/img/1-removebg-preview.jpeg" alt="EVSU Logo" style="max-width: 100px;"></th>
                                        <th colspan="3" class="align-middle text-center">
                                            <h5 class="font-weight-bold mb-0">EASTERN VISAYAS STATE UNIVERSITY</h5>
                                            <p class="mb-0">Tacloban City</p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th rowspan="3" class="align-middle"><strong>Title of Form:</strong> <span class="font-italic">Teacher Workload Form</span></th>
                                        <th class="align-middle text-center p-0">Control No.</th>
                                        <th class="align-middle text-center p-0">EVSU-ACA-F-002</th>
                                    </tr>
                                    <tr>
                                        <th class="align-middle text-center p-0"> Revision No. </th>
                                        <th class="align-middle text-center p-0">02</th>
                                    </tr>
                                    <tr>
                                        <th class="align-middle text-center p-0">Date</th>
                                        <th class="align-middle text-center p-0">February 20, 2023</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-8">
                        <strong>Faculty Member:</strong> <?php echo strtoupper($faculty['fname']) . " " .
                                                                (!empty($faculty['mname']) ? strtoupper(substr($faculty['mname'], 0, 1)) . ". " : "") .
                                                                strtoupper($faculty['lname']) . ", " . strtoupper($faculty['post_graduate_studies']); ?><br>
                        <strong>Academic Rank:</strong> <?php echo strtoupper($faculty['academic_rank']); ?><br>
                        <strong>College/Campus:</strong> CARIGARA CAMPUS
                    </div>
                    <div class="col-md-4">
                        <strong>Semester:</strong> <?php echo $semester_name; ?><br>
                        <strong>School Year:</strong> <?php
                                                        $currentYear = date("Y");
                                                        $nextYear = $currentYear + 1;
                                                        echo "$currentYear-$nextYear";
                                                        ?><br>
                        <strong>Designation:</strong> <?php echo strtoupper($faculty['designation']); ?>
                    </div>
                </div>

                <!-- Table for Regular Workload -->
                <h6 class="mt-4 mb-1">REGULAR</h6>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle text-center p-2">Course No.</th>
                                        <th rowspan="2" class="align-middle text-center p-2">Descriptive Title</th>
                                        <th rowspan="2" class="align-middle text-center p-2">Subject Units</th>
                                        <th rowspan="2" class="align-middle text-center p-2">TIME</th>
                                        <th rowspan="2" class="align-middle text-center p-2">DAYS</th>
                                        <th colspan="2" class="align-middle text-center p-2">No. of Hrs/Week</th>
                                        <th rowspan="2" class="align-middle text-center p-2">No. of Students</th>
                                        <th rowspan="2" class="align-middle text-center p-2">Room No.</th>
                                        <th rowspan="2" class="align-middle text-center p-2">Course, Yr., & Sec.</th>
                                    </tr>
                                    <tr>
                                        <th class="align-middle text-center p-1">Lec</th>
                                        <th class="align-middle text-center p-1">Lab</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($regular_schedules)) : ?>
                                        <?php
                                        $previous_course_offering_info_id = null;
                                        $days_combined = [];
                                        $total_units_regular = 0;
                                        $total_lec_regular = 0;
                                        $total_lab_regular = 0;
                                        $total_lec_hours = 0;
                                        $total_lab_hours = 0;

                                        $schedule_count = count($regular_schedules);

                                        foreach ($regular_schedules as $index => $schedule) {
                                            $next_course_offering_info_id = ($index + 1 < $schedule_count) ? $regular_schedules[$index + 1]['course_offering_info_id'] : null;
                                            $previous_course_offering_info_id = ($index > 0) ? $regular_schedules[$index - 1]['course_offering_info_id'] : null;

                                            if ($index == 0 || $schedule['course_offering_info_id'] !== $previous_course_offering_info_id) {
                                                $days_combined = [];
                                                $total_lec_hours = 0;
                                                $total_lab_hours = 0;
                                            }

                                            if (!in_array($schedule['combined_days'], $days_combined)) {
                                                $days_combined[] = $schedule['combined_days'];
                                            }

                                            $course_detail_query = "
                                        SELECT 
                                            courses.course_code, 
                                            courses.course_name, 
                                            program.program_code, 
                                            sections.level, 
                                            sections.section_name,
                                            sections.no_of_students, 
                                            courses.units,
                                            courses.lec,
                                            courses.lab
                                        FROM 
                                            courses 
                                        INNER JOIN 
                                            course_offering_info ON course_offering_info.courses_id = courses.id 
                                        INNER JOIN 
                                            sections ON course_offering_info.section_id = sections.id
                                        INNER JOIN 
                                            program ON sections.program_id = program.id
                                        WHERE 
                                            course_offering_info.id = ?
                                        ";
                                            $course_detail_stmt = $conn->prepare($course_detail_query);
                                            $course_detail_stmt->bind_param('i', $schedule['course_offering_info_id']);
                                            $course_detail_stmt->execute();
                                            $course_detail_result = $course_detail_stmt->get_result();
                                            $course_detail = $course_detail_result->fetch_assoc();

                                            $section_name_concatenated = $course_detail['program_code'] . '-' . substr($course_detail['level'], 0, 1) . $course_detail['section_name'];

                                            if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) {
                                                $total_units_regular += $course_detail['units'];
                                                $total_lec_regular += $course_detail['lec'];
                                                $total_lab_regular += $course_detail['lab'];
                                            }

                                            if ($previous_course_offering_info_id == $schedule['course_offering_info_id'] || $next_course_offering_info_id == $schedule['course_offering_info_id']) {
                                                if ($schedule['total_hours'] == $course_detail['lec']) {
                                                    $display_lec_hours = $course_detail['lec'];
                                                    $display_lab_hours = 0;
                                                } elseif ($schedule['total_hours'] == $course_detail['lab']) {
                                                    $display_lec_hours = 0;
                                                    $display_lab_hours = $course_detail['lab'];
                                                }
                                            } else {
                                                $display_lec_hours = $course_detail['lec'];
                                                $display_lab_hours = $course_detail['lab'];
                                            }

                                        ?>
                                            <tr>
                                                <td class="align-middle text-center p-1"><?php echo $course_detail['course_code']; ?></td>
                                                <td class="align-middle text-center p-1"><?php echo $course_detail['course_name']; ?></td>

                                                <?php if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) : ?>
                                                    <td class="align-middle text-center p-1"><?php echo $course_detail['units']; ?></td>
                                                <?php else : ?>
                                                    <td class="align-middle text-center p-1"></td>
                                                <?php endif; ?>
                                                <td class="align-middle text-center p-1">
                                                    <?php echo date('g:iA', strtotime($schedule['time_start'])) . ' - ' . date('g:iA', strtotime($schedule['time_end'])); ?>
                                                </td>
                                                <?php if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) : ?>
                                                    <td class="align-middle text-center p-1"><?php echo implode('', $days_combined); ?></td>
                                                <?php else : ?>
                                                    <td class="align-middle text-center p-1"><?php echo $schedule['combined_days']; ?></td>
                                                <?php endif; ?>

                                                <td class="align-middle text-center p-1"><?php echo $display_lec_hours; ?></td>
                                                <td class="align-middle text-center p-1"><?php echo $display_lab_hours; ?></td>

                                                <?php if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) : ?>
                                                    <td class="align-middle text-center p-1"><?php echo $course_detail['no_of_students']; ?></td>
                                                    <td class="align-middle text-center p-1"><?php echo $schedule['room']; ?></td>
                                                    <td class="align-middle text-center p-1"><?php echo $section_name_concatenated; ?></td>
                                                <?php else : ?>
                                                    <td class="align-middle text-center p-1"></td>
                                                    <td class="align-middle text-center p-1"></td>
                                                    <td class="align-middle text-center p-1"></td>
                                                <?php endif; ?>
                                            </tr>

                                            <?php
                                            $previous_course_offering_info_id = $schedule['course_offering_info_id'];
                                            ?>
                                        <?php }
                                        ?>

                                        <tr>
                                            <td rowspan="2"></td>
                                            <td rowspan="2" class="text-center align-middle p-1"><strong>Total</strong></td>
                                            <td rowspan="2" class="align-middle text-center p-1"><?php echo $total_units_regular; ?></td>
                                            <td rowspan="2"></td>
                                            <td rowspan="2"></td>
                                            <td class="align-middle text-center p-1"><?php echo $total_lec_regular; ?></td>
                                            <td class="align-middle text-center p-1"><?php echo $total_lab_regular; ?></td>
                                            <td rowspan="2"></td>
                                            <td rowspan="2"></td>
                                            <td rowspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="align-middle text-center p-1"> <?php echo $total_lec_regular + $total_lab_regular; ?></td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No schedules found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Table for Overload/Part-Time Workload -->
                <h6 class="mt-1">OVERLOAD/PART-TIME</h6>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle text-center p-2">Course No.</th>
                                        <th rowspan="2" class="align-middle text-center p-2">Descriptive Title</th>
                                        <th rowspan="2" class="align-middle text-center p-2">Subject Units</th>
                                        <th rowspan="2" class="align-middle text-center p-2">TIME</th>
                                        <th rowspan="2" class="align-middle text-center p-2">DAYS</th>
                                        <th colspan="2" class="align-middle text-center p-2">No. of Hrs/Week</th>
                                        <th rowspan="2" class="align-middle text-center p-2">No. of Students</th>
                                        <th rowspan="2" class="align-middle text-center p-2">Room No.</th>
                                        <th rowspan="2" class="align-middle text-center p-2">Course, Yr., & Sec.</th>
                                    </tr>
                                    <tr>
                                        <th class="align-middle text-center p-1">Lec</th>
                                        <th class="align-middle text-center p-1">Lab</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($overload_schedules)) : ?>
                                        <?php
                                        $previous_course_offering_info_id = null;
                                        $days_combined = [];
                                        $total_units_regular = 0;
                                        $total_lec_regular = 0;
                                        $total_lab_regular = 0;
                                        $total_lec_hours = 0;
                                        $total_lab_hours = 0;

                                        $schedule_count = count($overload_schedules);

                                        foreach ($overload_schedules as $index => $schedule) {

                                            $next_course_offering_info_id = ($index + 1 < $schedule_count) ? $overload_schedules[$index + 1]['course_offering_info_id'] : null;
                                            $previous_course_offering_info_id = ($index > 0) ? $overload_schedules[$index - 1]['course_offering_info_id'] : null;

                                            if ($index == 0 || $schedule['course_offering_info_id'] !== $previous_course_offering_info_id) {
                                                $days_combined = [];
                                                $total_lec_hours = 0;
                                                $total_lab_hours = 0;
                                            }

                                            if (!in_array($schedule['combined_days'], $days_combined)) {
                                                $days_combined[] = $schedule['combined_days'];
                                            }

                                            $course_detail_query = "
                                        SELECT 
                                            courses.course_code, 
                                            courses.course_name, 
                                            program.program_code, 
                                            sections.level, 
                                            sections.section_name,
                                            sections.no_of_students, 
                                            courses.units,
                                            courses.lec,
                                            courses.lab
                                        FROM 
                                            courses 
                                        INNER JOIN 
                                            course_offering_info ON course_offering_info.courses_id = courses.id 
                                        INNER JOIN 
                                            sections ON course_offering_info.section_id = sections.id
                                        INNER JOIN 
                                            program ON sections.program_id = program.id
                                        WHERE 
                                            course_offering_info.id = ?
                                        ";
                                            $course_detail_stmt = $conn->prepare($course_detail_query);
                                            $course_detail_stmt->bind_param('i', $schedule['course_offering_info_id']);
                                            $course_detail_stmt->execute();
                                            $course_detail_result = $course_detail_stmt->get_result();
                                            $course_detail = $course_detail_result->fetch_assoc();

                                            $section_name_concatenated = $course_detail['program_code'] . '-' . substr($course_detail['level'], 0, 1) . $course_detail['section_name'];

                                            if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) {
                                                $total_units_regular += $course_detail['units'];
                                                $total_lec_regular += $course_detail['lec'];
                                                $total_lab_regular += $course_detail['lab'];
                                            }

                                            if ($previous_course_offering_info_id == $schedule['course_offering_info_id'] || $next_course_offering_info_id == $schedule['course_offering_info_id']) {
                                                if ($schedule['total_hours'] == $course_detail['lec']) {
                                                    $display_lec_hours = $course_detail['lec'];
                                                    $display_lab_hours = 0;
                                                } elseif ($schedule['total_hours'] == $course_detail['lab']) {
                                                    $display_lec_hours = 0;
                                                    $display_lab_hours = $course_detail['lab'];
                                                }
                                            } else {
                                                $display_lec_hours = $course_detail['lec'];
                                                $display_lab_hours = $course_detail['lab'];
                                            }
                                        ?>
                                            <tr>
                                                <td class="align-middle text-center p-1"><?php echo $course_detail['course_code']; ?></td>
                                                <td class="align-middle text-center p-1"><?php echo $course_detail['course_name']; ?></td>

                                                <?php if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) : ?>
                                                    <td class="align-middle text-center p-1"><?php echo $course_detail['units']; ?></td>
                                                <?php else : ?>
                                                    <td class="align-middle text-center p-1"></td>
                                                <?php endif; ?>
                                                <td class="align-middle text-center p-1">
                                                    <?php echo date('g:i', strtotime($schedule['time_start']))  . ' - ' . date('g:iA', strtotime($schedule['time_end'])); ?>
                                                </td>
                                                <?php if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) : ?>
                                                    <td class="align-middle text-center p-1"><?php echo implode('', $days_combined); ?></td>
                                                <?php else : ?>
                                                    <td class="align-middle text-center p-1"><?php echo $schedule['combined_days']; ?></td>
                                                <?php endif; ?>

                                                <td class="align-middle text-center p-1"><?php echo $display_lec_hours; ?></td>
                                                <td class="align-middle text-center p-1"><?php echo $display_lab_hours; ?></td>

                                                <?php if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) : ?>
                                                    <td class="align-middle text-center p-1"><?php echo $course_detail['no_of_students']; ?></td>
                                                    <td class="align-middle text-center p-1"><?php echo $schedule['room']; ?></td>
                                                    <td class="align-middle text-center p-1"><?php echo $section_name_concatenated; ?></td>
                                                <?php else : ?>
                                                    <td class="align-middle text-center p-1"></td>
                                                    <td class="align-middle text-center p-1"></td>
                                                    <td class="align-middle text-center p-1"></td>
                                                <?php endif; ?>
                                            </tr>
                                            <?php $next_course_offering_info_id = $schedule['course_offering_info_id']; ?>
                                            <?php $previous_course_offering_info_id = $schedule['course_offering_info_id']; ?>
                                        <?php }
                                        ?>
                                        <tr>
                                            <td rowspan="2"></td>
                                            <td rowspan="2" class="text-center align-middle p-1"><strong>Total</strong></td>
                                            <td rowspan="2" class="align-middle text-center p-1"><?php echo $total_units_overload; ?></td>
                                            <td rowspan="2"></td>
                                            <td rowspan="2"></td>
                                            <td class="align-middle text-center p-1"><?php echo $total_lec_overload; ?></td>
                                            <td class="align-middle text-center p-1"><?php echo $total_lab_overload; ?></td>
                                            <td rowspan="2"></td>
                                            <td rowspan="2"></td>
                                            <td rowspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="align-middle text-center p-1"> <?php echo $total_lec_overload + $total_lab_overload; ?></td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <td class="p-3"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td rowspan="2"></td>
                                            <td rowspan="2" class="text-center align-middle "><strong>Total</strong></td>
                                            <td rowspan="2" class="text-center align-middle ">0</td>
                                            <td rowspan="2"></td>
                                            <td rowspan="2"></td>
                                            <td class="align-middle text-center p-1">0</td>
                                            <td class="align-middle text-center p-1">0</td>
                                            <td rowspan="2"></td>
                                            <td rowspan="2"></td>
                                            <td rowspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="align-middle text-center p-1">0</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Other In-School Involvement/Assignment -->
                <div class="row mt-4 mb-4">
                    <div class="col-md-7 pr-0">
                        <div class="mb-1">
                            <strong>Other In-School Involvement/Assignment Per Week:</strong>
                        </div>
                        <div class="col-md-5 pl-3 text-right">
                            <div class="mb-2">
                                Administrative: <input type="text" id="administrative_hours" value="" size="2" class="underlined"> Hours
                            </div>
                            <div class="mb-2">
                                Research: <input type="text" id="research_hours" value="" size="2" class="underlined"> Hours
                            </div>
                            <div class="mb-2">
                                Extension Services: <input type="text" id="extension_hours" value="" size="2" class="underlined"> Hours
                            </div>
                            <div class="mb-2">
                                Consultation: <input type="text" id="consultation_hours" value="" size="2" class="underlined"> Hours
                            </div>
                            <div class="mb-2">
                                Instructional Functions: <input type="text" id="instructional_hours" value="" size="2" class="underlined"> Hours
                            </div>
                            <div class="mb-2">
                                Others (Specify): <input type="text" id="other_hours" value="" size="2" class="underlined"> Hours
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5 pl-5">
                        <div class="mb-2 mt-5">
                            No. of Classes: <strong> <?php echo $num_class; ?></strong>
                        </div>
                        <div class="mb-2">
                            No. of Preparation: <strong> <?php echo $prep_class; ?></strong>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 text-center">
                    <div>
                        <strong class="underline-text text-center">
                            <?php echo strtoupper($faculty['fname']) . " " .
                                (!empty($faculty['mname']) ? strtoupper(substr($faculty['mname'], 0, 1)) . ". " : "") .
                                strtoupper($faculty['lname']) . ", " . strtoupper($faculty['post_graduate_studies']); ?>
                        </strong>
                    </div>
                    <small class="d-block text-center">Faculty</small>
                </div>

                <!-- Signatures -->
                <div class="row mt-4 justify-content-center">
                    <div class="col-md-3 text-center">
                        <strong class="underline-text"><?php echo strtoupper($head['fname']) . " " .
                                                            (!empty($head['mname']) ? strtoupper(substr($head['mname'], 0, 1)) . ". " : "") .
                                                            strtoupper($head['lname']) . ", " . strtoupper($head['post_graduate_studies']); ?></strong><br>
                        <small>Head, <?php echo $program['department']; ?></small>
                    </div>
                    <div class="col-md-3 text-center">
                        <strong><input type="text" name="cd_signature" id="cd_signature" value="" size="20" class="underlined"></strong><br>
                        <small>Campus Director</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <strong><input type="text" name="vpaa_signature" id="vpaa_signature" value="" size="20" class="underlined"></strong><br>
                        <small>Vice President for Academic Affairs</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <strong><input type="text" name="up_signature" id="up_signature" value="" size="20" class="underlined"></strong><br>
                        <small>University President</small>
                    </div>
                </div>
            </div>

            <!-- Button to Generate PDF -->
            <button class="btn btn-block btn-success shadow mb-4" onclick="generatePDF()">Generate PDF</button>
        </section>
    </div>
</div>
<script>
    function generatePDF() {
        var administrativeHours = document.getElementById('administrative_hours').value;
        var researchHours = document.getElementById('research_hours').value;
        var extensionHours = document.getElementById('extension_hours').value;
        var consultationHours = document.getElementById('consultation_hours').value;
        var instructionalHours = document.getElementById('instructional_hours').value;
        var otherHours = document.getElementById('other_hours').value;
        var cdSignature = document.getElementById('cd_signature').value;
        var vpaaSignature = document.getElementById('vpaa_signature').value;
        var upSignature = document.getElementById('up_signature').value;

        if (!administrativeHours || !researchHours || !extensionHours || !consultationHours || !instructionalHours || !otherHours || !cdSignature || !vpaaSignature || !upSignature) {
            window.location.href = "#page-top";
            alert_toast("Please fill in all fields.", 'danger');
            return;
        }

        var program_dept = "<?php echo $program['department']; ?>";

        var url = 'reportAjax/generate_teaching_load.php?faculty_id=<?php echo $faculty_id; ?>' +
            '&program_dept=' + encodeURIComponent(program_dept) +
            '&administrative_hours=' + encodeURIComponent(administrativeHours) +
            '&research_hours=' + encodeURIComponent(researchHours) +
            '&extension_hours=' + encodeURIComponent(extensionHours) +
            '&consultation_hours=' + encodeURIComponent(consultationHours) +
            '&instructional_hours=' + encodeURIComponent(instructionalHours) +
            '&other_hours=' + encodeURIComponent(otherHours) +
            '&cd_signature=' + encodeURIComponent(cdSignature) +
            '&vpaa_signature=' + encodeURIComponent(vpaaSignature) +
            '&up_signature=' + encodeURIComponent(upSignature);

        window.location.href = url;
    }
</script>