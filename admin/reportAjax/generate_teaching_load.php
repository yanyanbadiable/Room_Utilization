<?php
include '../db_connect.php';
require '../vendor/autoload.php';

use Mpdf\Mpdf;

if (isset($_GET['faculty_id'])) {
    $faculty_id = $_GET['faculty_id'];
    $administrative_hours = $_GET['administrative_hours'];
    $research_hours = $_GET['research_hours'];
    $extension_hours = $_GET['extension_hours'];
    $consultation_hours = $_GET['consultation_hours'];
    $instructional_hours = $_GET['instructional_hours'];
    $other_hours = $_GET['other_hours'];
    $cd_signature = $_GET['cd_signature'];
    $vpaa_signature = $_GET['vpaa_signature'];
    $up_signature = $_GET['up_signature'];
    $acad_year = $_GET['acad_year'];
    $program_dept = $_GET['program_dept'];

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

    // Fetch regular schedules for the faculty with combined days
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

    // Fetch overload/part-time schedules for the faculty
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

    // Initialize mPDF
    $mpdf = new Mpdf(['orientation' => 'L', 'format' => 'A4']);
    $mpdf->SetMargins(12.7, 12.7, 12.7);
    $mpdf->SetAutoPageBreak(true, 12.7);
    $mpdf->AddPage();

    // HTML content
    $html = '
    <style>
        *{
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse; 
        }
        th, td {
            border: 1px solid black;
            padding-top: 1px;
            text-align: center;
            vertical-align: middle;
        }
        .text-left{
            text-align: left;
        }
        .university-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .university-location {
            font-size: 12pt;
            margin-bottom: 0;
        }

        .control-info {
            font-size: 11pt;
            padding: 0;
        }
        .clearfix {
            clear: both;
        }
        .underline-text {
            border-bottom: 1px solid black;
            padding-left: 10px;
            padding-right: 10px;
        }
        .school-involvement{
            margin-top: 10px;
        }
        .school-involvement, .signatures, signature-section {
            font-size: 9pt;
        }
        .school-involvement p, .signatures p {
            margin: 5px 0;
        }
        small {
            font-size: 10pt;
        }

        .signatures {
            margin-top: 5px;
        }

        .signatures strong {
            font-size: 8pt;
            text-decoration: underline;
        }

        .signatures small {
            font-size: 8pt;
        }

        .signature-section {
            text-align: center;
            margin-top: 15px;
        }

        .signature-section strong {
            font-size: 8pt;
            text-decoration: underline;
        }

        .signature-section small {
            font-size: 8pt;
            display: block;
        }
    </style>

    <div>
        <!-- Header Section -->
        <table style="font-size: 10pt; border-collapse: collapse;">
            <tr>
                <td rowspan="4" >
                    <img src="img/1-removebg-preview.jpeg" alt="EVSU Logo" style="width: 80px; height: 80px;" class>
                </td>
                <td colspan="3" style="text-align: center;">
                    <h5 style="font-weight: bold; margin: 0;" class="university-name">EASTERN VISAYAS STATE UNIVERSITY</h5>
                    <p style="margin: 0;" class="university-location">Tacloban City</p>
                </td>
            </tr>
            <tr>
                <td rowspan="3" class="text-left" style="padding-left: 10px;">Title of Form: <span style=" font-weight: bold;">Teacher Workload Form</span></td>
                <td style="text-align: center;">Control No.</td>
                <td style="text-align: center;">EVSU-ACA-F-002</td>
            </tr>
            <tr>
                <td style="text-align: center;">Revision No.</td>
                <td style="text-align: center;">02</td>
            </tr>
            <tr>
                <td style="text-align: center;">Date</td>
                <td style="text-align: center;">February 20, 2023</td>
            </tr>
        </table>

        <!-- Faculty Information Section -->
        <table style="width: 100%; margin-top: 10px; font-size: 8pt;">
            <tr>
                <td style="width: 66%; vertical-align: top; text-align: left; border: none;">
                    <strong>Faculty Member:</strong> ' . strtoupper($faculty['fname']) . " " .
                    (!empty($faculty['mname']) ? strtoupper(substr($faculty['mname'], 0, 1)) . ". " : "") .
                    strtoupper($faculty['lname']) .
                    (!empty($faculty['post_graduate_studies']) ? ", " . strtoupper($faculty['post_graduate_studies']) : "") . '<br>
                    <strong>Academic Rank:</strong> ' . strtoupper($faculty['academic_rank']) . '<br>
                    <strong>College/Campus:</strong> CARIGARA CAMPUS
                </td>
                <td style="width: 34%; vertical-align: top; text-align: left; border: none;">
                    <strong>Semester:</strong> ' . strtoupper($semester_name) . '<br>
                    <strong>School Year:</strong> ' . $acad_year . '<br>
                    <strong>Designation:</strong> ' . strtoupper($faculty['designation']) . '
                </td>
            </tr>
        </table>

        <!-- Regular Workload Table -->
        <h6 style="margin-top: 10px; margin-bottom: 2px; font-size: 8pt;">REGULAR</h6>
        <table style="width: 100%; border: 1px solid black; font-size: 8pt;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th rowspan="2" style="width: 85px;">Course No.</th>
                    <th rowspan="2" style="width: 190px;">Descriptive Title</th>
                    <th rowspan="2" style="width: 95px;">Subject Units</th>
                    <th rowspan="2" style="width: 105px;">TIME</th>
                    <th rowspan="2" style="width: 60px;">DAYS</th>
                    <th colspan="2" style="width: 130px;">No. of Hrs/Week</th>
                    <th rowspan="2" style="width: 100px;">No. of Students</th>
                    <th rowspan="2" style="width: 80px;">Room No.</th>
                    <th rowspan="2" style="width: 110px;">Course, Yr., & Sec.</th>
                </tr>
                <tr style="background-color: #f2f2f2;">
                    <th style="width: 65px;">Lec</th>
                    <th style="width: 65px;">Lab</th>
                </tr>
            </thead>
            <tbody>';
    if (empty($regular_schedules)) {
        $html .= '<tr>
                            <td style="padding:9px;"></td>
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
                    <tr style="background-color: #f2f2f2;">
                        <td rowspan="2"></td>
                        <td rowspan="2"><strong>Total</strong></td>
                        <td rowspan="2">0</td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td>0</td>
                        <td>0</td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                        <td rowspan="2"></td>
                    </tr>
                    <tr style="background-color: #f2f2f2;">
                        <td colspan="2">0</td>
                    </tr>';
    } else {
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

            $html .= '<tr>
            <td>' . $course_detail['course_code'] . '</td>
            <td>' . $course_detail['course_name'] . '</td>';

            if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) {
                $html .= '<td>' . $course_detail['units'] . '</td>';
            } else {
                $html .= '<td></td>';
            }

            $html .= '<td>' . date('g:iA', strtotime($schedule['time_start'])) . ' - ' . date('g:iA', strtotime($schedule['time_end'])) . '</td>';

            if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) {
                $html .= '<td>' . implode('', $days_combined) . '</td>';
            } else {
                $html .= '<td>' . $schedule['combined_days'] . '</td>';
            }

            $html .= '<td>' . $display_lec_hours . '</td>
                      <td>' . $display_lab_hours . '</td>';

            if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) {
                $html .= '<td>' . $course_detail['no_of_students'] . '</td>
                          <td>' . $schedule['room'] . '</td>
                          <td>' . $section_name_concatenated . '</td>';
            } else {
                $html .= '<td></td>
                          <td></td>
                          <td></td>';
            }

            $html .= '</tr>';

            $previous_course_offering_info_id = $schedule['course_offering_info_id'];
        }

        $total_lec_lab = $total_lec_regular + $total_lab_regular;

        $html .= '<tr style="background-color: #f2f2f2;">
        <td rowspan="2" ></td>
        <td rowspan="2" class="text-center align-middle"><strong>Total</strong></td>
        <td rowspan="2">' . $total_units_regular . '</td>
        <td rowspan="2"></td>
        <td rowspan="2"></td>
        <td>' . $total_lec_regular . '</td>
        <td>' . $total_lab_regular . '</td>
        <td rowspan="2"></td>
        <td rowspan="2"></td>
        <td rowspan="2"></td>
    </tr>
    <tr style="background-color: #f2f2f2;">
        <td colspan="2">' . $total_lec_lab . '</td>
    </tr>';
    }

    $html .= '</tbody></table>
    <!-- Overload Workload Table -->
    <h6 style="margin-top: 5px; margin-bottom: 2px; font-size: 8pt;">OVERLOAD/PART-TIME</h6>
    <table style="width: 100%; border: 1px solid black; font-size: 8pt;">
        <thead>
                <tr style="background-color: #f2f2f2;">
                    <th rowspan="2" style="width: 85px;">Course No.</th>
                    <th rowspan="2" style="width: 190px;">Descriptive Title</th>
                    <th rowspan="2" style="width: 95px;">Subject Units</th>
                    <th rowspan="2" style="width: 105px;">TIME</th>
                    <th rowspan="2" style="width: 60px;">DAYS</th>
                    <th colspan="2" style="width: 130px;">No. of Hrs/Week</th>
                    <th rowspan="2" style="width: 100px;">No. of Students</th>
                    <th rowspan="2" style="width: 80px;">Room No.</th>
                    <th rowspan="2" style="width: 110px;">Course, Yr., & Sec.</th>
                </tr>
                <tr style="background-color: #f2f2f2;">
                    <th style="width: 65px;">Lec</th>
                    <th style="width: 65px;">Lab</th>
                </tr>
            </thead>
        <tbody>';
    if (empty($overload_schedules)) {
        $html .= '<tr>
                    <td style="padding:9px;"></td>
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
            <tr style="background-color: #f2f2f2;">
                <td rowspan="2"></td>
                <td rowspan="2"><strong>Total</strong></td>
                <td rowspan="2">0</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td>0</td>
                <td>0</td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
                <td rowspan="2"></td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td colspan="2">0</td>
            </tr>';
    } else {
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

            $html .= '<tr>
            <td>' . $course_detail['course_code'] . '</td>
            <td>' . $course_detail['course_name'] . '</td>';

            if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) {
                $html .= '<td>' . $course_detail['units'] . '</td>';
            } else {
                $html .= '<td></td>';
            }

            $html .= '<td>' . date('g:iA', strtotime($schedule['time_start'])) . ' - ' . date('g:iA', strtotime($schedule['time_end'])) . '</td>';

            if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) {
                $html .= '<td>' . implode('', $days_combined) . '</td>';
            } else {
                $html .= '<td>' . $schedule['combined_days'] . '</td>';
            }

            $html .= '<td>' . $display_lec_hours . '</td>
                      <td>' . $display_lab_hours . '</td>';

            if ($previous_course_offering_info_id !== $schedule['course_offering_info_id']) {
                $html .= '<td>' . $course_detail['no_of_students'] . '</td>
                          <td>' . $schedule['room'] . '</td>
                          <td>' . $section_name_concatenated . '</td>';
            } else {
                $html .= '<td></td>
                          <td></td>
                          <td></td>';
            }

            $html .= '</tr>';

            $previous_course_offering_info_id = $schedule['course_offering_info_id'];
        }

        $total_lec_lab = $total_lec_regular + $total_lab_regular;

        $html .= '<tr style="background-color: #f2f2f2;">
        <td rowspan="2" ></td>
        <td rowspan="2" class="text-center align-middle"><strong>Total</strong></td>
        <td rowspan="2">' . $total_units_regular . '</td>
        <td rowspan="2"></td>
        <td rowspan="2"></td>
        <td>' . $total_lec_regular . '</td>
        <td>' . $total_lab_regular . '</td>
        <td rowspan="2"></td>
        <td rowspan="2"></td>
        <td rowspan="2"></td>
    </tr>
    <tr style="background-color: #f2f2f2;">
        <td colspan="2">' . $total_lec_lab . '</td>
    </tr>';
    }

    $html .= '</tbody></table>
