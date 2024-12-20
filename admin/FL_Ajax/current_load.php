<?php
include '../db_connect.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['level'])) {
    $instructor = $_GET['instructor'];
    $level = $_GET['level'];
    $department_id = $_SESSION['login_department_id'];

    $faculty_program_query = "
    SELECT program.department_id 
    FROM faculty
    JOIN program ON faculty.program_id = program.id
    WHERE faculty.id = ?
    ";
    $faculty_program_stmt = $conn->prepare($faculty_program_query);
    $faculty_program_stmt->bind_param('i', $instructor);
    $faculty_program_stmt->execute();
    $faculty_program_result = $faculty_program_stmt->get_result();
    $faculty_program = $faculty_program_result->fetch_assoc()['department_id'];


    $loads_query = "
        SELECT courses.*, course_offering_info.*, schedules.* 
        FROM courses 
        INNER JOIN course_offering_info ON courses.id = course_offering_info.courses_id 
        INNER JOIN schedules ON schedules.course_offering_info_id = course_offering_info.id 
        WHERE schedules.faculty_id = '$instructor'
    ";
    $loads_result = mysqli_query($conn, $loads_query);

    if (!$loads_result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    $loads = [];
    while ($row = mysqli_fetch_assoc($loads_result)) {
        if (!array_key_exists($row['course_offering_info_id'], $loads)) {
            $loads[$row['course_offering_info_id']] = $row;
        }
    }

    $total_hours_query = "
    SELECT DISTINCT course_offering_info_id, hours 
            FROM courses 
            INNER JOIN course_offering_info ON course_offering_info.courses_id = courses.id  
            INNER JOIN schedules ON schedules.course_offering_info_id = course_offering_info.id
            WHERE schedules.faculty_id = ? AND schedules.is_active = 1
    ";
    $total_hours_stmt = $conn->prepare($total_hours_query);
    $total_hours_stmt->bind_param('i', $instructor);
    $total_hours_stmt->execute();
    $total_hours_result = $total_hours_stmt->get_result();

    $total_hours = 0;
    $seen_course_offering_ids = [];

    while ($row = $total_hours_result->fetch_assoc()) {
        if (!in_array($row['course_offering_info_id'], $seen_course_offering_ids)) {
            $total_hours += (float) $row['hours'];
            $seen_course_offering_ids[] = $row['course_offering_info_id'];
        }
    }

    $tabular_schedules_query = "
        SELECT DISTINCT course_offering_info_id 
        FROM schedules 
        WHERE is_active = 1 AND faculty_id = '$instructor'
    ";
    $tabular_schedules_result = mysqli_query($conn, $tabular_schedules_query);

    $tabular_schedules = [];
    while ($row = mysqli_fetch_assoc($tabular_schedules_result)) {
        $tabular_schedules[] = $row;
    }

    $schedules_query = "
        SELECT * 
        FROM schedules 
        WHERE is_active = 1 AND faculty_id = '$instructor'
    ";
    $schedules_result = mysqli_query($conn, $schedules_query);

    $schedules = [];
    while ($row = mysqli_fetch_assoc($schedules_result)) {
        $schedules[] = $row;
    }

    // Timetable
    $weekDays = [
        'M' => 'Mon',
        'T' => 'Tue',
        'W' => 'Wed',
        'Th' => 'Thu',
        'F' => 'Fri',
        'S' => 'Sat',
        'Su' => 'Sun'
    ];

    function generateTimeRange($startTime, $endTime)
    {
        $times = [];
        $start = new DateTime($startTime);
        $end = new DateTime($endTime);
        $interval = new DateInterval('PT30M');

        while ($start < $end) {
            $next = clone $start;
            $next->add($interval);
            $times[] = [
                'start' => $start->format('H:i'),
                'end'   => $next->format('H:i'),
                'displayStart' => $start->format('h:i '),
                'displayEnd'   => $next->format('h:i ')
            ];
            $start = $next;
        }

        return $times;
    }

    function fetchSchedules($instructor)
    {
        global $conn;
        $sql = "
        SELECT 
            s.*, 
            co.course_name, 
            f.fname,
            f.lname, 
            sec.section_name, 
            sec.level,
            p.program_code,
            coi.section_id,
            TIMESTAMPDIFF(MINUTE, s.time_start, s.time_end) AS difference
        FROM 
            schedules s
        JOIN 
            course_offering_info coi ON s.course_offering_info_id = coi.id
        JOIN 
            courses co ON coi.courses_id = co.id
        JOIN 
            faculty f ON s.faculty_id = f.id
        JOIN 
            sections sec ON coi.section_id = sec.id
        JOIN 
            program p ON sec.program_id = p.id
        WHERE 
            s.is_active = 1 AND s.faculty_id = ? AND
            s.day IN ('M', 'T', 'W', 'Th', 'F', 'S') AND
            s.time_start BETWEEN '07:00' AND '19:00'
    ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $instructor);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function generateLightColor($seed)
    {
        srand($seed);
        $r = mt_rand(127, 255);
        $g = mt_rand(127, 255);
        $b = mt_rand(127, 255);
        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }

    function generateCalendarData($weekDays, $instructor)
    {
        $calendarData = [];
        $timeRange = generateTimeRange('07:00', '19:00');
        $schedules = fetchSchedules($instructor);
        $skipSlots = [];
        $sectionColors = [];

        foreach ($timeRange as $timeIndex => $time) {
            foreach ($weekDays as $dayCode => $dayName) {
                if (isset($skipSlots[$dayCode][$timeIndex])) {
                    continue;
                }
                $found = false;
                foreach ($schedules as $schedule) {
                    if ($schedule['day'] == $dayCode && $schedule['time_start'] <= $time['start'] && $schedule['time_end'] > $time['start']) {
                        $scheduleStart = new DateTime($schedule['time_start']);
                        $scheduleEnd = new DateTime($schedule['time_end']);
                        $duration = (int) ceil(($scheduleEnd->getTimestamp() - $scheduleStart->getTimestamp()) / 1800);

                        $facultyInitial = strtoupper(substr($schedule['fname'], 0, 1));
                        $facultyName = $facultyInitial . '. ' . $schedule['lname'];

                        $sectionNameConcatenated = $schedule['program_code'] . '-' . substr($schedule['level'], 0, 1) . $schedule['section_name'];

                        if (!isset($sectionColors[$schedule['section_id']])) {
                            $sectionColors[$schedule['section_id']] = generateLightColor($schedule['section_id']);
                        }

                        $calendarData[$time['displayStart'] . ' - ' . $time['displayEnd']][$dayCode] = [
                            'course_name' => $schedule['course_name'],
                            'faculty_name' => $facultyName,
                            'section_name' => $sectionNameConcatenated,
                            'rowspan' => $duration,
                            'schedule_id' => $schedule['id'],
                            'instructor_id' => $instructor,
                            'background_color' => $sectionColors[$schedule['section_id']]
                        ];
                        for ($i = 1; $i < $duration; $i++) {
                            $skipSlots[$dayCode][$timeIndex + $i] = true;
                        }
                        $found = true;
                        break;
                    }
                }
                if (!$found && !isset($skipSlots[$dayCode][$timeIndex])) {
                    $calendarData[$time['displayStart'] . ' - ' . $time['displayEnd']][$dayCode] = ['empty' => true];
                }
            }
        }

        return $calendarData;
    }

    $calendarData = generateCalendarData($weekDays, $instructor);
    // End of timetable

    $designation_query = "SELECT faculty.*, designation.designation, unit_loads.academic_rank FROM faculty INNER JOIN unit_loads ON faculty.academic_rank = unit_loads.id LEFT JOIN designation ON faculty.designation = designation.id WHERE faculty.id = $instructor;
    ";
    $designation_result = mysqli_query($conn, $designation_query);
    $designation = mysqli_fetch_assoc($designation_result);

    $display_value = '';
    if (!empty($designation['designation'])) {
        $display_value = $designation['designation'];
    } else if (!empty($designation['academic_rank'])) {
        $display_value = $designation['academic_rank'];
    }
}
?>

