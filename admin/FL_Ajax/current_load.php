<?php
include '../db_connect.php';

// Check if the request method is GET and if instructor and level are set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['level'])) {

    // Get instructor and level from the GET parameters
    $instructor = $_GET['instructor'];
    $level = $_GET['level'];

    // Prevent SQL injection by escaping instructor
    $instructor = mysqli_real_escape_string($conn, $instructor);

    // Query to fetch schedules
    $loads_query = "
        SELECT courses.*, course_offering_info.*, schedules.* 
        FROM courses 
        JOIN course_offering_info ON courses.id = course_offering_info.courses_id 
        JOIN schedules ON schedules.course_offering_info_id = course_offering_info.id 
        WHERE schedules.faculty_id = '$instructor'
    ";
    $loads_result = mysqli_query($conn, $loads_query);

    // Check if the query was successful
    if (!$loads_result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    // Fetch schedules and store them in an array
    $loads = [];
    while ($row = mysqli_fetch_assoc($loads_result)) {
        $loads[] = $row;
    }

    // Query to fetch distinct tabular schedules
    $tabular_schedules_query = "
        SELECT DISTINCT course_offering_info_id 
        FROM schedules 
        WHERE is_active = 1 AND faculty_id = '$instructor'
    ";
    $tabular_schedules_result = mysqli_query($conn, $tabular_schedules_query);

    // Check if the query was successful
    if (!$tabular_schedules_result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    // Fetch tabular schedules and store them in an array
    $tabular_schedules = [];
    while ($row = mysqli_fetch_assoc($tabular_schedules_result)) {
        $tabular_schedules[] = $row;
    }

    // Query to fetch all schedules
    $schedules_query = "
        SELECT * 
        FROM schedules 
        WHERE is_active = 1 AND faculty_id = '$instructor'
    ";
    $schedules_result = mysqli_query($conn, $schedules_query);

    // Check if the query was successful
    if (!$schedules_result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    // Fetch all schedules and store them in an array
    $schedules = [];
    while ($row = mysqli_fetch_assoc($schedules_result)) {
        $schedules[] = $row;
    }
}

if (!empty($schedules)) {
    $event_array = [];
    foreach ($schedules as $sched) {
        // Retrieve course details
        $course_detail_query = "
            SELECT courses.course_code, course_offering_info.section_id 
            FROM courses 
            JOIN course_offering_info ON course_offering_info.courses_id = courses.id 
            WHERE course_offering_info.id = '{$sched['course_offering_info_id']}'
        ";
        $course_detail_result = mysqli_query($conn, $course_detail_query);
        $course_detail = mysqli_fetch_assoc($course_detail_result);

        // Determine color based on day
        $day = "";
        $color = "";
        switch ($sched['day']) {
            case 'M':
            case 'W':
                $day = 'Monday';
                $color = 'LightSalmon';
                break;
            case 'T':
            case 'Th':
                $day = 'Tuesday';
                $color = 'lightblue';
                break;
            case 'F':
            case 'Sa':
                $day = 'Friday';
                $color = 'LightSalmon';
                break;
            case 'Su':
                $day = 'LightSalmon';
                break;
            default:
                // Handle other cases if needed
                break;
        }

        // Add event details to event array
        $event_array[] = [
            'id' => $sched['id'],
            'title' => $course_detail['course_code'] . '<br>' . $sched['room'] . '<br>' . $course_detail['section_id'],
            'start' => date('Y-m-d', strtotime($day . ' this week')) . 'T' . $sched['time_starts'],
            'end' => date('Y-m-d', strtotime($day . ' this week')) . 'T' . $sched['time_end'],
            'color' => $color,
            "textEscape" => 'false',
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

<div class="card shadow mb-4 px-2">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs d-flex">
            <li class="mr-auto header mr-auto p-2">
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
            <div class="tab-pane" id="tab_2-2">
                <div class="table-responsive">
                    <?php if (!empty($tabular_schedules)) : ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Units</th>
                                    <th>Section</th>
                                    <th>Schedule</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tabular_schedules as $schedule) : ?>
                                    <?php
                                    $course_detail_query = "
                                        SELECT courses.course_code, courses.course_name, course_offering_info.section_name, courses.units 
                                        FROM courses 
                                        JOIN course_offering_info ON course_offering_info.courses_id = courses.id 
                                        WHERE course_offering_info.id = '{$schedule['course_offering_info_id']}'
                                    ";
                                    $course_detail_result = mysqli_query($conn, $course_detail_query);
                                    $course_detail = mysqli_fetch_assoc($course_detail_result);
                                    ?>
                                    <tr onclick="remove_faculty_load('<?php echo $schedule['course_offering_info_id']; ?>')">
                                        <td><?php echo $course_detail['course_code']; ?></td>
                                        <td><?php echo $course_detail['course_name']; ?></td>
                                        <td><?php echo $course_detail['units']; ?></td>
                                        <td><?php echo $course_detail['section_name']; ?></td>
                                        <td>
                                            <div class="text-center">
                                                <?php
                                                $schedule3s_query = "SELECT DISTINCT room FROM schedules WHERE course_offering_info_id = '{$schedule['offering_id']}'";
                                                $schedule3s_result = mysqli_query($conn, $schedule3s_query);
                                                ?>
                                                <?php while ($schedule3 = mysqli_fetch_assoc($schedule3s_result)) : ?>
                                                    <?php echo $schedule3['room']; ?>
                                                <?php endwhile; ?>
                                                <br>
                                                <?php
                                                $schedule2s_query = "SELECT DISTINCT time_starts, time_end, room FROM schedules WHERE offering_id = '{$schedule['offering_id']}'";
                                                $schedule2s_result = mysqli_query($conn, $schedule2s_query);
                                                ?>
                                                <?php while ($schedule2 = mysqli_fetch_assoc($schedule2s_result)) : ?>
                                                    <?php
                                                    $days_query = "SELECT day FROM schedules WHERE offering_id = '{$schedule['offering_id']}' AND time_starts = '{$schedule2['time_starts']}' AND time_end = '{$schedule2['time_end']}' AND room = '{$schedule2['room']}'";
                                                    $days_result = mysqli_query($conn, $days_query);
                                                    ?>
                                                    [<?php while ($day = mysqli_fetch_assoc($days_result)) : ?><?php echo $day['day']; ?><?php endwhile; ?> <?php echo date('g:iA', strtotime($schedule2['time_starts'])) . '-' . date('g:iA', strtotime($schedule2['time_end'])); ?>]<br>
                                                <?php endwhile; ?>
                                            </div>
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

                    eventClick: function(event) {
                        remove_faculty_load(event.offering_id);
                    },

                });

                function addFacultyLoading() {
                        var array = {};
                        array['instructor'] = "<?php echo $instructor; ?>";
                        array['offering_id'] = originalEventObject.title;
                        $.ajax({
                            type: "GET",
                            url: "/ajax/admin/faculty_loading/add_faculty_load",
                            data: array,
                            success: function(data) {
                                displaycourses('<?php echo $level; ?>', "<?php echo $instructor; ?>");
                                getCurrentLoad("<?php echo $instructor; ?>", '<?php echo $level; ?>');
                                alert_toast('Successfully loaded the subject to the Instructor!', 'success');
                            },
                            error: function(xhr, status, error) {
                                if (xhr.status == 500) {
                                    alert_toast('Conflict in Schedule Found!', 'danger');
                                }
                                if (xhr.status == 404) {
                                    var boolean = confirm('The no. of units loaded exceeds. Do you want to override?');
                                    if (boolean == true) {
                                        getunitsloaded(array['offering_id']);
                                    }
                                }
                            }
                        })
                    }

                function getunitsloaded(offering_id) {
                    var array = {};
                    array['offering_id'] = offering_id;
                    array['instructor'] = "<?php echo $instructor; ?>";
                    array['level'] = "<?php echo $level; ?>";
                    $.ajax({
                        type: "GET",
                        url: "/ajax/admin/faculty_loading/get_units_loaded",
                        data: array,
                        success: function(data) {
                            $('#displaygetunitsloaded').html(data).fadeIn();
                            $('#modalunits').modal('toggle');
                        },
                        error: function() {
                            toastr.error('Something Went Wrong!', 'Notification!');
                        }
                    })
                }

                function remove_faculty_load(offering_id) {
                    var boolean = confirm('By clicking the ok button will unload the subject from the instructor. Do you wish to continue?');
                    if (boolean == true) {
                        var array = {};
                        array['offering_id'] = offering_id;
                        array['instructor'] = "<?php echo $instructor; ?>";
                        $.ajax({
                            type: "GET",
                            url: "/ajax/admin/faculty_loading/remove_faculty_load",
                            data: array,
                            success: function(data) {
                                displaycourses('<?php echo $level; ?>', "<?php echo $instructor; ?>");
                                getCurrentLoad("<?php echo $instructor; ?>", '<?php echo $level; ?>');
                                toastr.error('Removal of Faculty Loading', 'Notification!');
                            }
                        })
                    }
                }
</script>