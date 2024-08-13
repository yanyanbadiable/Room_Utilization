<?php
include '../db_connect.php'; 

if (isset($_GET['day'], $_GET['section_id'])) {
    $day = $_GET['day'];
    $course_offering_info_id = $_GET['id'];
    $section_id = $_GET['section_id'];

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
                'end' => $next->format('H:i'),
                'displayStart' => $start->format('h:i '),
                'displayEnd' => $next->format('h:i ')
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

    $startOptions = getAvailableTimeOptions($day, $section_id, '08:00', '19:00', 'start');
    $endOptions = getAvailableTimeOptions($day, $section_id, '08:00', '19:00', 'end');

    echo json_encode([
        'startOptions' => $startOptions,
        'endOptions' => $endOptions
    ]);
}
