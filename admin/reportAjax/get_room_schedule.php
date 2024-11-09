<?php
include '../db_connect.php';

session_start();

if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
    $program_id = $_SESSION['login_program_id'];

    $program_query = "SELECT * FROM program WHERE id = ?";
    $program_stmt = $conn->prepare($program_query);
    $program_stmt->bind_param("i", $program_id);
    $program_stmt->execute();
    $program_result = $program_stmt->get_result();
    $program = $program_result->fetch_assoc();

    $room_query = "SELECT rooms.*, program.department FROM rooms INNER JOIN program ON rooms.program_id = program.id WHERE rooms.id = ?";
    $room_stmt = $conn->prepare($room_query);
    $room_stmt->bind_param("i", $room_id);
    $room_stmt->execute();
    $room_result = $room_stmt->get_result();
    $room = $room_result->fetch_assoc();

    $head_query = "SELECT * FROM faculty WHERE designation = 1";
    $head_result = mysqli_query($conn, $head_query);
    $head = $head_result->fetch_assoc();

    // Timetable

    $weekDays = [
        'M' => 'Monday',
        'T' => 'Tuesday',
        'W' => 'Wednesday',
        'Th' => 'Thursday',
        'F' => 'Friday',
        'S' => 'Saturday',
        'Su' => 'Sunday'
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

    function fetchSchedules($room_id)
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
            room_id = ? AND
            s.day IN ('M', 'T', 'W', 'Th', 'F', 'S') AND
            s.time_start BETWEEN '07:00' AND '19:00'
    ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $room_id);
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

    function generateCalendarData($weekDays, $room_id)
    {
        $calendarData = [];
        $timeRange = generateTimeRange('07:00', '19:00');
        $schedules = fetchSchedules($room_id);
        $skipSlots = [];
        $facultyColors = [];

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

                        if (!isset($facultyColors[$schedule['faculty_id']])) {
                            $facultyColors[$schedule['faculty_id']] = generateLightColor($schedule['faculty_id']);
                        }

                        $calendarData[$time['displayStart'] . ' - ' . $time['displayEnd']][$dayCode] = [
                            'course_name' => $schedule['course_name'],
                            'faculty_name' => $facultyName,
                            'section_name' => $sectionNameConcatenated,
                            'rowspan' => $duration,
                            'schedule_id' => $schedule['id'],
                            'room_id' => $room_id,
                            'background_color' => $facultyColors[$schedule['faculty_id']]
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
    $calendarData = generateCalendarData($weekDays, $room_id);
}
?>

<style>
    #header {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }

    #header img {
        /* width: 100px; */
        height: 100px;
    }

    .header-text {
        margin: 0;
    }

    .form {
        margin-top: 1rem;
    }

    .underline-text {
        text-decoration: underline;
    }

    .underlined {
        border: none;
        border-bottom: 1px solid #858796;
        width: auto;
        text-align: center;
    }

    .header-text {
        text-align: center;
        font-size: 15px;
    }

    .sub-header-text {
        text-align: center;
        font-size: 12px;
    }

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

