<?php
include '../db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['level']) && isset($_GET['offering_id'])) {
    $instructor = $_GET['instructor'];
    $offering_id = $_GET['offering_id'];
    $level = $_GET['level'];

    $instructor_info = [];
    $designation_query = "SELECT designation FROM faculty WHERE user_id = ?";
    $designation_stmt = $conn->prepare($designation_query);
    $designation_stmt->bind_param('i', $instructor);
    $designation_stmt->execute();
    $designation_result = $designation_stmt->get_result();
    if ($designation_result->num_rows > 0) {
        $instructor_info = $designation_result->fetch_assoc();
    }
    $designation = $instructor_info['designation'];

    // Fetching units
    $units_load = [];
    $units_query = "SELECT units FROM units_load WHERE users_id = ?";
    $units_stmt = $conn->prepare($units_query);
    $units_stmt->bind_param('i', $instructor);
    $units_stmt->execute();
    $units_result = $units_stmt->get_result();
    if ($units_result->num_rows > 0) {
        $units_load = $units_result->fetch_assoc();
    }
    $units = $units_load['units'];

    // Fetching tabular schedules
    $tabular_schedules = [];
    $tabular_schedules_query = "
    SELECT DISTINCT course_offering_info_id
    FROM schedules 
    WHERE is_active = 1 AND users_id = ?
";
    $tabular_schedules_stmt = $conn->prepare($tabular_schedules_query);
    $tabular_schedules_stmt->bind_param('i', $instructor);
    $tabular_schedules_stmt->execute();
    $tabular_schedules_result = $tabular_schedules_stmt->get_result();
    if ($tabular_schedules_result->num_rows > 0) {
        while ($row = $tabular_schedules_result->fetch_assoc()) {
            $tabular_schedules[] = $row;
        }
    }
}
?>

<div class="modal fade" id="modalunits">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title">Schedules</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <?php if (!empty($tabular_schedules)) : ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Course</th>
                                    <th class="text-center">Units</th>
                                    <th class="text-center">Section</th>
                                    <th class="text-center">Schedule</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tabular_schedules as $schedule) : ?>
                                    <?php
                                    $course_detail_query = "
                                    SELECT 
                                    courses.course_code, 
                                    courses.course_name, 
                                    program.program_code, 
                                    sections.level, 
                                    sections.section_name, 
                                    courses.units
                                    FROM 
                                        courses 
                                    INNER JOIN 
                                        course_offering_info ON course_offering_info.courses_id = courses.id 
                                    INNER JOIN 
                                        sections ON course_offering_info.section_id = sections.id
                                    INNER JOIN 
                                        program ON sections.program_id = program.id
                                    WHERE 
                                        course_offering_info.id = ?
                                    ";
                                    $course_detail_stmt = $conn->prepare($course_detail_query);
                                    $course_detail_stmt->bind_param('i', $schedule['course_offering_info_id']);
                                    $course_detail_stmt->execute();
                                    $course_detail_result = $course_detail_stmt->get_result();
                                    $course_detail = mysqli_fetch_assoc($course_detail_result);

                                    // Concatenate the section name
                                    $section_name_concatenated = $course_detail['program_code'] . '-' . substr($course_detail['level'], 0, 1) . $course_detail['section_name'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="text-center">
                                                <?php echo $course_detail['course_code']; ?> <br>
                                                <?php echo $course_detail['course_name']; ?>
                                            </div>
                                        </td>
                                        <td class="text-center"><?php echo $course_detail['units']; ?></td>
                                        <td class="text-center"><?php echo $section_name_concatenated; ?></td>
                                        <td>
                                            <div class="text-center">
                                                <?php
                                                $schedule_query = "SELECT DISTINCT s.room_id, r.room
                                                                    FROM schedules s
                                                                    INNER JOIN rooms r ON s.room_id = r.id
                                                                    WHERE s.course_offering_info_id = ?";
                                                $schedule_stmt = $conn->prepare($schedule_query);
                                                $schedule_stmt->bind_param('i', $schedule['course_offering_info_id']);
                                                $schedule_stmt->execute();
                                                $schedule_result = $schedule_stmt->get_result();
                                                while ($schedule_row = $schedule_result->fetch_assoc()) {
                                                    echo $schedule_row['room'] . "<br>";
                                                }

                                                $schedule_time_query = "SELECT DISTINCT time_start, time_end, room_id 
                                                                        FROM schedules 
                                                                        WHERE course_offering_info_id = ?";
                                                $schedule_time_stmt = $conn->prepare($schedule_time_query);
                                                $schedule_time_stmt->bind_param('i', $schedule['course_offering_info_id']);
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
                                                    $day_stmt->bind_param('isss', $schedule['course_offering_info_id'], $schedule_time['time_start'], $schedule_time['time_end'], $schedule_time['room_id']);
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
                                            <button onclick="remove_faculty_load('<?php echo $schedule['course_offering_info_id']; ?>')" class="btn btn-danger btn-flat">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <p>Do you want to override the units given by the Admin? <b>{{$units}}</b></p>
                        <div class="form-group">
                            <label>Maximun no. of Units Loaded</label>
                            <input id="overrideval" type="text" class="form-control" value="{{$units}}">
                        </div>
                    <?php else : ?>
                        <div class="callout callout-warning">
                            <div class="text-center">
                                <h5>No Faculty Loading Found!</h5>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="overridebtn(overrideval.value)" class="btn btn-primary">Override</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    function overridebtn(override) {
        var array = {};
        array['instructor'] = "<?php echo $instructor; ?>";
        array['offering_id'] = "<?php echo $offering_id; ?>";
        array['override'] = override;
        $.ajax({
            type: "GET",
            url: "/ajax/admin/faculty_loading/override_add",
            data: array,
            success: function(data) {
                displaycourses('<?php echo $level; ?>', "<?php echo $instructor; ?>");
                getCurrentLoad("<?php echo $instructor; ?>", '<?php echo $level; ?>');
                $('#modalunits').modal('toggle');
            },
            error: function(xhr) {
                if (xhr.status == 500) {
                    toastr.error('Conflict in Schedule Found!!', 'Message!');
                } else {
                    toastr.error('Something Went Wrong!', 'Message!');
                }
            }
        })
    }
</script>