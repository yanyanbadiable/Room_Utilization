<?php
include('db_connect.php');

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$program_code = '';
$offerings = [];
var_dump($_GET);
if (isset($_GET['program_id'])) {
    $program_code = $_GET['program_id'];


    // Fetch program details based on program ID
    $program_query = $conn->prepare("SELECT * FROM program WHERE id = ?");
    $program_query->bind_param("s", $program_code);
    $program_query->execute();
    $program_result = $program_query->get_result();
    $program = $program_result->fetch_assoc();

    // Fetch courses offered for the program
    $courses_query = $conn->prepare("SELECT * FROM courses WHERE program_id = ?");
    $courses_query->bind_param("s", $program_code);
    $courses_query->execute();
    $courses_result = $courses_query->get_result();
    while ($row = $courses_result->fetch_assoc()) {
        $courses[] = $row;
    }

    // Fetch course offerings
    $offerings_query = $conn->query("SELECT * FROM course_offering_info");
    if ($offerings_query->num_rows > 0) {
        while ($row = $offerings_query->fetch_assoc()) {
            $offerings[] = $row;
        }
    }
}
?>

<?php if (!empty($offerings)) { ?>
    <div class="box box-default">
        <div class='box-header'>
            <h5 class='box-title'>Courses Offered</h5>
        </div>
        <div class='box-body'>
            <div class='table-responsive'>
                <table class='table table-bordered table-striped'>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Description</th>
                            <th width="40%">Schedule</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course) { ?>
                            <?php
                            $curriculum_id = $course['curriculum_id'];
                            $curricula_query = $conn->prepare("SELECT * FROM curriculum WHERE id = ?");
                            $curricula_query->bind_param("s", $curriculum_id);
                            $curricula_query->execute();
                            $curricula_result = $curricula_query->get_result();
                            $curricula = $curricula_result->fetch_assoc();
                            ?>
                            <tr>
                                <td><?php echo $curricula['course_code']; ?></td>
                                <td><?php echo $curricula['course_name']; ?></td>
                                <td>
                                    <div align="center">
                                        <?php
                                        $schedule_query = $conn->prepare("SELECT DISTINCT room FROM room_schedules WHERE offering_id = ?");
                                        $schedule_query->bind_param("s", $course['id']);
                                        $schedule_query->execute();
                                        $schedule_result = $schedule_query->get_result();
                                        while ($schedule = $schedule_result->fetch_assoc()) {
                                            echo $schedule['room'] . "<br>";
                                        }

                                        $schedule_query = $conn->prepare("SELECT time_starts, time_end, room FROM room_schedules WHERE offering_id = ?");
                                        $schedule_query->bind_param("s", $course['id']);
                                        $schedule_query->execute();
                                        $schedule_result = $schedule_query->get_result();
                                        while ($schedule = $schedule_result->fetch_assoc()) {
                                            $days_query = $conn->prepare("SELECT day FROM room_schedules WHERE offering_id = ? AND time_starts = ? AND time_end = ? AND room = ?");
                                            $days_query->bind_param("ssss", $course['id'], $schedule['time_starts'], $schedule['time_end'], $schedule['room']);
                                            $days_query->execute();
                                            $days_result = $days_query->get_result();
                                            while ($day = $days_result->fetch_assoc()) {
                                                echo $day['day'] . " " . date('g:iA', strtotime($schedule['time_starts'])) . "-" . date('g:iA', strtotime($schedule['time_end'])) . "<br>";
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td><a href="<?php echo '/admin/course_scheduling/schedule/' . $course['id'] . '/' . $section_name; ?>" target="_blank" class="btn btn-flat btn-success"><i class="fa fa-pencil"></i></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="card shadow mb-4">
        <div class="card-header bg-transparent">
            <h5 class="card-title">Courses Offered</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-danger" role="alert">
                <h5><strong>No Course Offered Found!</strong></h5>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var removeButtons = document.querySelectorAll('.remove-course-offer');
        removeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var curriculumId = button.getAttribute('data-curriculum-id');
                var sectionId = button.getAttribute('data-section-id');

                // Assuming you have a function searchcourse defined
                searchcourse('<?php echo $curriculum_year; ?>', '<?php echo $level; ?>', '<?php echo $period; ?>', '<?php echo $section_name; ?>');
            });
        });
    });
</script>