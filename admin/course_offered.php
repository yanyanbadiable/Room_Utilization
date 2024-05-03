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

    // Query to fetch offerings - Assuming you have a table named "offerings"
    $offerings_query = $conn->query("SELECT * FROM course_offering_info");

    // Check if there are any offerings
    if ($offerings_query->num_rows > 0) {
        $offerings = $offerings_query->fetch_all(MYSQLI_ASSOC);
    } else {
        $offerings = []; // Set $offerings to an empty array if no offerings found
    }
}
?>

<?php if (!empty($offerings)) : ?>
    <div class="card card-default">
        <div class="card-header">
            <h5 class="card-title">Courses Offered</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th width="35%">Description</th>
                            <th>Lec</th>
                            <th>Lab</th>
                            <th>Units</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr>
                                <td><?php echo $row['course_code'] ?></td>
                                <td><?php echo $row['course_name'] ?></td>
                                <td><?php echo $row['lec'] ?></td>
                                <td><?php echo $row['lab'] ?></td>
                                <td><?php echo $row['units'] ?></td>
                                <td class="text-center"><button onclick="removeoffer('<?php echo $curriculum->id; ?>','<?php echo $section_name; ?>')" class="btn btn-danger btn-flat"><i class="fa fa-times"></i></button></td>
                            </tr>
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
                <h5 ><strong>No Course Offered Found!</strong></h5>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    function removeoffer(curriculum_id, section_name) {
        var array = {};
        array['curriculum_id'] = curriculum_id;
        array['section_name'] = section_name;

        $.ajax({
            type: "GET",
            url: "/ajax/admin/course_offerings/remove_course_offer",
            data: array,
            success: function(data) {
                toast.error(data, 'Notification!');
                searchcourse('<?php echo $curriculum_year; ?>', '<?php echo $level; ?>', '<?php echo $period; ?>', '<?php echo $section_name; ?>');
            },
            error: function() {
                alert('Something Went Wrong');
            }
        });
    }
</script>