<!-- Other In-School Involvement/Assignment Section -->
<div class="school-involvement">
    <strong >Other In-School Involvement/Assignment Per Week:</strong>
    <table width="100%" cellpadding="10" style="margin-top: 5px;">
    <tr>
        <td width="25%" style="border: none; font-size: 9pt; text-align: right; vertical-align: top; padding-left: 10px; ">
            <p>Administrative: <strong>' . htmlspecialchars($administrative_hours) . ' </strong> Hours</p>
            <p>Research: <strong>' . htmlspecialchars($research_hours) . '</strong> Hours</p>
            <p>Extension Services: <strong>' . htmlspecialchars($extension_hours) . '</strong> Hours</p>
            <p>Consultation: <strong>' . htmlspecialchars($consultation_hours) . '</strong> Hours</p>
            <p>Instructional Functions: <strong>' . htmlspecialchars($instructional_hours) . '</strong> Hours</p>
            <p>Others (Specify): <strong>' . htmlspecialchars($other_hours) . '</strong> Hours</p>
        </td>
        <td width="25%" style="border: none;"></td>
        <td width="50%" style="border: none; font-size: 9pt; text-align: left; vertical-align: top;">
            <p>No. of Classes: <strong>' . htmlspecialchars($num_class) . '</strong></p>
            <p>No. of Preparation: <strong>' . htmlspecialchars($prep_class) . '</strong></p>
        </td>
    </tr>
    </table>

    <!-- Faculty Signature -->
    <div class="signature-section">
        <strong>' . strtoupper($faculty['fname']) . " " . (!empty($faculty['mname']) ? strtoupper(substr($faculty['mname'], 0, 1)) . ". " : "") . strtoupper($faculty['lname']) . ', ' . strtoupper($faculty['post_graduate_studies']) . '</strong><br>
        <small>Faculty</small>
    </div>
