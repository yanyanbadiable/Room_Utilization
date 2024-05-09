<?php
include('db_connect.php');

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$program_code = '';
$level = '';
$section_name = '';
$offerings = [];

if (isset($_GET['program_code'], $_GET['level'], $_GET['section_name'])) {
    $program_code = $_GET['program_code'];
    $level = $_GET['level'];
    $section_name = $_GET['section_name'];

    // Prepare the query
    $offerings_query = $conn->prepare("
        SELECT 
            course_offering_info.*,
            courses.course_code,
            courses.course_name
        FROM 
            course_offering_info
        INNER JOIN
            courses ON course_offering_info.courses_id = courses.id
        INNER JOIN
            sections ON course_offering_info.section_id = sections.id
        WHERE 
        course_offering_info.courses_id = 63 AND
        sections.id = 12 AND
        courses.program_id = ? AND
        sections.level = ?
    ");
    
    // Bind parameters
    $offerings_query->bind_param("is", $program_code, $level);

    // Execute the query
    $offerings_query->execute();
    
    // Check for errors after executing the query
    if ($offerings_query->error) {
        printf("Error: %s.\n", $offerings_query->error);
        exit();
    }
    
    // Get the result
    $offerings_result = $offerings_query->get_result();
    $offerings = $offerings_result->fetch_all(MYSQLI_ASSOC);
    
    var_dump($offerings);
}
?>


<?php if (!empty($offerings)) { ?>
    <div class="card shadow mb-4">
        <div class='card-header bg-transparent'>
            <h5 class='card-title'>Courses Offered</h5>
        </div>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table table-bordered table-striped'>
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th width="40%">Schedule</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($offerings as $offering) { ?>
                            <tr>
                                <td><?php echo $offering['course_code']; ?></td>
                                <td><?php echo $offering['course_name']; ?></td>
                                <td>
                                    <!-- Display schedule information here -->
                                    <?php echo $offering['schedule']; ?>
                                </td>
                                <td>
                                <td><a href="<?php echo '/admin/course_scheduling/schedule/' . $course['id'] . '/' . $section_name; ?>" target="_blank" class="btn btn-flat btn-success"><i class="fa fa-pencil"></i></a></td>
                                </td>
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
                var courseId = button.getAttribute('data-course-id');
                var sectionId = button.getAttribute('data-section-id');

                // Assuming you have a function searchcourse defined
                searchcourse('<?php echo $course_year; ?>', '<?php echo $level; ?>', '<?php echo $period; ?>', '<?php echo $section_name; ?>');
            });
        });
    });
</script>