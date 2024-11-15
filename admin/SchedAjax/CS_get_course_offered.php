<?php
include '../db_connect.php';
// var_dump($_GET);
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['program_code']) && isset($_GET['level']) && isset($_GET['section_id'])) {
    $program_code = $_GET['program_code'];
    $level = $_GET['level'];
    $section_id = $_GET['section_id'];

    $courses_query = $conn->prepare("
        SELECT course_offering_info.*, courses.course_code, courses.course_name
        FROM course_offering_info
        JOIN courses ON course_offering_info.courses_id = courses.id
        WHERE courses.level = ? AND course_offering_info.section_id = ?
    ");

    if (!$courses_query) {
        die('Error: ' . $conn->error);
    }

    $courses_query->bind_param("ss", $level, $section_id);
    $courses_query->execute();
    $courses_result = $courses_query->get_result();

    $courses = [];
    while ($row = $courses_result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>

<?php if (!empty($courses)) : ?>
    <div class="card card-default shadow mb-4">
        <div class='card-header bg-transparent'>
            <h5 class='card-title'>Courses Offered</h5>
        </div>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Description</th>
                            <th width="40%">Schedule</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course) : ?>
                            <tr>
                                <td><?php echo $course['course_code']; ?></td>
                                <td><?php echo $course['course_name']; ?></td>
                                <td>
                                    <div class="text-center">
                                        <?php
                                        $schedule3s_query = mysqli_query($conn, "
                                            SELECT DISTINCT r.room
                                            FROM schedules s
                                            JOIN rooms r ON s.room_id = r.id
                                            WHERE s.course_offering_info_id = '" . $course['id'] . "'
                                        ");
                                        $rooms = [];
                                        while ($schedule3 = mysqli_fetch_assoc($schedule3s_query)) {
                                            $rooms[] = $schedule3['room'];
                                        }
                                        foreach ($rooms as $room) {
                                            echo $room . '<br>';
                                            $schedule2s_query = mysqli_query($conn, "
                                                SELECT DISTINCT time_start, time_end
                                                FROM schedules
                                                WHERE course_offering_info_id = '" . $course['id'] . "' 
                                                AND room_id = (SELECT id FROM rooms WHERE room = '$room')
                                            ");

                                            while ($schedule2 = mysqli_fetch_assoc($schedule2s_query)) {
                                                $days_query = mysqli_query($conn, "
                                                    SELECT day 
                                                    FROM schedules 
                                                    WHERE course_offering_info_id = '" . $course['id'] . "' 
                                                    AND time_start = '" . $schedule2['time_start'] . "' 
                                                    AND time_end = '" . $schedule2['time_end'] . "' 
                                                    AND room_id = (SELECT id FROM rooms WHERE room = '$room')
                                                ");
                                                $days = [];
                                                while ($day = mysqli_fetch_assoc($days_query)) {
                                                    $days[] = $day['day'];
                                                }
                                                echo '[' . implode(',', $days) . " " . date('g:iA', strtotime($schedule2['time_start'])) . '-' . date('g:iA', strtotime($schedule2['time_end'])) . ']<br>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td class="text-center align-middle"><a href="index.php?page=add_course_schedule&id=<?php echo $course['id']; ?>&section_id=<?php echo $section_id; ?>" class="btn btn-flat btn-success"><i class="fa fa-pencil-alt"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="card card-default shadow mb-4">
        <div class='card-header bg-transparent'>
            <h5 class='card-title'>Courses Offered</h5>
        </div>
        <div class='card-body m-0'>
            <div class="rounded-sm bg-warning p-3 text-white text-center " role="alert">
                <h5>No Course Offered Found!</h5>
            </div>
        </div>
    </div>
<?php endif; ?>