</div>

<!-- Signatures Section -->
<div class="signatures">
    <table width="100%" style="margin-top: 5px; text-align: center; border: none;">
        <tr>
            <td width="25%" style="border: none;">
                <strong>' . strtoupper($head['fname']) . " " . (!empty($head['mname']) ? strtoupper(substr($head['mname'], 0, 1)) . ". " : "") . strtoupper($head['lname']) . ', ' . strtoupper($head['post_graduate_studies']) . '</strong><br>
                <small>Head, ' . htmlspecialchars($program_dept) . '</small>
            </td>
            <td width="25%" style="border: none;">
                <strong>' . strtoupper($cd_signature) . '</strong><br>
                <small>Campus Director</small>
            </td>
            <td width="25%" style="border: none;">
                <strong>' . strtoupper($vpaa_signature) . '</strong><br>
                <small>Vice President for Academic Affairs</small>
            </td>
            <td width="25%" style="border: none;">
                <strong>' . strtoupper($up_signature) . '</strong><br>
                <small>University President</small>
            </td>
        </tr>
    </table>
</div>
';
    $faculty_full_name = strtoupper($faculty['fname']) . '_' . strtoupper($faculty['lname']);
    $filename = 'faculty_workload(' . $faculty_full_name . ').pdf';
    // Output PDF
    $mpdf->WriteHTML($html);
    $mpdf->Output($filename, 'D');
}