<div class="card shadow p-4 mb-4" id="card-container">
    <div id="header" class="mb-3">
        <img src="../assets/img/1-removebg-preview.jpeg" alt="" class="mr-4">
        <div class="text-container">
            <div class="header-text">
                <h6 class="m-1">Republic of the Philippines</h6>
                <h6><b>EASTERN VISAYAS STATE UNIVERSITY CARIGARA CAMPUS</b></h6>
                <h6 class="mb-1">Carigara, Leyte</h6>
                <h6><b class="text-uppercase"><?php echo $room['department'] ?></b></h6>
            </div>
            <div class="header-text">
                <h5><b>ROOM UTILIZATION</b></h5>
            </div>
            <div class="sub-header-text">
                <?php
                $currentMonthDay = date('m-d');
                $query = " 
                    SELECT * FROM semester 
                    WHERE 
                        DATE_FORMAT(start_date, '%m-%d') <= '$currentMonthDay' 
                        AND DATE_FORMAT(end_date, '%m-%d') >= '$currentMonthDay'
                    LIMIT 1";
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo "No Active Semester";
                } else {
                    while ($row = $result->fetch_assoc()) :
                ?>
                        <h6 style="text-transform: uppercase;">
                            <?php echo $row['sem_name'] ?>, AY: <b>
                                <input type="text" name="acad_year" id="acad_year" value="" size="9" class="underlined">
                            </b>
                        </h6>
                <?php
                    endwhile;
                }
                ?>
            </div>
        </div>
        <img src="../assets/img/Bagong_Pilipinas_logo.jpeg" alt="" class="ml-4">
    </div>
    <div class="form form-group mb-2">
        <h6><b>Course:</b> <?php echo $program['department'] ?></h6>
        <h6 class="m-0"><b>Room:</b> <?php echo $room['room']  ?></h6>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="text-center">
                    <th width="125" class="p-1">Time</th>
                    <?php foreach ($weekDays as $day) : ?>
                        <th class="p-1"><?php echo $day; ?></th>
                    <?php endforeach; ?>
                    <th width="125" class="p-1">Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($calendarData as $time => $days) : ?>
                    <tr>
                        <td class="text-center p-1 align-middle" style="font-size:0.8rem;"><?php echo $time; ?></td>
                        <?php foreach ($days as $dayCode => $value) : ?>
                            <?php if (isset($value['rowspan'])) : ?>
                                <td rowspan="<?php echo $value['rowspan']; ?>" class="align-middle text-center" style="font-size:0.8rem; background-color:<?php echo $value['background_color']; ?>; color: #000; " data-schedule-id="<?php echo $value['schedule_id']; ?>">
                                    <?php echo $value['course_name']; ?><br>
                                    <b class="text-uppercase">
                                        <small><?php echo $value['section_name']; ?></small><br>
                                        (<?php echo $value['faculty_name']; ?>)
                                    </b>
                                </td>
                            <?php else : ?>
                                <td></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <td class="text-center p-1 align-middle" style="font-size:0.8rem;"><?php echo $time; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <?php if ($room['program_id'] == $program_id): ?>
        <div class="row">
            <div class="col-md-4">
                <h6 style="font-style:italic;" class="text-left mb-3">Prepared by:</h6>
                <div class="text-center">
                    <strong class="underlined">
                        <?php echo strtoupper($head['fname']) . " " .
                            (!empty($head['mname']) ? strtoupper(substr($head['mname'], 0, 1)) . ". " : "") .
                            strtoupper($head['lname']) . ", " . strtoupper($head['post_graduate_studies']); ?>
                    </strong><br>
                    <small>Head, <?php echo $program['department']; ?></small>
                </div>
            </div>
            <div class="col-md-4">
                <h6 style="font-style:italic;" class="text-left mb-3">Noted:</h6>
                <div class="text-center">
                    <strong>
                        <input type="text" name="hpdu" id="hpdu" value="" size="30" class="underlined">
                    </strong><br>
                    <small>Head, Planning and Development Unit</small>
                </div>
            </div>
            <div class="col-md-4">
                <h6 style="font-style:italic;" class="text-left mb-3">Approved by:</h6>
                <div class="text-center">
                    <strong>
                        <input type="text" name="cd_signature" id="cd_signature" value="" size="30" class="underlined">
                    </strong><br>
                    <small>Campus Director</small>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php if ($room['program_id'] == $program_id): ?>
    <button class="btn btn-block btn-success shadow mb-4" onclick="generatePDF()">Generate PDF</button>
<?php endif; ?>
<script>
    function generatePDF() {
        var head_planning = document.getElementById('hpdu').value;
        var cd_signature = document.getElementById('cd_signature').value;
        var acad_year = document.getElementById('acad_year').value;

        if (!cd_signature || !head_planning || !acad_year) {
            window.location.href = "#page-top";
            alert_toast("Please fill in all fields.", 'warning');
            return;
        }

        var program_dept = "<?php echo $program['department']; ?>";

        var url = 'reportAjax/generate_schedule.php?room_id=<?php echo $room_id; ?>' +
            '&program_dept=' + encodeURIComponent(program_dept) +
            '&head_planning=' + encodeURIComponent(head_planning) +
            '&acad_year=' + encodeURIComponent(acad_year) +
            '&cd_signature=' + encodeURIComponent(cd_signature);

        window.location.href = url;
    }
</script>