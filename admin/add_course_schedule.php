<?php
include 'db_connect.php';

// Check if it's a GET request and if the required parameters are set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['section_id'])) {
    try {
        $course_offering_info_id = $_GET['id'];
        $section_id = $_GET['section_id'];

        // Query to get course_offering_info information
        $course_offering_info_result = $conn->query("SELECT * FROM course_offering_info WHERE id = $course_offering_info_id");
        if (!$course_offering_info_result) {
            throw new Exception("Error fetching course offering information: " . $conn->error);
        }
        $course_offering_info = $course_offering_info_result->fetch_assoc();

        // Query to get inactive room schedules
        $inactive_result = $conn->query("SELECT * FROM schedules WHERE is_active = 0");
        if (!$inactive_result) {
            throw new Exception("Error fetching inactive room schedules: " . $conn->error);
        }
        $inactive = [];
        while ($row = $inactive_result->fetch_assoc()) {
            $inactive[] = $row;
        }

        // Get schedule
        $get_schedule = getSchedule($conn, $course_offering_info_id);

        $is_comlab = $conn->query("SELECT is_comlab FROM courses WHERE id = " . $course_offering_info['courses_id'])->fetch_assoc()['is_comlab'];

        // Render view
?>

            <!-- Include CSS libraries -->
            <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css"> -->

            <div class="container-fluid">
                <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
                    <h3><i class="fa fa-calendar-check"></i> Course Scheduling</h3>
                    <ol class="breadcrumb bg-transparent p-0 m-0">
                        <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active"> Course Management</li>
                        <li class="breadcrumb-item active">Course Scheduling</li>
                    </ol>
                </section>
                <section class="content">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card card-solid card-default shadow mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title">Inactive Schedules</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($inactive)) : ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Schedule</th>
                                                        <th>Attach</th>
                                                        <th>Delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($inactive as $schedule) : ?>
                                                        <tr>
                                                            <td><?php echo $schedule['day']; ?> <?php echo date('g:iA', strtotime($schedule['time_starts'])); ?>-<?php echo date('g:iA', strtotime($schedule['time_end'])); ?></td>

                                                            <td><a href="SchedAjax/CS_get_room_available.php?id=<?= $schedule['id']; ?>&course_offering_info_id=<?= $course_offering_info_id; ?>" class="btn btn-flat btn-block btn-success"><i class="fa fa-plus-circle"></i></a></td>

                                                            <td><a href="SchedAjax/delete_schedule.php?id=<?= $schedule['id']; ?>&course_offering_info_id=<?= $course_offering_info_id; ?>" onclick="return confirm('Do you wish to continue?')" class="btn btn-flat btn-block btn-danger"><i class="fa fa-times"></i></a></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else : ?>
                                        <div class="alert alert-danger" role="alert">
                                            <h5><strong>No Course Offered Found!</strong></h5>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="card card-default shadow mb-4">
                                <div class="card-header bg-transparent">
                                    <h5 class="card-title">Schedule</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label>Day</label>
                                                <select class="form-control" id="day">
                                                    <option>Day</option>
                                                    <option value="M">Monday</option>
                                                    <option value="T">Tuesday</option>
                                                    <option value="W">Wednesday</option>
                                                    <option value="Th">Thursday</option>
                                                    <option value="F">Friday</option>
                                                    <option value="Sa">Saturday</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Time Start</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control timepicker" id="time_start" min="07:00" max="20:30" step="1800">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Time End</label>
                                                <div class="input-group">
                                                    <input type="time" class="form-control timepicker" id="time_end" min="07:00" max="20:30" step="1800">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-sm-1">
                                            <label>Add</label>
                                            <a onclick="addschedule(day.value,time_start.value,time_end.value)" class="btn btn-flat btn-success text-white"><i class="fa fa-plus-circle"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer no-padding bg-transparent">
                                    <div class="col-sm-12">
                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div id="myModal" class="modal fade" role="dialog">
                <div id='displayroom'></div>
            </div>

            <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script> -->

            <script>
                // Your JavaScript code here
                $('.timepicker').timepicker({
                    showInputs: false,
                });

                $('#time_start').on('change', function() {
                    // Your existing code
                    <?php if (isset($is_comlab) && $is_comlab == 1) : ?>
                        $('#time_end').val(moment(this.value, "hh:mm TT").add(3, 'hours').format("hh:mm A"));
                    <?php endif; ?>

                    // New code for validation
                    var startTime = this.value;
                    var endTime = $('#time_end').val();

                    if (startTime >= endTime) {
                        $('#time_end').addClass('is-invalid');
                        $('#time_end').siblings('.invalid-feedback').text('End time must be greater than start time.');
                    } else {
                        $('#time_end').removeClass('is-invalid');
                        $('#time_end').siblings('.invalid-feedback').text('');
                    }
                });


                function addschedule(day, time_start, time_end) {
                    var isValid = true;
                    var startTime = $('#time_start').val();
                    var endTime = $('#time_end').val();

                    if (!day || !time_start || !time_end) {
                        isValid = false;
                        alert_toast('Please fill in all fields.', 'danger');
                    } else if (startTime >= endTime) {
                        isValid = false;
                        $('#time_end').addClass('is-invalid');
                        $('#time_end').siblings('.invalid-feedback').text('End time must be after start time.');
                    } else {
                        $('#time_end').removeClass('is-invalid');
                        $('#time_end').siblings('.invalid-feedback').text('');
                    }

                    if (isValid) {
                        // Your AJAX call here
                        $.ajax({
                            type: "GET",
                            url: "SchedAjax/CS_get_room_available.php",
                            data: {
                                day: day,
                                time_start: time_start,
                                time_end: time_end,
                                course_offering_info_id: <?= $course_offering_info_id ?>,
                                section_id: <?= $section_id ?>
                            },
                            success: function(data) {
                                $('#displayroom').html(data).fadeIn();
                                $('#myModal').modal('show');
                            }
                        });
                    }
                }

                $('#calendar').fullCalendar({
                    firstDay: 1,
                    columnFormat: 'ddd',
                    defaultView: 'agendaWeek',
                    hiddenDays: [0],
                    minTime: '07:00:00',
                    maxTime: '22:00:00',
                    header: false,
                    allDaySlot: false,
                    eventSources: [<?= $get_schedule ?>],
                    eventRender: function(event, element) {
                        element.find('div.fc-title').html(element.find('div.fc-title').text());
                    },
                    eventClick: function(event) {
                        var boolean = confirm('Clicking the OK button button will change the status of the schedule. Do you wish to continue?');
                        if (boolean == true) {
                            window.open('/admin/course_scheduling/remove_schedule/' + event.id + '/' + event.course_offering_info_id, '_self');
                        }
                    }
                });
            </script>

<?php
    } catch (Exception $e) {
        // Handle the exception: log it, display a message, etc.
        echo "An error occurred: " . $e->getMessage();
    }
}

