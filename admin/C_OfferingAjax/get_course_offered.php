<?php
include('../db_connect.php');

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// var_dump($_GET);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['year']) && isset($_GET['level']) && isset($_GET['period']) && isset($_GET['section_id'])) {

    $curriculum_year = $_GET['year'];
    $level = $_GET['level'];
    $period = $_GET['period'];
    $section_id = $_GET['section_id'];


    $offerings_query = $conn->prepare("
        SELECT course_offering_info.*, courses.course_code, courses.course_name, courses.lec, courses.lab, courses.units
        FROM course_offering_info
        INNER JOIN courses ON course_offering_info.courses_id = courses.id
        WHERE course_offering_info.section_id = ?
    ");


    $offerings_query->bind_param("s", $section_id);
    $offerings_query->execute();
    $offerings_result = $offerings_query->get_result();


    $offerings = [];
    while ($row = $offerings_result->fetch_assoc()) {
        $offerings[] = $row;
    }
}
?>



<?php if (!empty($offerings)) { ?>
    <div class="card shadow mb-4">
        <div class='card-header bg-transparent'>
            <h5 class='card-title'>Courses Offered</h5>
        </div>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table table-bordered'>
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
                        <?php foreach ($offerings as $offering) { ?>
                            <tr>
                                <td><?php echo $offering['course_code']; ?></td>
                                <td><?php echo $offering['course_name']; ?></td>
                                <td><?php echo $offering['lec']; ?></td>
                                <td><?php echo $offering['lab']; ?></td>
                                <td><?php echo $offering['units']; ?></td>
                                <td class="text-center"><button onclick="removeOffer('<?php echo $offering['courses_id']; ?>','<?php echo $offering['section_id']; ?>')" class="btn btn-danger btn-flat"><i class="fa fa-times"></i></button></td>
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
    function removeOffer(courses_id, section_id) {

        var array = {};
        array['courses_id'] = courses_id;
        array['section_id'] = section_id;

        $.ajax({
            type: "GET",
            url: "ajax.php?action=remove_course_offer",
            data: array,
            success: function(data) {
                alert_toast(data, 'danger');
                console.log(data)
                searchcourse('<?php echo $curriculum_year; ?>', '<?php echo $level; ?>', '<?php echo $period; ?>', '<?php echo $section_id; ?>');
            },
            error: function() {
                alert('Something Went Wrong');
            }
        });
    }
</script>