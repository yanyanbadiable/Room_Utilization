<style>
    #header {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }

    #header img {
        width: 100px;
        height: 100px;
        margin-right: 2rem;
    }

    .header-text {
        text-align: center;
        margin: 0;
        font-size: 15px;
    }

    .sub-header-text {
        text-align: center;
        font-size: 12px;
    }

    /* Print styles */
    @media print {
        #calendar {
            /* display: block !important; */
            width: 100% !important;
            margin: 0 auto;
        }

        .no-print {
            display: none;
        }

        .fc-toolbar {
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

    }
</style>
<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor'])) {
    $instructor = $_GET['instructor'];

    $query = "SELECT DISTINCT * FROM schedules WHERE is_active = 1 AND users_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $instructor);
    $stmt->execute();
    $result = $stmt->get_result();

    $schedules = array();
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }

    $event_array = [];
    if (!empty($schedules)) {
        foreach ($schedules as $sched) {
            $course_detail_query = "
        SELECT 
        courses.course_code, 
        rooms.room AS room, 
        sections.section_name
        FROM courses 
        JOIN course_offering_info ON course_offering_info.courses_id = courses.id
        JOIN schedules ON schedules.course_offering_info_id = course_offering_info.id
        JOIN rooms ON rooms.id = schedules.room_id
        JOIN sections ON sections.id = course_offering_info.section_id
        WHERE course_offering_info.id = ?";

            $course_detail_stmt = $conn->prepare($course_detail_query);
            $course_detail_stmt->bind_param('i', $sched['course_offering_info_id']);
            $course_detail_stmt->execute();
            $course_detail_result = $course_detail_stmt->get_result();

            if (!$course_detail_result) {
                die('Query Error: ' . mysqli_error($conn));
            }
            $course_detail = mysqli_fetch_assoc($course_detail_result);

            $day_map = [
                'M' => 'Monday',
                'T' => 'Tuesday',
                'W' => 'Wednesday',
                'Th' => 'Thursday',
                'F' => 'Friday',
                'Sa' => 'Saturday',
                'Su' => 'Sunday'
            ];

            $color_map = [
                'M' => 'LightSalmon',
                'T' => 'lightblue',
                'W' => 'LightSalmon',
                'Th' => 'lightblue',
                'F' => 'LightSalmon',
                'Sa' => 'LightSalmon',
                'Su' => 'LightSalmon'
            ];

            $day = $day_map[$sched['day']] ?? '';
            $color = $color_map[$sched['day']] ?? '';

            $event_array[] = [
                'id' => $sched['id'],
                'title' => $course_detail['course_code'] . '<br>' . $course_detail['room'] . '<br>' . $course_detail['section_name'],
                'start' => date('Y-m-d', strtotime('next ' . $day)) . 'T' . $sched['time_start'],
                'end' => date('Y-m-d', strtotime('next ' . $day)) . 'T' . $sched['time_end'],
                'color' => $color,
                'textColor' => 'black',
                'extendedProps' => [
                    'course_offering_info_id' => $sched['course_offering_info_id']
                ]
            ];
        }
    }

    $get_schedule = json_encode($event_array);

    $faculty_query = "SELECT * FROM users WHERE id = ?";
    $faculty_stmt = $conn->prepare($faculty_query);
    $faculty_stmt->bind_param("i", $instructor);
    $faculty_stmt->execute();
    $faculty_result = $faculty_stmt->get_result();
    $faculty = $faculty_result->fetch_assoc();

    $info_query = "SELECT * FROM faculty WHERE user_id = ?";
    $info_stmt = $conn->prepare($info_query);
    $info_stmt->bind_param("i", $instructor);
    $info_stmt->execute();
    $info_result = $info_stmt->get_result();
    $info = $info_result->fetch_assoc();
} else {
    $get_schedule = NULL;
}
?>

<div class="card shadow p-4">
    <div id="printableArea">
        <div id="header" class="mb-3">
            <img src="../assets/img/1-removebg-preview.png" alt="">
            <div class="tex-container">
                <div class="header-text">
                    <p class="m-1">Republic of the Philippines</p>
                    <h6><b>EASTERN VISAYAS STATE UNIVERSITY CARIGARA CAMPUS</b></h6>
                    <p class="m-1">Carigara, Leyte</p>
                </div>
                <div class="header-text">
                    <h5><b>Class/Faculty Schedule</b></h5>
                </div>
                <div class="sub-header-text">
                    <h6>Second SEMESTER AY: <b><?php
                                                $currentYear = date("Y");
                                                $nextYear = $currentYear + 1;
                                                echo "$currentYear-$nextYear";
                                                ?></b></h6>
                </div>
            </div>
        </div>
        <div class="form form-group">
            <b>Instructor Name:</b> <?php echo $faculty['fname'] . ' ' . $faculty['lname']; ?>
        </div>
        <div id="calendar">
            <br><br>
        </div>
    </div>
    <button class="no-print btn btn-block btn-success" onclick="printSchedule()">Print Schedule</button>
</div>

<script>
    var calendarEl = document.getElementById('calendar');
    var events = <?php echo $get_schedule; ?>;
    var calendar = new FullCalendar.Calendar(calendarEl, {
        height: "auto",
        dayHeaderFormat: {
            weekday: 'short'
        },
        initialView: 'timeGridWeek',
        hiddenDays: [0],
        slotMinTime: '07:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        headerToolbar: false,
        eventOverlap: false,
        events: events,
        eventDidMount: function(info) {
            var titleElement = info.el.querySelector('.fc-event-title');
            if (titleElement) {
                titleElement.innerHTML = info.event.title;
            }
            info.el.classList.add('custom-event');
        }
    });
    calendar.render();

    function printSchedule() {
        printJS({
            printable: 'printableArea',
            type: 'html',
            style: `
             @page {
                        size: landscape;
                        margin: 10mm;
                    }
                    #header {
                        display: flex;
                        justify-content: center;
                        margin-bottom: 1rem;
                    }
                    #header img {
                        width: 100px;
                        height: 100px;
                        margin-right: 2rem;
                    }
                    .header-text {
                        text-align: center;
                        margin: 0;
                        font-size: 15px;
                    }
                    .sub-header-text {
                        text-align: center;
                        font-size: 12px;
                    }
                    .fc-time {
                        font-size: 6pt !important;
                    }
                    .fc-title {
                        font-size: 7pt;
                    }
                    .fc th {
                        border: 1px solid black;
                        border-collapse: collapse;
                    }
                    .fc-today {
                        background-color: inherit !important;
                    }
                    .fc-ltr .fc-axis {
                        text-align: center;
                    }
                    .fc tr:nth-child(even) {
                        background-color: #f2f2f2;
                        background-position: bottom;
                    }
                `
        });
    }
</script>