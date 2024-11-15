<?php
require '../vendor/autoload.php';
include '../db_connect.php';

session_start();

if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
    $head_planning = $_GET['head_planning'];
    $cd_signature = $_GET['cd_signature'];
    $program_id = $_SESSION['login_program_id'];
    $program_dept =  $_GET['program_dept'];

    $program_query = "SELECT * FROM program WHERE id = ?";
    if ($program_stmt = $conn->prepare($program_query)) {
        $program_stmt->bind_param("i", $program_id);
        $program_stmt->execute();
        $program_result = $program_stmt->get_result();
        $program = $program_result->fetch_assoc();
        $program_stmt->close();
    }

    $head_query = "SELECT * FROM faculty WHERE designation = 1";
    $head_result = mysqli_query($conn, $head_query);
    $head = $head_result->fetch_assoc();

    $school_year_query = "SELECT YEAR(start_date) as year_only FROM semester WHERE sem_name = '1st Semester'";
    $school_year_result = mysqli_query($conn, $school_year_query);

    if ($school_year_result && $school_year_row = $school_year_result->fetch_assoc()) {
        $start_year = $school_year_row['year_only'];
        $next_year = $start_year + 1;
        $school_year = $start_year . '-' . $next_year;
    } else {
        $school_year = "Year not found";
    }

    $room_query = "SELECT * FROM rooms WHERE id = ?";
    if ($room_stmt = $conn->prepare($room_query)) {
        $room_stmt->bind_param("i", $room_id);
        $room_stmt->execute();
        $room_result = $room_stmt->get_result();
        $room = $room_result->fetch_assoc();
        $room_stmt->close();
    }

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
                'displayStart' => $start->format('h:i'),
                'displayEnd'   => $next->format('h:i')
            ];
            $start = $next;
        }

        return $times;
    }

    // Function to fetch schedules
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

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $room_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $schedules = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $schedules;
        } else {
            die('Error preparing schedule query.');
        }
    }

    // Function to generate light color
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

    $mpdf = new \Mpdf\Mpdf(['orientation' => 'L', 'format' => 'A3']);
    $mpdf->SetMargins(12.7, 12.7, 12.7);
    $mpdf->SetAutoPageBreak(true, 12.7);
    $mpdf->AddPage();

    $imageWidth = 25;
    $imageHeight = 25;

    $leftImageX = 110;
    $rightImageX = 310 - $imageWidth;

    $mpdf->Image('img/1-removebg-preview.jpeg', $leftImageX, 12.4, $imageWidth, $imageHeight, 'jpeg', '', true, false);
    $mpdf->Image('img/Bagong_Pilipinas_logo.jpeg', $rightImageX, 12.4, $imageWidth, $imageHeight, 'jpeg', '', true, false);

    $html = <<<EOD
<style>
    .header-text {
        position: absolute;
        top: 30px;
        left: 0;
        right: 0;
        text-align: center;
        line-height: 1.5;
        z-index: 1;
    }
    .large-text {
        font-size: 16px;
    }
    .medium-text {
        font-size: 14px;
    }
    .small-text {
        font-size: 12px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black;
        padding: 5px;
        text-align: center;
        font-size: 10pt;
    }
    .signatures{
        margin-top: 20px
    }
</style>

<!-- Header Text -->
<div class="header-text">
    <h6 class="large-text">Republic of the Philippines</h6>
    <h6 class="large-text"><b>EASTERN VISAYAS STATE UNIVERSITY CARIGARA CAMPUS</b></h6>
    <h6 class="medium-text">Carigara, Leyte</h6>
    <h6 class="medium-text"><b>{$program['department']}</b></h6>
    <h5 class="large-text"><b>ROOM UTILIZATION</b></h5>
EOD;

    $currentDate = date('Y-m-d');
    $query = "SELECT * FROM semester WHERE start_date <= '$currentDate' AND end_date >= '$currentDate' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $html .= '<h6 class="medium-text">' . strtoupper($row['sem_name']) . ', AY: ' . $school_year . '</h6>';
        }
    } else {
        $html .= '<h6 class="medium-text">No semester information found.</h6>';
    }

    $html .= <<<EOD
    <br><br>
</div>

<h6 class="medium-text" style='line-height: 0.1; margin-top: 110px;'><b>Course:</b> {$program['program_name']}</h6>
<h6 class="medium-text" style='line-height: 0.1;'><b>Room:</b> {$room['room']}</h6>

<table style='width: 100%; table-layout: fixed;'>
    <thead>
        <tr>
            <th style='width: 12%;'>Time</th>
EOD;

    foreach ($weekDays as $day) {
        $html .= "<th style='width: 12%;'>{$day}</th>";
    }
    $html .= "<th style='width: 12%;'>Time</th>";
    $html .= "</tr></thead><tbody>";

    foreach ($calendarData as $time => $days) {
        $html .= "<tr>";
        $html .= "<td class='text-center p-2' style='font-size:0.9rem;'>{$time}</td>";

        foreach ($days as $dayCode => $value) {
            if (isset($value['rowspan'])) {
                $html .= "<td rowspan='{$value['rowspan']}' class='align-middle text-center' style='background-color:{$value['background_color']}; color: #000;' data-schedule-id='{$value['schedule_id']}'>";
                $html .= "{$value['course_name']}<br>";
                $html .= "<b class='text-uppercase'><small>{$value['section_name']}</small><br>({$value['faculty_name']})</b>";
                $html .= "</td>";
            } else {
                $html .= "<td></td>";
            }
        }

        $html .= "<td class='text-center p-2' style='font-size:0.9rem;'>{$time}</td>";
        $html .= "</tr>";
    }

    $html .= "</tbody></table>
    <div class='signatures'>
        <table width='100%' style='margin-top: 10px; border: none;'>
        <tr>
            <td width='33.33%' style='padding: 0 30px 30px; border: none; text-align: left;'>
                <p style='font-style: italic;'>Prepared by:</p>  
            </td>
            
            <td width='33.33%' style='padding: 0 30px 30px; border: none; text-align: left;'>
                <p style='font-style: italic;'>Noted:</p>  
            </td>
            
            <td width='33.33%' style='padding: 0 30px 30px; border: none; text-align: left;'>
                <p style='font-style: italic;'>Approved by:</p>  
            </td>
        </tr>
        <tr>
            <td style='padding: 0 10px; border: none;'>
                <div style='text-align: center;'>
                    <strong>" . strtoupper($head['fname']) . " " . (!empty($head['mname']) ? strtoupper(substr($head['mname'], 0, 1)) . ". " : "") . strtoupper($head['lname']) . ', ' . strtoupper($head['post_graduate_studies']) . "</strong>
                </div>
                <small>Head, " . htmlspecialchars($program_dept) . "</small>
            </td>
            
            <td style='padding: 0 10px; border: none;'> 
                <div style='text-align: center;'>
                    <strong>" . strtoupper($head_planning) . "</strong>
                </div>
                <small>Head, Planning and Development Unit</small>
            </td>
            
            <td style='padding: 0 10px; border: none;'> 
                <div style='text-align: center;'>
                    <strong>" . strtoupper($cd_signature) . "</strong>
                </div>
                <small>Campus Director</small>
            </td>
        </tr>
    </table>
    </div>
</div>";

    $room_name = strtoupper($room['room']);
    $filename = 'room_utilization_report(' . $room_name . ').pdf';

    // Output the PDF
    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, 'D');
}
