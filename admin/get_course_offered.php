<?php
include('db_connect.php');

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$program_code = '';
$offerings = [];

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
    $courses = $courses_result->fetch_all(MYSQLI_ASSOC);

    // Fetch course offerings
    $offerings_query = $conn->prepare("SELECT * FROM course_offering_info");
    $offerings_query->execute();
    $offerings_result = $offerings_query->get_result();
    if ($offerings_result->num_rows > 0) {
        $offerings = $offerings_result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<?php if (!empty($offerings)) : ?>
    <div class="card shadow mb-4">
        <div class='card-header'>
            <h5 class='card-title'>Courses Offered</h5>
        </div>
        <div class='card-body'>
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
                        <?php foreach ($courses as $course) : ?>
                            


                            <?php $curricula = curriculum::find($course['curriculum_id']); ?>
                            <tr>
                                <td><?php echo $curricula->course_code; ?></td>
                                <td><?php echo $curricula->course_name; ?></td>
                                <td>
                                    <div align="center">
                                        <?php
                                        $schedule3s = room_schedules::distinct()->where('offering_id', $course['id'])->get(['room']);
                                        ?>
                                        <?php foreach ($schedule3s as $schedule3) : ?>
                                            <?php echo $schedule3->room; ?>
                                        <?php endforeach; ?>
                                        <br>
                                        <?php
                                        $schedule2s = room_schedules::distinct()->where('offering_id', $course['id'])->get(['time_starts', 'time_end', 'room']);
                                        ?>
                                        <?php foreach ($schedule2s as $schedule2) : ?>
                                            <?php
                                            $days = room_schedules::where('offering_id', $course['id'])
                                                ->where('time_starts', $schedule2->time_starts)
                                                ->where('time_end', $schedule2->time_end)
                                                ->where('room', $schedule2->room)
                                                ->get(['day']);
                                            ?>
                                            [@foreach ($days as $day){{$day->day}}@endforeach {{date('g:iA', strtotime($schedule2->time_starts))}}-{{date('g:iA', strtotime($schedule2->time_end))}}]<br>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td><a href="{{url('/admin/course_scheduling/schedule',array($course['id'],$section_name))}}" target="_blank" class="btn btn-flat btn-success"><i class="fa fa-pencil"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else : ?>
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
<?php endif; ?>

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
