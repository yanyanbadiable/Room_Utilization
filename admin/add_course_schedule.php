<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['section_id'])) {
    $course_offering_info_id = $_GET['id'];
    $section_id = $_GET['section_id'];

    $stmt = $conn->prepare("SELECT * FROM course_offering_info WHERE id = ? AND section_id = ?");
    $stmt->bind_param("ii", $course_offering_info_id, $section_id);
    $stmt->execute();
    $course_offering_info_result = $stmt->get_result();
    $course_offering_info = $course_offering_info_result->fetch_assoc();

    $stmt = $conn->prepare("SELECT is_comlab, hours FROM courses WHERE id = ? ");
    $stmt->bind_param("i", $course_offering_info['courses_id']);
    $stmt->execute();
    $courseResult = $stmt->get_result()->fetch_assoc();
    $is_comlab = $courseResult['is_comlab'];
    $totalCourseHours = $courseResult['hours'];
    $totalCourseHoursParts = explode(':', $totalCourseHours);
    $totalCourseHours = $totalCourseHoursParts[0] + $totalCourseHoursParts[1] / 60;

    $stmt = $conn->prepare("SELECT * FROM schedules WHERE is_active = 0");
    $stmt->execute();
    $inactive_result = $stmt->get_result();

    $inactive = [];
    while ($row = $inactive_result->fetch_assoc()) {
        $inactive[] = $row;
    }

    $stmt = $conn->prepare("SELECT SUM(TIMESTAMPDIFF(HOUR, 
            STR_TO_DATE(time_start, '%H:%i'), 
            STR_TO_DATE(time_end, '%H:%i'))) AS scheduled_hours
    FROM schedules 
    WHERE course_offering_info_id = ? AND is_active = 1;
    ");
    $stmt->bind_param("i", $course_offering_info_id);
    $stmt->execute();
    $scheduledHoursResult = $stmt->get_result();
    $scheduledHours = $scheduledHoursResult->fetch_assoc()['scheduled_hours'] ?? 0;

    if (!is_numeric($scheduledHours)) {
        $scheduledHours = 0;
    }

    $remainingHours = $totalCourseHours - $scheduledHours;

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

    function getAvailableTimeOptions($day, $section_id, $startTime, $endTime, $type = 'start')
    {
        global $conn;
        $options = '';

        $query = "
        SELECT s.time_start, s.time_end 
        FROM schedules s
        INNER JOIN course_offering_info coi ON s.course_offering_info_id = coi.id
        WHERE s.day = ? AND coi.section_id = ? AND s.is_active = 1
    ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $day, $section_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $scheduledTimes = $result->fetch_all(MYSQLI_ASSOC);

        $times = generateTimeRange($startTime, $endTime);

        foreach ($times as $time) {
            $overlap = false;
            foreach ($scheduledTimes as $scheduled) {
                if (($time['start'] >= $scheduled['time_start'] && $time['start'] < $scheduled['time_end']) ||
                    ($time['end'] > $scheduled['time_start'] && $time['end'] <= $scheduled['time_end'])
                ) {
                    $overlap = true;
                    break;
                }
            }
            if (!$overlap) {
                $optionValue = $type === 'start' ? $time['start'] : $time['end'];
                $optionDisplay = $type === 'start' ? $time['displayStart'] : $time['displayEnd'];
                $options .= "<option value='{$optionValue}'>{$optionDisplay}</option>";
            }
        }
        return $options;
    }

    function fetchSchedules($section_id)
    {
        global $conn;
        $sql = "
        SELECT 
            s.*, 
            co.course_name, 
            r.room, 
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
            rooms r ON s.room_id = r.id
        JOIN 
            sections sec ON coi.section_id = sec.id
        JOIN 
            program p ON sec.program_id = p.id
        WHERE 
            coi.section_id = ? AND
            s.day IN ('M', 'T', 'W', 'Th', 'F', 'S') AND
            s.time_start BETWEEN '07:00' AND '19:00'
    ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $section_id);
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

    function generateCalendarData($weekDays, $section_id)
    {
        $calendarData = [];
        $timeRange = generateTimeRange('07:00', '19:00');
        $schedules = fetchSchedules($section_id);
        $skipSlots = [];
        $coursesColors = [];

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


                        $sectionNameConcatenated = $schedule['program_code'] . '-' . substr($schedule['level'], 0, 1) . $schedule['section_name'];

                        if (!isset($coursesColors[$schedule['course_offering_info_id']])) {
                            $coursesColors[$schedule['course_offering_info_id']] = generateLightColor($schedule['course_offering_info_id']);
                        }

                        $calendarData[$time['displayStart'] . ' - ' . $time['displayEnd']][$dayCode] = [
                            'course_name' => $schedule['course_name'],
                            'room_name' => $schedule['room'],
                            'section_name' => $sectionNameConcatenated,
                            'rowspan' => $duration,
                            'schedule_id' => $schedule['id'],
                            'course_offering_info_id' => $schedule['course_offering_info_id'],
                            'background_color' => $coursesColors[$schedule['course_offering_info_id']]
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
    $calendarData = generateCalendarData($weekDays, $section_id);
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

    .table th.schedule,
    .table th.attach,
    .table th.delete {
        min-width: unset;
        max-width: unset;
    }

    .table th.schedule {
        width: 50% !important;
    }

    .table th.attach,
    .table th.delete {
        width: 25% !important;
    }

    .card-footer {
        border-top: none;
    }

    .clickable {
        cursor: pointer;
    }
</style>


<div class="container-fluid p-2">
    <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
        <h3><i class="fa fa-calendar-check"></i> Course Scheduling</h3>
        <ol class="breadcrumb bg-transparent p-0 m-0">
            <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="index.php?page=course_scheduling">Academic Program</a></li>
            <li class="breadcrumb-item active">Course Scheduling</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-sm-4">
                <div class="card card-solid card-default shadow mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title m-1">Inactive Schedules</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($inactive)) : ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="schedule">Schedule</th>
                                            <th class="text-center attach">Attach</th>
                                            <th class="text-center delete">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($inactive as $schedule) : ?>
                                            <tr>
                                                <td><?php echo $schedule['day']; ?> <?php echo date('g:iA', strtotime($schedule['time_start'])); ?>-<?php echo date('g:iA', strtotime($schedule['time_end'])); ?></td>

                                                <td class="text-center">
                                                    <button class="btn btn-flat btn-success" onclick="attach_schedule(<?php echo $schedule['id']; ?>, <?php echo $course_offering_info_id; ?>, <?php echo $section_id; ?>)">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </button>
                                                </td>

                                                <td class="text-center">
                                                    <button class="btn btn-flat btn-danger delete_schedule" data-id="<?php echo $schedule['id']; ?>" data-offering-id="<?php echo $course_offering_info_id; ?>">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </td>

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
                        <h4 class="card-title m-0 d-inline"><i class="fa fa-calendar-check pr-1"></i> Schedule -
                            <span><small><b>(Remaining Hour/s: <?php echo $remainingHours; ?>)</b></small></span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <input type="hidden" id="course_offering_info_id" value="<?php echo $course_offering_info_id; ?>">
                                <input type="hidden" id="section_id" value="<?php echo $section_id; ?>">
                                <div class="form-group">
                                    <label>Day</label>
                                    <select class="form-control" id="day">
                                        <option value=" ">Day</option>
                                        <option value="M">Monday</option>
                                        <option value="T">Tuesday</option>
                                        <option value="W">Wednesday</option>
                                        <option value="Th">Thursday</option>
                                        <option value="F">Friday</option>
                                        <option value="S">Saturday</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Time Start</label>
                                    <select class="form-control" id="time_start">
                                        <option value=" ">Select start time</option>
                                        <?php
                                        $day = 'M';
                                        echo getAvailableTimeOptions($day, $section_id, '07:00', '19:00', 'start');
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Time End</label>
                                    <select class="form-control" id="time_end">
                                        <option value=" ">Select end time</option>
                                        <!-- <option disabled selected>Select end time</option> -->
                                        <?php
                                        echo getAvailableTimeOptions($day, $section_id, '07:00', '19:00', 'end');
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-1 text-center">
                                <label>Add</label>
                                <a onclick="addSchedule(day.value, time_start.value, time_end.value)" class="btn btn-flat btn-success text-white"><i class="fa fa-plus-circle"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer no-padding bg-transparent mb-3 p-2">
                        <div class="col-sm-12">
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
                                                        <td rowspan="<?php echo $value['rowspan']; ?>" class="align-middle text-center clickable" style="background-color:<?php echo $value['background_color']; ?>; color: #000; font-size: 0.7rem;" data-schedule-id="<?php echo $value['schedule_id']; ?>" data-offering-id="<?php echo $value['course_offering_info_id']; ?>">
                                                            <?php echo $value['course_name']; ?><br>
                                                            <b class="text-uppercase">
                                                                <?php echo $value['room_name']; ?><br>
                                                                <?php echo $value['section_name']; ?>
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
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div id='display_room'></div>
</div>

<script>
    var courseOfferingInfoId = "<?php echo $course_offering_info_id; ?>";
    var sectionId = "<?php echo $section_id; ?>";
    var totalCourseHours = parseFloat("<?php echo $totalCourseHours; ?>");
    var scheduledHours = parseFloat("<?php echo $scheduledHours; ?>");

    function _reset() {
        $('#day').val('');
        $('#time_start').val('');
        $('#time_end').val('');
    }

    function addSchedule(day, time_start, time_end) {
        var day = $('#day').val();
        var time_start = $('#time_start').val();
        var time_end = $('#time_end').val();
        var isValid = true;

        if (!day || !time_start || !time_end || day.trim() === 'Day' || time_start.trim() === 'Select start time' || time_end.trim() === 'Select end time') {
            isValid = false;
            alert_toast('Please fill in all fields.', 'warning');
            return;
        }

        var newStart = new Date('1970-01-01T' + time_start + 'Z');
        var newEnd = new Date('1970-01-01T' + time_end + 'Z');

        var newScheduleHours = (newEnd - newStart) / (1000 * 60 * 60);
        var remainingHours = totalCourseHours - scheduledHours;

        if (remainingHours <= 0) {
            window.location.href = "#page-top";
            alert_toast('No remaining hours available for scheduling.', 'warning');
            return;
        }

        if (newScheduleHours > remainingHours) {
            window.location.href = "#page-top";
            alert_toast(`The remaining hours to schedule is only ${remainingHours} hours.`, 'warning');
            return;
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
                        section_id: sectionId,
                        total_hours: newScheduleHours
                    },
                    success: function(data) {
                        $('#display_room').html(data).fadeIn();
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

    document.addEventListener('DOMContentLoaded', function() {
        const clickableCells = document.querySelectorAll('.clickable');

        clickableCells.forEach(cell => {
            cell.addEventListener('click', function() {
                const scheduleId = this.getAttribute('data-schedule-id');
                const offeringId = this.getAttribute('data-offering-id');

                if (scheduleId && offeringId) {
                    _conf("Are you sure you want to remove this schedule?", "remove_schedule", [scheduleId, offeringId]);

                } else {
                    console.error('Schedule ID or Offering ID is missing.');
                }
            });
        });
    });

    $(document).ready(function() {
        var day = $('#day');
        var timeStartSelect = $('#time_start');
        var timeEndSelect = $('#time_end');

        function fetchTimeOptions(dayValue) {
            $.ajax({
                type: "GET",
                url: "SchedAjax/CS_fetch_time_options.php",
                data: {
                    day: dayValue,
                    id: $('#course_offering_info_id').val(),
                    section_id: $('#section_id').val()
                },
                dataType: "json",
                success: function(data) {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    timeStartSelect.html('<option disabled selected>Select start time</option>' + data.startOptions);
                    timeEndSelect.html('<option disabled selected>Select end time</option>' + data.endOptions);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching time options:', xhr.responseText);
                }
            });
        }

        day.on('change', function() {
            fetchTimeOptions(this.value);
        });

        timeStartSelect.on('change', function() {
            const selectedIndex = this.selectedIndex;
            const endOptions = Array.from(timeEndSelect[0].options);

            timeEndSelect.html('<option disabled selected>Select end time</option>');
            endOptions.forEach((option, index) => {
                if (index > selectedIndex) {
                    timeEndSelect.append(option);
                }
            });
        });

        fetchTimeOptions(day.val());
    });


    function remove_schedule(schedule_id, offering_id) {
        $.ajax({
            type: "POST",
            url: "ajax.php?action=remove_schedule",
            data: {
                schedule_id: schedule_id,
                offering_id: offering_id
            },
            success: function(data) {
                window.location.href = "#page-top";
                alert_toast("Schedule successfully removed!", 'success');
                location.reload();
            },
            error: function(xhr, status, error) {
                alert_toast(xhr.responseText, 'danger');
            }
        });
    }

    function attach_schedule(schedule_id, offering_id, section_id) {
        $.ajax({
            type: "POST",
            url: "ajax.php?action=attach_schedule",
            data: {
                schedule_id: schedule_id,
                offering_id: offering_id,
                section_id: section_id
            },
            success: function(response) {
                var data = JSON.parse(response);
                window.location.href = "#page-top";
                if (data.status === 'success') {
                    alert_toast(data.message, 'success');
                    location.reload();
                } else {
                    alert_toast(data.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                window.location.href = "#page-top";
                alert_toast(xhr.responseText, 'danger');
            }
        });
    }


    $('.delete_schedule').click(function() {
        var scheduleId = $(this).data('id');
        var offeringId = $(this).data('offering-id');
        _conf("Are you sure to permanently delete this schedule?", "delete_schedule", [scheduleId, offeringId]);
    });


    function delete_schedule(schedule_id, offering_id) {
        $.ajax({
            type: "POST",
            url: "ajax.php?action=delete_schedule",
            data: {
                schedule_id: schedule_id,
                offering_id: offering_id
            },
            success: function(data) {
                window.location.href = "#page-top";
                alert_toast(data, 'success');
                location.reload();
            },
            error: function(xhr, status, error) {
                window.location.href = "#page-top";
                alert_toast(xhr.responseText, 'danger');
            }
        });
    }
</script>