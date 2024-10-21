<?php
include '../db_connect.php';
session_start();
$program_id = $_SESSION['login_program_id'];
$collection = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['level'])) {

    $instructor = $_GET['instructor'];
    $level = $_GET['level'];

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
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('is', $program_id, $level);
    $stmt->execute();
    $courses_result = $stmt->get_result();

    $courses = [];
    if ($courses_result->num_rows > 0) {
        while ($row = $courses_result->fetch_assoc()) {
            $courses[] = $row;
        }
    }

    if (!empty($courses)) {
        foreach ($courses as $course) {
            $detail_query = "SELECT coi.id AS course_offering_id, sec.section_name, c.level, c.course_code, c.course_name, coi.courses_id AS courses_id, p.program_code
                             FROM course_offering_info coi
                             JOIN sections sec ON coi.section_id = sec.id
                             JOIN courses c ON coi.courses_id = c.id
                             JOIN program p ON sec.program_id = p.id
                             WHERE coi.id = ?
                             AND c.level = ?";

            $detail_stmt = $conn->prepare($detail_query);
            if (!$detail_stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $detail_stmt->bind_param('is', $course['course_offering_info_id'], $level);
            $detail_stmt->execute();
            $detail_result = $detail_stmt->get_result();

            if ($detail_result->num_rows > 0) {
                while ($detail = $detail_result->fetch_assoc()) {
                    $section_name_concatenated = $detail['program_code'] . '-' . substr($detail['level'], 0, 1) . $detail['section_name'];

                    $schedules_query = "SELECT id, room_id, day, time_start, time_end 
                                        FROM schedules 
                                        WHERE course_offering_info_id = ? AND faculty_id IS NULL";
                    $schedules_stmt = $conn->prepare($schedules_query);
                    if (!$schedules_stmt) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $schedules_stmt->bind_param('i', $course['course_offering_info_id']);
                    $schedules_stmt->execute();
                    $schedules_result = $schedules_stmt->get_result();

                    $schedules = [];
                    while ($schedule = $schedules_result->fetch_assoc()) {
                        $schedules[] = $schedule;
                    }

                    $collection[$course['course_offering_info_id']] = (object)[
                        'level' => $detail['level'],
                        'offering_id' => $detail['course_offering_id'],
                        'section_name' => $section_name_concatenated,
                        'course_code' => $detail['course_code'],
                        'course_name' => $detail['course_name'],
                        'schedules' => $schedules
                    ];
                }
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
                                <tr>
                                    <td>
                                        <div class="text-center">
                                            <?php echo $data->course_name; ?><br>
                                            <?php echo $data->section_name; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <?php foreach ($data->schedules as $schedule) : ?>
                                                <?php
                                                $room_query = "SELECT room FROM rooms WHERE id = ?";
                                                $room_stmt = $conn->prepare($room_query);
                                                if (!$room_stmt) {
                                                    die("Prepare failed: " . $conn->error);
                                                }
                                                $room_stmt->bind_param('i', $schedule['room_id']);
                                                $room_stmt->execute();
                                                $room_result = $room_stmt->get_result();
                                                $room = $room_result->fetch_assoc();
                                                echo $room['room'] . "<br>";
                                                echo $schedule['day'] . "<br>";
                                                echo "[" . date('g:iA', strtotime($schedule['time_start'])) . "-" . date('g:iA', strtotime($schedule['time_end'])) . "]<br>";
                                                ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button onclick="addFacultyLoad('<?php echo $instructor; ?>', '<?php echo $data->offering_id; ?>')" class="btn btn-success btn-flat">
                                            <i class="fa fa-plus-circle"></i>
                                        </button>
                                    </td>
                                </tr>
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
                displayCourses('<?php echo $level; ?>', "<?php echo $instructor; ?>");
                getCurrentLoad("<?php echo $instructor; ?>", '<?php echo $level; ?>');
                window.location.href = "#page-top";
                alert_toast('Successfully Added Faculty Load!', 'success');
            },
            error: function(xhr, status, error) {
                if (xhr.status == 409) {
                    alert_toast('Conflict in Schedule Found!', 'danger');
                }
                if (xhr.status == 404) {
                    _conf("The no. of hours loaded exceeds. Do you want to override?", "addOverload", [instructor, course_offering_info_id]);
                }
            }
        });
    }

    function addOverload(instructor, course_offering_info_id) {
        var data = {
            instructor: instructor,
            course_offering_info_id: course_offering_info_id
        };
        $.ajax({
            type: "GET",
            url: "ajax.php?action=add_overload",
            data: data,
            success: function(response) {
                displayCourses('<?php echo $level; ?>', "<?php echo $instructor; ?>");
                getCurrentLoad("<?php echo $instructor; ?>", '<?php echo $level; ?>');
                window.location.href = "#page-top";
                alert_toast('Successfully Added Faculty Load as Overload!', 'success');
            },
            error: function(xhr, status, error) {
                if (xhr.status == 409) {
                    window.location.href = "#page-top";
                    alert_toast('Conflict in Schedule Found!', 'danger');
                }
            }
        });
    }
</script>