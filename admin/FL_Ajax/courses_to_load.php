<?php
include '../db_connect.php';

$collection = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['level'])) {

    $instructor = $_GET['instructor'];
    $level = $_GET['level'];

    session_start();
    $program_id = $_SESSION['login_program_id'];

    // Modify the SQL query to retrieve course offering IDs based on the program ID and level
    $courses_query = "SELECT DISTINCT coi.id AS course_offering_info_id
                      FROM schedules s
                      JOIN course_offering_info coi ON s.course_offering_info_id = coi.id
                      JOIN sections sec ON coi.section_id = sec.id
                      JOIN program prog ON sec.program_id = prog.id
                      JOIN courses c ON coi.courses_id = c.id
                      WHERE s.is_active = 1
                      AND s.faculty_id IS NULL
                      AND prog.id = ?
                      AND c.level = ?";

    $stmt = $conn->prepare($courses_query);
    $stmt->bind_param('is', $program_id, $level);
    $stmt->execute();
    $courses_result = $stmt->get_result();

    $courses = [];
    if ($courses_result->num_rows > 0) {
        while ($row = $courses_result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
    // else {
    //     echo "No courses found for the given program_id and level.";
    // }

    if (!empty($courses)) {
        foreach ($courses as $course) {
            $detail_query = "SELECT coi.id AS course_offering_id, s.section_name, c.level, coi.courses_id AS courses_id, p.program_code
                             FROM course_offering_info coi
                             JOIN sections s ON coi.section_id = s.id
                             JOIN courses c ON coi.courses_id = c.id
                             JOIN program p ON s.program_id = p.id
                             WHERE coi.id = ?
                             AND c.level = ?";

            $detail_stmt = $conn->prepare($detail_query);
            $detail_stmt->bind_param('is', $course['course_offering_info_id'], $level);
            $detail_stmt->execute();
            $detail_result = $detail_stmt->get_result();

            if ($detail_result->num_rows > 0) {
                $detail = $detail_result->fetch_assoc();
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
        <div id="searchCourses">
            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">Course</th>
                            <th class="text-center">Schedule</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($collection)) : ?>
                            <?php foreach ($collection as $data) : ?>
                                <?php if ($data->level == $level) : ?> <!-- Check if the level matches -->
                                    <?php
                                    // Fetching course details
                                    $course_query = "SELECT course_code FROM courses WHERE id = ?";
                                    $course_stmt = $conn->prepare($course_query);
                                    $course_stmt->bind_param('i', $data->courses_id);
                                    $course_stmt->execute();
                                    $course_result = $course_stmt->get_result();
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
                                                $schedule_query = "SELECT DISTINCT s.room_id, r.room
                                                                   FROM schedules s
                                                                   INNER JOIN rooms r ON s.room_id = r.id
                                                                   WHERE s.course_offering_info_id = ?";
                                                $schedule_stmt = $conn->prepare($schedule_query);
                                                $schedule_stmt->bind_param('i', $data->offering_id);
                                                $schedule_stmt->execute();
                                                $schedule_result = $schedule_stmt->get_result();
                                                while ($schedule = $schedule_result->fetch_assoc()) {
                                                    echo $schedule['room'] . "<br>";
                                                }

                                                $schedule_time_query = "SELECT DISTINCT time_start, time_end, room_id 
                                                                        FROM schedules 
                                                                        WHERE course_offering_info_id = ?";
                                                $schedule_time_stmt = $conn->prepare($schedule_time_query);
                                                $schedule_time_stmt->bind_param('i', $data->offering_id);
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
                                                    $day_stmt->bind_param('isss', $data->offering_id, $schedule_time['time_start'], $schedule_time['time_end'], $schedule_time['room_id']);
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
                                            <button onclick="addFacultyLoad('<?php echo $instructor; ?>', '<?php echo $data->offering_id; ?>')" class="btn btn-success btn-flat">
                                                <i class="fa fa-plus-circle"></i>
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
    function addFacultyLoad(instructor, course_offering_info_id) {
        var data = {
            instructor: instructor,
            course_offering_info_id: course_offering_info_id
        };

        $.ajax({
            type: "GET",
            url: "ajax.php?action=add_faculty_load",
            data: data,
            success: function(response) {
                alert_toast(response, 'success');
            },
            error: function() {
                alert('An error occurred while adding the faculty loads.');
            }
        });
    }
</script>