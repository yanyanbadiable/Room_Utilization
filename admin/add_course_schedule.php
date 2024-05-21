<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['section_id'])) {
    $course_offering_info_id = $_GET['id'];
    $section_id = $_GET['section_id'];

    $stmt = $conn->prepare("SELECT * FROM course_offering_info WHERE id = ?");
    $stmt->bind_param("i", $course_offering_info_id);
    $stmt->execute();
    $course_offering_info_result = $stmt->get_result();
    $course_offering_info = $course_offering_info_result->fetch_assoc();

    if (!$course_offering_info) {
        throw new Exception("Error fetching course offering information: " . $conn->error);
    }

    $stmt = $conn->prepare("SELECT * FROM schedules WHERE is_active = 0");
    $stmt->execute();
    $inactive_result = $stmt->get_result();

    $inactive = [];
    while ($row = $inactive_result->fetch_assoc()) {
        $inactive[] = $row;
    }

    $stmt = $conn->prepare("SELECT is_comlab FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_offering_info['courses_id']);
    $stmt->execute();
    $is_comlab = $stmt->get_result()->fetch_assoc()['is_comlab'];
}
?>

<style>
    .card-footer {
        border-top: none;
    }

    .custom-event {
        white-space: normal !important;
        overflow: visible !important;
        height: auto !important;
        padding: 2px !important;
        font-size: 12px;
        text-align: center;
    }

    .custom-event .fc-event-title {
        display: block;
        white-space: normal;
        font-size: 12px;
    }
</style>

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
                                <a onclick="addSchedule(day.value, time_start.value, time_end.value)" class="btn btn-flat btn-success text-white"><i class="fa fa-plus-circle"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer no-padding bg-transparent mb-3 p-2">
                        <div class="col-sm-12">
                            <div id="calendar1"></div>
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

<script>
    var courseOfferingInfoId = <?php echo json_encode($course_offering_info_id); ?>;
    var sectionId = <?php echo json_encode($section_id); ?>;

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar1');
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
            eventClick: function(info) {
                var boolean = confirm('Clicking the OK button will change the status of the schedule. Do you wish to continue?');
                if (boolean == true) {
                    window.open('/admin/course_scheduling/remove_schedule/' + info.event.id + '/' + info.event.extendedProps.course_offering_info_id, '_self');
                }
            },
            eventDidMount: function(info) {
                var titleElement = info.el.querySelector('.fc-event-title');
                if (titleElement) {
                    titleElement.innerHTML = info.event.title;
                }
                info.el.classList.add('custom-event');
            }

        });
        fetchEvents();

        function fetchEvents() {
            fetch('ajax.php?action=get_schedule&course_offering_info_id=<?= $course_offering_info_id ?>')
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {

                    if (data.error) {
                        console.error('Error fetching schedule data:', data.error);
                    } else {
                        calendar.removeAllEvents();
                        calendar.addEventSource(data);
                    }
                })
                .catch(function(error) {
                    console.error('There was an error while fetching schedule data:', error);
                });
        }
        calendar.render();
    });

    function addSchedule(day, time_start, time_end) {
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

            if (courseOfferingInfoId && sectionId && day && time_start && time_end) {
                $.ajax({
                    type: "GET",
                    url: "SchedAjax/CS_get_room_available.php",
                    data: {
                        day: day,
                        time_start: time_start,
                        time_end: time_end,
                        course_offering_info_id: courseOfferingInfoId,
                        section_id: sectionId
                    },
                    success: function(data) {
                        $('#displayroom').html(data).fadeIn();
                        $('#myModal').modal('show');

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            } else {
                alert('Error: Invalid data passed to AJAX request.');
            }
        }
    }

    $('#time_start').on('change', function() {
        <?php if (isset($is_comlab) && $is_comlab == 1) : ?>
            $('#time_end').val(moment(this.value, "HH:mm").add(3, 'hours').format("HH:mm"));
        <?php endif; ?>

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
</script>