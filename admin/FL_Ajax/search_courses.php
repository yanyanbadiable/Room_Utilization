<?php
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['value']) && isset($_GET['level'])) {

    $value = $_GET['value'];
    $level = $_GET['level'];

    session_start();
    $program_id = $_SESSION['login_program_id'];

    $value = mysqli_real_escape_string($conn, $value);

    // Query to fetch courses and their offering information
    $courses_query = "
        SELECT courses.id AS course_id, courses.course_code, coi.id AS course_offering_info_id
        FROM courses
        JOIN course_offering_info coi ON courses.id = coi.courses_id
        JOIN sections s ON coi.section_id = s.id
        JOIN program p ON s.program_id = p.id
        JOIN schedules sch ON coi.id = sch.course_offering_info_id
        WHERE courses.course_code LIKE '%$value%'
        AND p.id = $program_id
        AND sch.is_active = 1
        AND sch.users_id IS NULL
    ";

    $courses_result = mysqli_query($conn, $courses_query);

    // Debugging: Check if the query executed successfully
    if (!$courses_result) {
        die('Query Error: ' . mysqli_error($conn));
    }

    $collection = []; // Initialize collection array

    if ($courses_result) {
        $courses = [];
        while ($row = mysqli_fetch_assoc($courses_result)) {
            $courses[] = $row;
        }

        // Debugging: Print the courses array
        // echo '<pre>';
        // print_r($courses);
        // echo '</pre>';

        if (!empty($courses)) {
            foreach ($courses as $course) {
                if (isset($course['course_offering_info_id'])) {
                    $course_offering_info_id = $course['course_offering_info_id'];
                    // Fetch details for each course offering
                    $detail_query = "
                        SELECT coi.id AS course_offering_id, s.section_name, c.level, coi.courses_id AS courses_id, p.program_code
                        FROM course_offering_info coi
                        JOIN sections s ON coi.section_id = s.id
                        JOIN courses c ON coi.courses_id = c.id
                        JOIN program p ON s.program_id = p.id
                        WHERE coi.id = {$course_offering_info_id}
                    ";
                    $detail_result = mysqli_query($conn, $detail_query);

                    // Error checking for detail query
                    if (!$detail_result) {
                        die('Detail Query Error: ' . mysqli_error($conn));
                    }

                    if ($detail_result && mysqli_num_rows($detail_result) > 0) {
                        $detail = mysqli_fetch_assoc($detail_result);
                        $section_name_concatenated = $detail['program_code'] . '-' . substr($detail['level'], 0, 1) . $detail['section_name'];
                        $collection[] = (object)[
                            'level' => $detail['level'],
                            'offering_id' => $detail['course_offering_id'],
                            'section_name' => $section_name_concatenated,
                            'courses_id' => $detail['courses_id']
                        ];
                    }
                }
            }
        }
    }
}
?>

<div class='col-sm-12 mb-3 p-0'>
    <div class="table-responsive">
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Schedule</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty(array_filter($collection, function ($item) use ($level) {
                    return $item->level == $level;
                }))) : ?>
                    <?php
                    $color_array = ['info', 'danger', 'warning', 'danger'];
                    $ctr = 0;
                    foreach ($collection as $data) : ?>
                        <?php if ($data->level == $level) : ?>
                            <?php
                            // Fetch courses details
                            $course_query = "SELECT * FROM courses WHERE id = {$data->courses_id}";
                            $course_result = mysqli_query($conn, $course_query);

                            // Error checking for course query
                            if (!$course_result) {
                                die('Course Query Error: ' . mysqli_error($conn));
                            }

                            $course = mysqli_fetch_assoc($course_result);
                            ?>
                            <tr>
                                <td>
                                    <div align="center"><?php echo $course['course_code']; ?><br><?php echo $data->section_name; ?></div>
                                </td>
                                <td>
                                    <div data-object="<?php echo $data->offering_id; ?>" class='callout callout-<?php echo $color_array[$ctr]; ?>'>
                                        <div align="center">
                                            <?php
                                            $schedule_query = "
                                                SELECT DISTINCT s.room_id, r.room
                                                FROM schedules s
                                                INNER JOIN rooms r ON s.room_id = r.id
                                                WHERE s.course_offering_info_id = {$data->offering_id}
                                            ";
                                            $schedule3_result = mysqli_query($conn, $schedule_query);

                                            // Error checking for schedule query
                                            if (!$schedule3_result) {
                                                die('Schedule Query Error: ' . mysqli_error($conn));
                                            }

                                            while ($schedule3 = mysqli_fetch_assoc($schedule3_result)) {
                                                echo $schedule3['room'] . "<br>";
                                            }

                                            $schedule2_query = "
                                                SELECT DISTINCT time_start, time_end 
                                                FROM schedules 
                                                WHERE course_offering_info_id = {$data->offering_id}
                                            ";
                                            $schedule2_result = mysqli_query($conn, $schedule2_query);

                                            // Error checking for schedule time query
                                            if (!$schedule2_result) {
                                                die('Schedule Time Query Error: ' . mysqli_error($conn));
                                            }

                                            while ($schedule2 = mysqli_fetch_assoc($schedule2_result)) {
                                                $day_query = "
                                                    SELECT day 
                                                    FROM schedules 
                                                    WHERE course_offering_info_id = {$data->offering_id} 
                                                    AND time_start = '{$schedule2['time_start']}' 
                                                    AND time_end = '{$schedule2['time_end']}'
                                                ";
                                                $day_result = mysqli_query($conn, $day_query);

                                                // Error checking for day query
                                                if (!$day_result) {
                                                    die('Day Query Error: ' . mysqli_error($conn));
                                                }

                                                $days = [];
                                                while ($day = mysqli_fetch_assoc($day_result)) {
                                                    $days[] = $day['day'];
                                                }
                                                echo "[" . implode("", $days) . " " . date('g:iA', strtotime($schedule2['time_start'])) . "-" . date('g:iA', strtotime($schedule2['time_end'])) . "]<br>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-success" onclick="addToCalendar(<?php echo $data->offering_id; ?>)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php $ctr = ($ctr + 1) % count($color_array); ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3" class="text-center">No courses found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>