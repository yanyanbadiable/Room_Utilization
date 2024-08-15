<?php
// // Start output buffering
// ob_start();

require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include 'db_connect.php';

// Check if 'room_id' is set in the query string
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
    $program_id = $_SESSION['login_program_id'];

    $program_query = "SELECT * FROM program WHERE id = ?";
    $program_stmt = $conn->prepare($program_query);
    $program_stmt->bind_param("i", $program_id);
    $program_stmt->execute();
    $program_result = $program_stmt->get_result();
    $program = $program_result->fetch_assoc();

    $room_query = "SELECT * FROM rooms WHERE id = ?";
    $room_stmt = $conn->prepare($room_query);
    $room_stmt->bind_param("i", $room_id);
    $room_stmt->execute();
    $room_result = $room_stmt->get_result();
    $room = $room_result->fetch_assoc();

    // Timetable functions
    $weekDays = [
        'M' => 'Monday',
        'T' => 'Tuesday',
        'W' => 'Wednesday',
        'Th' => 'Thursday',
        'F' => 'Friday',
        'S' => 'Saturday'
    ];

    function generateTimeRange($startTime, $endTime) {
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

    function fetchSchedules($room_id) {
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
            s.time_start BETWEEN '08:00' AND '19:00'
    ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function generateLightColor($seed) {
        srand($seed);
        $r = mt_rand(127, 255);
        $g = mt_rand(127, 255);
        $b = mt_rand(127, 255);
        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }

    function generateCalendarData($weekDays, $room_id) {
        $calendarData = [];
        $timeRange = generateTimeRange('08:00', '19:00');
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

<!DOCTYPE html>
<html>
<head>
    <title>Room Utilization Report</title>
    <style>
        /* Add your styles here */
        #header {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        #header img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            object-position: center;
            margin-inline: 2rem;
        }

        .header-text {
            margin: 0;
        }

        .form {
            margin-top: 1rem;
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
            border-collapse: collapse;
        }

        .table th,
        .table td {
            min-width: 14.28%;
            max-width: 14.28%;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
<div id="card-container">
    <div id="header" class="mb-3">
        <img src="../assets/img/1-removebg-preview.png" alt="">
        <div class="text-container">
            <div class="header-text">
                <h6 class="m-1">Republic of the Philippines</h6>
                <h6><b>EASTERN VISAYAS STATE UNIVERSITY CARIGARA CAMPUS</b></h6>
                <h6 class="mb-1">Carigara, Leyte</h6>
                <h6><b class="text-uppercase"><?php echo $program['department'] ?></b></h6>
            </div>
            <div class="header-text">
                <h5><b>ROOM UTILIZATION</b></h5>
            </div>
            <div class="sub-header-text">
                <?php
                $currentDate = date('Y-m-d');
                $query = "SELECT * FROM semester WHERE start_date <= '$currentDate' AND end_date >= '$currentDate' LIMIT 1";
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo "No Active Semester";
                } else {
                    while ($row = $result->fetch_assoc()) :
                ?>
                        <h6 style="text-transform: uppercase;"><?php echo $row['sem_name'] ?>, AY: <b>
                                <?php
                                $currentYear = date("Y");
                                $nextYear = $currentYear + 1;
                                echo "$currentYear-$nextYear";
                                ?>
                            </b></h6>
                <?php
                    endwhile;
                }
                ?>
            </div>
        </div>
        <img src="../assets/img/Bagong_Pilipinas_logo.png" alt="">
    </div>
    <div class="form form-group mb-2">
        <h6><b>Course:</b> <?php echo $program['department'] ?></h6>
        <h6 class="m-0"><b>Room:</b> <?php echo $room['room']  ?></h6>
    </div>
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
                                <td rowspan="<?php echo $value['rowspan']; ?>" class="align-middle text-center clickable" style="background-color:<?php echo $value['background_color']; ?>; color: #000; " data-schedule-id="<?php echo $value['schedule_id']; ?>">
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <br>
    <br>
    <h6>Prepared by:</h6>
    <h5 class="font-weight-bold">____________________________</h5>
</div>

<?php
// End capturing HTML output
$html = ob_get_clean();

// Initialize Dompdf with custom options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Load HTML to Dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF (1. stream to the browser, 2. download)
$dompdf->stream("Room_Utilization.pdf", ["Attachment" => false]);

// You can also use "Attachment" => true to force download
?>
</body>
</html>
