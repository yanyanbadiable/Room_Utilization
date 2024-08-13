<?php
include('db_connect.php');

$weekDays = [
    'M' => 'Monday',
    'T' => 'Tuesday',
    'W' => 'Wednesday',
    'Th' => 'Thursday',
    'F' => 'Friday',
    'S' => 'Saturday'
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

function fetchSchedules()
{
    global $conn;
    $sql = "
        SELECT 
            s.*, 
            co.course_name, 
            f.id AS faculty_id,
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
            s.day IN ('M', 'T', 'W', 'Th', 'F', 'S') AND
            s.time_start BETWEEN '08:00' AND '19:00'
    ";
    $result = $conn->query($sql);
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

function generateCalendarData($weekDays)
{
    $calendarData = [];
    $timeRange = generateTimeRange('08:00', '19:00');
    $schedules = fetchSchedules();
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

$calendarData = generateCalendarData($weekDays);
?>

<div class="container-fluid">
    <div class="content">
        <div class="row">
            <div class="col-sm-8">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        Calendar
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th width="125">Time</th>
                                        <?php foreach ($weekDays as $day) : ?>
                                            <th><?php echo $day; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($calendarData as $time => $days) : ?>
                                        <tr>
                                            <td class="text-center" ><?php echo $time; ?></td>
                                            <?php foreach ($days as $dayCode => $value) : ?>
                                                <?php if (isset($value['rowspan'])) : ?>
                                                    <td rowspan="<?php echo $value['rowspan']; ?>" class="align-middle text-center clickable" style="background-color:<?php echo $value['background_color']; ?>; color: #000; font-size: 14px;" data-schedule-id="<?php echo $value['schedule_id']; ?>">
                                                        <?php echo $value['course_name']; ?><br>
                                                        <b class="text-uppercase">
                                                            <?php echo $value['section_name']; ?><br>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 767.98px) {

        .table th,
        .table td {
            font-size: 0.8rem;
        }
    }

    .clickable {
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clickableCells = document.querySelectorAll('.clickable');

        clickableCells.forEach(cell => {
            cell.addEventListener('click', function() {
                const scheduleId = this.getAttribute('data-schedule-id');
                if (scheduleId) {
                    _conf("Are you sure you want to remove this schedule?", "remove_schedule", [$(this).attr('data-id')]);
                }
            });
        });
    });

    // function remove_schedule(schedule_id, offering_id) {
    //     $.ajax({
    //         type: "POST",
    //         url: "ajax.php?action=remove_schedule",
    //         data: {
    //             schedule_id: schedule_id,
    //             offering_id: offering_id
    //         },
    //         success: function(data) {
    //             alert_toast(data, 'success');
    //             location.reload();
    //         },
    //         error: function(xhr, status, error) {
    //             alert_toast(xhr.responseText, 'danger');
    //         }
    //     });
    // }
</script>