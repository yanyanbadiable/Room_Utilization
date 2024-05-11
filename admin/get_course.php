<?php
include('db_connect.php');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['year']) && isset($_GET['level']) && isset($_GET['period']) && isset($_GET['section_id']) && isset($_GET['program_id'])) {

    $year = $_GET['year'] ?? '';
    $level = $_GET['level'] ?? '';
    $period = $_GET['period'] ?? '';
    $section_id = $_GET['section_id'] ?? '';
    $program_id = $_GET['program_id'] ?? '';


    $offered_stmt = $conn->prepare("SELECT courses_id FROM course_offering_info WHERE section_id = ?");
    $offered_stmt->bind_param("s", $section_id);
    $offered_stmt->execute();
    $offered_result = $offered_stmt->get_result();


    $offered_courses = [];
    while ($row = $offered_result->fetch_assoc()) {
        $offered_courses[] = $row['courses_id'];
    }


    $courses = [];

    $sql = "SELECT c.* FROM courses c WHERE c.year = ? AND c.level = ? AND c.period = ? AND c.program_id = ?";
    if (!empty($offered_courses)) {
        $sql .= " AND c.id NOT IN (";
        foreach ($offered_courses as $course_id) {
            $sql .= '?,';
        }

        $sql = rtrim($sql, ',') . ')';
    }



    $course_stmt = $conn->prepare($sql);


    $bindParams = array_merge([$year, $level, $period, $program_id], $offered_courses);
    $types = str_repeat('s', count($bindParams));
    $course_stmt->bind_param($types, ...$bindParams);


    $course_stmt->execute();


    if ($course_stmt->error) {
        echo "Error: " . $course_stmt->error;
    }


    $course_result = $course_stmt->get_result();
    while ($row = $course_result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>

<div>
    <?php if (!empty($courses)) { ?>
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
                                        <button onclick="addOffer('<?php echo $course['id']; ?>')" class="btn btn-success btn-flat"><i class="fa fa-plus-circle"></i></button>
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
    function addOffer(course_id) {
        var array = {};
        if ('<?php echo $section_id; ?>' != "") {
            array['course_id'] = course_id;
            array['section_id'] = '<?php echo $section_id; ?>';

            $.ajax({
                type: "GET",
                url: "ajax.php?action=add_course_offer",
                data: array,
                success: function(data) {
                    alert_toast(data, 'success');
                    searchcourse('<?php echo $year; ?>', '<?php echo $level; ?>', '<?php echo $period; ?>', '<?php echo $section_id; ?>', '<?php echo $program_id; ?>');
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