<style>
    .table {
        table-layout: fixed;
        width: 100%;
    }

    .table th,
    .table td {
        min-width: 14.28%;
        max-width: 14.28%;
    }
</style>

<div class="card shadow mb-4 px-3">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs d-flex">
            <li class="mr-auto header mr-auto py-3">
                <h4 class="m-0"><i class="fa fa-calendar"></i>
                    <span>Faculty Loading <b>(<?php echo $display_value; ?>)</b></span><br>
                    <span class="small m-0">Total No. of Hours Loaded: <?php echo $total_hours; ?></span> <br>
                </h4>
                <h5 class="m-0">
                    <span class="small m-0">Total No. of Units Loaded: <?php echo array_sum(array_column($loads, 'units')); ?></span>
                </h5>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#tab_1-1" data-toggle="tab">Calendar View</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tab_2-2" data-toggle="tab">Tabular View</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active mb-4" id="tab_1-1">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th width="125" class="p-2">Time</th>
                                <?php foreach ($weekDays as $day) : ?>
                                    <th class="p-2"><?php echo $day; ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($calendarData as $time => $days) : ?>
                                <tr>
                                    <td class="text-center p-2" style="font-size:0.9rem;"><?php echo $time; ?></td>
                                    <?php foreach ($days as $dayCode => $value) : ?>
                                        <?php if (isset($value['rowspan'])) : ?>
                                            <td rowspan="<?php echo $value['rowspan']; ?>" class="align-middle text-center clickable" style="background-color:<?php echo $value['background_color']; ?>; color: #000; font-size: 0.7rem;" data-schedule-id="<?php echo $value['schedule_id']; ?>">
                                                <?php echo $value['course_name']; ?><br>
                                                <b class="text-uppercase">
                                                    <?php echo $value['section_name']; ?><br>
                                                    (<?php echo $value['faculty_name']; ?>)
                                                </b>
                                            </td>
                                        <?php else : ?>
                                            <td></td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane mb-4" id="tab_2-2">
                <div class="table-responsive">
                    <?php if (!empty($tabular_schedules)) : ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Course</th>
                                    <th class="text-center">Units</th>
                                    <th class="text-center">Section</th>
                                    <th class="text-center">Schedule</th>
                                    <th class="text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tabular_schedules as $schedule) : ?>
                                    <?php
                                    $course_detail_query = "
                                    SELECT 
                                    courses.course_code, 
                                    courses.course_name, 
                                    courses.program_id,
                                    program.department_id,
                                    program.program_code, 
                                    sections.level, 
                                    sections.section_name, 
                                    courses.units
                                    FROM 
                                        courses 
                                    INNER JOIN 
                                        course_offering_info ON course_offering_info.courses_id = courses.id 
                                    INNER JOIN 
                                        sections ON course_offering_info.section_id = sections.id
                                    INNER JOIN 
                                        program ON courses.program_id = program.id
                                    WHERE 
                                        course_offering_info.id = ?
                                    ";
                                    $course_detail_stmt = $conn->prepare($course_detail_query);
                                    $course_detail_stmt->bind_param('i', $schedule['course_offering_info_id']);
                                    $course_detail_stmt->execute();
                                    $course_detail_result = $course_detail_stmt->get_result();
                                    $course_detail = mysqli_fetch_assoc($course_detail_result);

                                    $section_name_concatenated = $course_detail['program_code'] . '-' . substr($course_detail['level'], 0, 1) . $course_detail['section_name'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center">
                                                <?php echo $course_detail['course_code']; ?> <br>
                                                <?php echo $course_detail['course_name']; ?>
                                            </div>
                                        </td>
                                        <td class="text-center"><?php echo $course_detail['units']; ?></td>
                                        <td class="text-center"><?php echo $section_name_concatenated; ?></td>
                                        <td>
                                            <div class="text-center">
                                                <?php
                                                $schedule_query = "SELECT DISTINCT s.room_id, r.room
                                                                    FROM schedules s
                                                                    INNER JOIN rooms r ON s.room_id = r.id
                                                                    WHERE s.course_offering_info_id = ? AND s.faculty_id = $instructor";
                                                $schedule_stmt = $conn->prepare($schedule_query);
                                                $schedule_stmt->bind_param('i', $schedule['course_offering_info_id']);
                                                $schedule_stmt->execute();
                                                $schedule_result = $schedule_stmt->get_result();
                                                while ($schedule_row = $schedule_result->fetch_assoc()) {
                                                    echo $schedule_row['room'] . "<br>";
                                                }

                                                $schedule_time_query = "SELECT DISTINCT time_start, time_end, room_id 
                                                                        FROM schedules 
                                                                        WHERE course_offering_info_id = ? AND faculty_id = $instructor";
                                                $schedule_time_stmt = $conn->prepare($schedule_time_query);
                                                $schedule_time_stmt->bind_param('i', $schedule['course_offering_info_id']);
                                                $schedule_time_stmt->execute();
                                                $schedule_time_result = $schedule_time_stmt->get_result();
                                                while ($schedule_time = $schedule_time_result->fetch_assoc()) {
                                                    $day_query = "SELECT day 
                                                                    FROM schedules 
                                                                    WHERE course_offering_info_id = ? 
                                                                    AND time_start = ? 
                                                                    AND time_end = ? 
                                                                    AND room_id = ?";
                                                    $day_stmt = $conn->prepare($day_query);
                                                    $day_stmt->bind_param('isss', $schedule['course_offering_info_id'], $schedule_time['time_start'], $schedule_time['time_end'], $schedule_time['room_id']);
                                                    $day_stmt->execute();
                                                    $day_result = $day_stmt->get_result();
                                                    $days = [];
                                                    while ($day = $day_result->fetch_assoc()) {
                                                        $days[] = $day['day'];
                                                    }
                                                    echo "[" . implode("", $days) . " " . date('g:iA', strtotime($schedule_time['time_start'])) . "-" . date('g:iA', strtotime($schedule_time['time_end'])) . "]<br>";
                                                }
                                                ?>

                                            </div>
                                        </td>
                                        <?php if ($course_detail['department_id'] == $department_id): ?>
                                            <td class="text-center align-middle">
                                                <button class="btn btn-danger btn-flat remove_faculty_load" data-offering_id="<?php echo $schedule['course_offering_info_id']; ?>">
                                                    <i class="fa fa-times"></i>
                                                </button>

                                            </td>
                                        <?php else : ?>
                                            <td>

                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if ($faculty_program == $department_id): ?>
                            <div class="col-sm-12">
                                <a onclick="window.location.href='index.php?page=reportAjax/teaching_load&faculty_id=<?php echo urlencode($instructor); ?>'" class="btn btn-primary btn-block">View Faculty Workload</a>
                            </div>
                        <?php endif; ?>

                    <?php else : ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Course</th>
                                    <th class="text-center">Units</th>
                                    <th class="text-center">Section</th>
                                    <th class="text-center">Schedule</th>
                                    <th class="text-center align-middle">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td colspan="5" class="text-center"> No Faculty Loading Found!</td>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.remove_faculty_load').click(function() {
        _conf("Are you sure you want to unload the course from this instructor?", "remove_faculty_load", [$(this).attr('data-offering_id')]);
    });

    function remove_faculty_load(offering_id) {
        var array = {};
        array['offering_id'] = offering_id;
        array['instructor'] = "<?php echo $instructor; ?>";
        $.ajax({
            type: "GET",
            url: "ajax.php?action=remove_faculty_load",
            data: array,
            success: function(data) {
                var response = JSON.parse(data);
                if (response.success) {
                    displayCourses('<?php echo $level; ?>', "<?php echo $instructor; ?>");
                    getCurrentLoad("<?php echo $instructor; ?>", '<?php echo $level; ?>');
                    alert_toast(response.message, 'success');
                } else {
                    alert_toast(response.message, 'danger');
                }
            }
        });
    }
</script>