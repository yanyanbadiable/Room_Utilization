<?php
include '../db_connect.php';

// Check if the request method is GET and if instructor and level are set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['level'])) {

    // Get instructor and level from the GET parameters
    $instructor =  $_GET['instructor'];
    $level = $_GET['level'];

    $loads_query = "
        SELECT courses.*, course_offering_info.*, schedules.* 
        FROM courses 
        INNER JOIN course_offering_info ON courses.id = course_offering_info.courses_id 
        INNER JOIN schedules ON schedules.course_offering_info_id = course_offering_info.id 
        WHERE schedules.users_id = '$instructor'
    ";
    $loads_result = mysqli_query($conn, $loads_query);

    if (!$loads_result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    $loads = [];
    while ($row = mysqli_fetch_assoc($loads_result)) {
        $loads[] = $row;
    }

    $tabular_schedules_query = "
        SELECT DISTINCT course_offering_info_id 
        FROM schedules 
        WHERE is_active = 1 AND users_id = '$instructor'
    ";
    $tabular_schedules_result = mysqli_query($conn, $tabular_schedules_query);

    if (!$tabular_schedules_result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    $tabular_schedules = [];
    while ($row = mysqli_fetch_assoc($tabular_schedules_result)) {
        $tabular_schedules[] = $row;
    }

    $schedules_query = "
        SELECT * 
        FROM schedules 
        WHERE is_active = 1 AND users_id = '$instructor'
    ";
    $schedules_result = mysqli_query($conn, $schedules_query);

    if (!$schedules_result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    $schedules = [];
    while ($row = mysqli_fetch_assoc($schedules_result)) {
        $schedules[] = $row;
    }
}

if (!empty($schedules)) {
    $event_array = [];
    foreach ($schedules as $sched) {
        // Retrieve course and room details
        $course_detail_query = "
            SELECT courses.course_code, course_offering_info.section_id, rooms.room 
            FROM courses 
            INNER JOIN course_offering_info ON course_offering_info.courses_id = courses.id 
            INNER JOIN rooms ON rooms.id = '{$sched['room_id']}'
            WHERE course_offering_info.id = '{$sched['course_offering_info_id']}'
        ";
        $course_detail_result = mysqli_query($conn, $course_detail_query);
        $course_detail = mysqli_fetch_assoc($course_detail_result);

        // Determine color based on day
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

        // Add event details to event array
        $event_array[] = [
            'id' => $sched['id'],
            'title' => $course_detail['course_code'] . '<br>' . $course_detail['room'] . '<br>' . $course_detail['section_id'],
            'start' => date('Y-m-d', strtotime($day . ' this week')) . 'T' . $sched['time_start'],
            'end' => date('Y-m-d', strtotime($day . ' this week')) . 'T' . $sched['time_end'],
            'color' => $color,
            'textColor' => 'black',
            'course_offering_info_id' => $sched['course_offering_info_id']
        ];
    }

    $get_schedule = json_encode($event_array);
} else {
    $get_schedule = NULL;
}

$designation_query = "SELECT * FROM faculty WHERE user_id = '$instructor'";
$designation_result = mysqli_query($conn, $designation_query);
$designation = mysqli_fetch_assoc($designation_result);

?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.11/index.global.min.js"></script>

<div class="card shadow mb-4 px-3">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs d-flex">
            <li class="mr-auto header mr-auto py-3">
                <h4><i class="fa fa-calendar"></i>
                    <span>Faculty Loading <b>(<?php echo $designation['designation']; ?>)</b></span><br>
                    <span class="small m-0">Total No. of Units Loaded: <?php echo array_sum(array_column($loads, 'units')); ?></span>
                </h4>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#tab_1-1" data-toggle="tab">Calendar View</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tab_2-2" data-toggle="tab">Tabular View</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1-1">
                <div id="calendar"></div>
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
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tabular_schedules as $schedule) : ?>
                                    <?php
                                    $course_detail_query = "
                                    SELECT 
                                    courses.course_code, 
                                    courses.course_name, 
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
                                    program ON sections.program_id = program.id
                                WHERE 
                                    course_offering_info.id = '{$schedule['course_offering_info_id']}'
                                ";
                                    $course_detail_result = mysqli_query($conn, $course_detail_query);
                                    $course_detail = mysqli_fetch_assoc($course_detail_result);

                                    // Concatenate the section name
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
                                                                    WHERE s.course_offering_info_id = ?";
                                                $schedule_stmt = $conn->prepare($schedule_query);
                                                $schedule_stmt->bind_param('i', $schedule['course_offering_info_id']);
                                                $schedule_stmt->execute();
                                                $schedule_result = $schedule_stmt->get_result();
                                                while ($schedule_row = $schedule_result->fetch_assoc()) {
                                                    echo $schedule_row['room'] . "<br>";
                                                }

                                                $schedule_time_query = "SELECT DISTINCT time_start, time_end, room_id 
                                                                        FROM schedules 
                                                                        WHERE course_offering_info_id = ?";
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
                                        <td class="text-center">
                                            <button onclick="remove_faculty_load('<?php echo $schedule['course_offering_info_id']; ?>')" class="btn btn-danger btn-flat">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="col-sm-12">
                            <a href="../index.php?page=generate_schedule&instructor=<?php echo $instructor; ?>" target="_blank" class="btn btn-primary btn-block">Generate Schedule</a>
                        </div>
                    <?php else : ?>
                        <div class="callout callout-warning">
                            <div class="text-center">
                                <h5>No Faculty Loading Found!</h5>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    function remove_faculty_load(offering_id) {
        var array = {};
        array['offering_id'] = offering_id;
        array['instructor'] = "<?php echo $instructor; ?>";
        $.ajax({
            type: "GET",
            url: "ajax.php?action=remove_faculty_load",
            data: array,
            success: function(data) {
                // Parse JSON response
                var response = JSON.parse(data);

                // Check if the operation was successful
                if (response.success) {
                    // Display courses and current load
                    displayCourses('<?php echo $level; ?>', "<?php echo $instructor; ?>");
                    getCurrentLoad("<?php echo $instructor; ?>", '<?php echo $level; ?>');
                    // Show success message
                    alert_toast(response.message, 'success');
                } else {
                    // Show error message
                    alert_toast(response.message, 'danger');
                }
            }
        });
    }


    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            height: "auto",
            firstDay: 1,
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
            eventSources: [<?php echo "$get_schedule" ?>],
            eventRender: function(event, element) {
                element.find('div.fc-title').html(element.find('div.fc-title').text());
            },
        });



        function getUnitsLoaded(offering_id) {
            var array = {};
            array['offering_id'] = offering_id;
            array['instructor'] = "<?php echo $instructor; ?>";
            array['level'] = "<?php echo $level; ?>";
            $.ajax({
                type: "GET",
                url: "/ajax/admin/faculty_loading/get_units_loaded",
                data: array,
                success: function(data) {
                    $('#displayGetUnitsLoaded').html(data).fadeIn();
                    $('#modalunits').modal('toggle');
                },
                error: function() {
                    alert_toast('Something Went Wrong!', 'Notification!');
                }
            })
        }
    })
</script>