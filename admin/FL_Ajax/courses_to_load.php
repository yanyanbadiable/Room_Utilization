<?php
include '../db_connect.php';

$collection = []; // Ensure $collection is initialized as an empty array

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['level'])) {

    $instructor = $_GET['instructor'];
    $level = $_GET['level'];

    // Assuming you have a session variable storing the program ID of the logged-in user
    session_start();
    $program_id = $_SESSION['login_program_id'];

    // Modify the SQL query to retrieve course offering IDs based on the program ID of the logged-in user
    $courses_query = "SELECT DISTINCT coi.id AS course_offering_info_id
                      FROM schedules s
                      JOIN course_offering_info coi ON s.course_offering_info_id = coi.id
                      JOIN sections sec ON coi.section_id = sec.id
                      JOIN program prog ON sec.program_id = prog.id
                      WHERE s.is_active = 1
                      AND s.faculty_id IS NULL
                      AND prog.id = $program_id";

    $courses_result = $conn->query($courses_query);

    $courses = [];
    if ($courses_result->num_rows > 0) {
        while ($row = $courses_result->fetch_assoc()) {
            $courses[] = $row;
        }
    }

    if (!empty($courses)) {
        foreach ($courses as $course) {
            $detail_query = "
            SELECT coi.id AS course_offering_id, s.section_name, c.level, coi.courses_id AS courses_id, p.program_code
            FROM course_offering_info coi
            JOIN sections s ON coi.section_id = s.id
            JOIN courses c ON coi.courses_id = c.id
            JOIN program p ON s.program_id = p.id
            WHERE coi.id = {$course['course_offering_info_id']}            
            ";
            $detail_result = mysqli_query($conn, $detail_query);
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
?>


<div class='card shadow mb-4'>
    <div class='card-header bg-dark text-white py-3'>
        <h4 class='card-title m-0'>Courses to Load</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12 mb-3">
                <input type="text" onkeyup="search(event, this.value, '<?php echo $level; ?>')" class="form-control" placeholder="Enter the course code to search..">
            </div>
        </div>
        <div id="searchcourse">
            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th width="30%">Course</th>
                            <th>Schedule</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($collection)) : ?>
                            <?php foreach ($collection as $data) : ?>
                                <?php if ($data->level == $level) : ?>
                                    <?php
                                    // Fetching course details
                                    $course_query = "SELECT course_code FROM courses WHERE id = {$data->courses_id}";
                                    $course_result = $conn->query($course_query);
                                    $course = $course_result->fetch_assoc();
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center">
                                                <?php echo $course['course_code']; ?><br>
                                                <?php echo $data->section_name; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <?php
                                                $schedule_query = "
                                                    SELECT DISTINCT s.room_id, r.room
                                                    FROM schedules s
                                                    INNER JOIN rooms r ON s.room_id = r.id
                                                    WHERE s.course_offering_info_id = {$data->offering_id}
                                                ";
                                                $schedule_result = $conn->query($schedule_query);
                                                while ($schedule = $schedule_result->fetch_assoc()) {
                                                    echo $schedule['room'] . "<br>";
                                                }

                                                $schedule_time_query = "
                                                    SELECT DISTINCT time_start, time_end, room_id 
                                                    FROM schedules 
                                                    WHERE course_offering_info_id = {$data->offering_id}
                                                ";
                                                $schedule_time_result = $conn->query($schedule_time_query);
                                                while ($schedule_time = $schedule_time_result->fetch_assoc()) {
                                                    $day_query = "
                                                        SELECT day 
                                                        FROM schedules 
                                                        WHERE course_offering_info_id = {$data->offering_id} 
                                                          AND time_start = '{$schedule_time['time_start']}' 
                                                          AND time_end = '{$schedule_time['time_end']}' 
                                                          AND room_id = '{$schedule_time['room_id']}'
                                                    ";
                                                    $day_result = $conn->query($day_query);
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
                                            <button class="btn btn-success" onclick="addToCalendar(<?php echo $data->offering_id; ?>)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
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
    </div>
</div>

<script>
    function addToCalendar(offeringId) {
        // Add your JavaScript logic here to add the course to the calendar
        console.log('Add to calendar: ', offeringId);
        // Example: You might want to make an AJAX request to a server-side script to handle the addition
    }
</script>