function getSchedule($conn, $course_offering_info_id)
{
    $event_array = array();
    $schedules_result = $conn->query("SELECT * FROM schedules WHERE course_offering_info_id = $course_offering_info_id");
    while ($sched = $schedules_result->fetch_assoc()) {
        // Query to get course detail
        $course_detail_result = $conn->query("SELECT courses.course_code, course_offering_info.section_id
                                              FROM courses 
                                              JOIN course_offering_info ON course_offering_info.courses_id = courses.id 
                                              WHERE course_offering_info.id = $course_offering_info_id");
        if (!$course_detail_result) {
            throw new Exception("Error fetching course detail information: " . $conn->error);
        }
        $course_detail = $course_detail_result->fetch_assoc();

        // Determine day and color
        $day = '';
        $color = '';
        switch ($sched['day']) {
            case 'M':
                $day = 'Monday';
                $color = 'LightSalmon';
                break;
            case 'T':
                $day = 'Tuesday';
                $color = 'lightblue';
                break;
            case 'W':
                $day = 'Wednesday';
                $color = 'LightSalmon';
                break;
            case 'Th':
                $day = 'Thursday';
                $color = 'lightblue';
                break;
            case 'F':
                $day = 'Friday';
                $color = 'LightSalmon';
                break;
            case 'Sa':
                $day = 'Saturday';
                $color = 'lightblue';
                break;
            case 'Su':
                $day = 'Sunday';
                $color = 'LightSalmon';
                break;
        }

        // Add event to event array
        $event_array[] = array(
            'id' => $sched['id'],
            'title' => $course_detail['course_code'] . '<br>' . $sched['room_id'] . '<br>' . $course_detail['section_id'],
            'start' => date('Y-m-d', strtotime('this ' . $day)) . 'T' . $sched['time_start'],
            'end' => date('Y-m-d', strtotime('this ' . $day)) . 'T' . $sched['time_end'],
            'color' => $color,
            "textEscape" => 'false',
            'textColor' => 'black',
            'course_offering_info_id' => $course_offering_info_id
        );
    }
    return json_encode($event_array);
}
?>

<style>
    .card-footer {
        border-top: none;
    }
</style>