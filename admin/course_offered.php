<?php
include('db_connect.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$curriculum_year = '';
$level = '';
$period = '';
$section_name = '';

if (isset($_GET['program_id'])) {

    $program_code = $_GET['program_id'];


    $program_query = $conn->prepare("SELECT * FROM program WHERE id = ?");
    $program_query->bind_param("s", $program_code);
    $program_query->execute();
    $program_result = $program_query->get_result();
    $program = $program_result->fetch_assoc();


    $row = $program;

    $courses_query = $conn->prepare("SELECT * FROM courses WHERE program_id = ?");
    $courses_query->bind_param("s", $program_code);
    $courses_query->execute();
    $courses_result = $courses_query->get_result();
    $courses = $courses_result->fetch_all(MYSQLI_ASSOC);


    $years = array_column($courses, 'year');


    $offerings_query = $conn->query("SELECT * FROM course_offering_info");


    if ($offerings_query->num_rows > 0) {
        $offerings = $offerings_query->fetch_all(MYSQLI_ASSOC);
    } else {
        $offerings = [];
    }
    // var_dump($program_code);
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
                        <?php foreach ($offerings as $offering) : ?>
                            <tr>
                                <?php

                                $course_query = $conn->prepare("SELECT * FROM courses WHERE id = ?");
                                $course_query->bind_param("s", $offering['courses_id']);
                                $course_query->execute();
                                $course_result = $course_query->get_result();
                                $course = $course_result->fetch_assoc();
                                ?>
                                <td><?php echo $course['course_code'] ?></td>
                                <td><?php echo $course['course_name'] ?></td>
                                <td><?php echo $course['lec'] ?></td>
                                <td><?php echo $course['lab'] ?></td>
                                <td><?php echo $course['units'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-danger btn-flat remove-course-offer" data-curriculum-id="<?php echo $offering['id']; ?>" data-section-id="<?php echo $offering['section_id']; ?>">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
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
    $(document).ready(function() {
        $('.remove-course-offer').click(function() {
            var curriculumId = $(this).data('curriculum-id');
            var sectionId = $(this).data('section-id');

            $.ajax({
                type: 'GET',
                url: 'ajax.php?action=remove_course_offer',
                data: {
                    curriculum_id: curriculumId,
                    section_id: sectionId
                },
                success: function(data) {

                    searchcourse('<?php echo $curriculum_year; ?>', '<?php echo $level; ?>', '<?php echo $period; ?>', '<?php echo $section_name; ?>');
                },
                error: function() {
                    alert('Something Went Wrong');
                }
            });
        });
    });
</script>