<?php
include('db_connect.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if program code is set in the URL
if (isset($_GET['program_id'])) {
    // Get the program code from the URL parameter
    $program_code = $_GET['program_id'];

    // Query to fetch the program details based on program code
    $program_query = $conn->prepare("SELECT * FROM program WHERE id = ?");
    $program_query->bind_param("s", $program_code);
    $program_query->execute();
    $program_result = $program_query->get_result();
    $program = $program_result->fetch_assoc();

    // Assign program details to $row   
    $row = $program;

    $courses_query = $conn->prepare("SELECT * FROM courses WHERE program_id = ?");
    $courses_query->bind_param("s", $program_code);
    $courses_query->execute();
    $courses_result = $courses_query->get_result();
    $courses = $courses_result->fetch_all(MYSQLI_ASSOC);


    // Fetch years from the courses result
    $years = array_column($courses, 'year');
}

?>

<div>
    <?php
    if (!empty($courses)) {
    ?>
        <div class="card shadow mb-4">
            <div class="card-header bg-transparent">
                <h5 class="card-title">Courses to Offer</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th width="35%">Course Name</th>
                                <th>Lec</th>
                                <th>Lab</th>
                                <th>Units</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course) { ?>
                                <tr>
                                    <td><?php echo $course['course_code']; ?></td>
                                    <td><?php echo $course['course_name']; ?></td>
                                    <td><?php echo $course['lec']; ?></td>
                                    <td><?php echo $course['lab']; ?></td>
                                    <td><?php echo $course['units']; ?></td>
                                    <td class="text-center">
                                        <!-- Directly call the AJAX function on button click -->
                                        <button onclick="addOffer('<?php echo $course['id']; ?>', '<?php echo isset($section_name) ? $section_name : ''; ?>', '<?php echo isset($curriculum_year) ? $curriculum_year : ''; ?>', '<?php echo isset($level) ? $level : ''; ?>', '<?php echo isset($period) ? $period : ''; ?>')" class="btn btn-success btn-flat"><i class="fa fa-plus-circle"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="card shadow mb-4 ">
            <div class="card-header bg-transparent">
                <h5>Courses to Offer</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger" role="alert">
                    <h5><strong>No Courses to Offer Found!</strong></h5>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    function addOffer(courseId, sectionName, curriculumYear, level, period) {
        var array = {};
        if (sectionName == "") {
            array['course_id'] = courseId;
            array['section_name'] = sectionName;

            $.ajax({
                type: "GET",
                url: "ajax.php?action=add_course_offer",
                data: array,
                success: function(data) {
                    console.log(data);
                    alert('Course offer added successfully!');

                    searchcourse(curriculumYear, level, period, sectionName);
                },
                error: function() {
                    alert('An error occurred while adding the course offer.');
                }
            });
        } else {
            alert('Please input a section name');
        }
    }
</script>

<style>
    .card-header {
        border-bottom: none;
    }
</style>