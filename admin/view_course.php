<?php
include('db_connect.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$courses = [];

if (isset($_GET['program_code'])) {
    $program_code = $_GET['program_code'];

    $program_query = "SELECT id, program_name FROM program WHERE program_code = ?";
    $stmt = $conn->prepare($program_query);
    $stmt->bind_param("s", $program_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $program = $result->fetch_assoc();

    if ($program) {

        $program_id = $program['id'];

        $query = "SELECT DISTINCT year FROM courses WHERE program_id = ? ORDER BY year DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $program_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $courses[] = $row['year'];
        }
    } else {
        echo "Program not found!";
    }
}
?>
<div class="container-fluid p-3">
    <div class="row">
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="far fa-folder"></i> View Course</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> Course Management</li>
                <li class="breadcrumb-item active"><a href="index.php?page=courses">View Course</a></li>
            </ol>
        </section>

        <section class="content col-sm-12">
            <div class="row">
                <div class="col-sm-12" id="displaycurriculum">
                    <div class="card card-default shadow mb-4">
                        <div class="card-header bg-transparent">
                            <h3 class="card-title m-0"><?php echo $program['program_name']; ?></h3>
                        </div>
                        <div class="card-body py-0 mb-4">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Curriculum Year</th>
                                            <th class="text-center" style="width: 30%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courses as $year) : ?>
                                            <tr>
                                                <td><?php echo $year . ' - ' . ($year + 1); ?></td>
                                                <td class="text-center">
                                                    <a href="index.php?page=list_course&program_code=<?php echo $program_code; ?>&year=<?php echo $year; ?>" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                                                    <a onclick="editModal('<?php echo $year; ?>')" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div id="displayEditModal">

        </div>
    </div>
</div>

<style>
    .card-header {
        border-bottom: none;
    }
</style>

<script>
    function editModal(year) {
        var array = {};
        array['year'] = year;
        $.ajax({
            type: "GET",
            url: "edit_year.php",
            data: array,
            success: function(data) {
                $('#displayEditModal').html(data).fadeIn();
                $('#editModal').modal('toggle');
            },
            error: function() {
                alert_toast('Something Went Wrong!', 'danger');
            }
        })
    }
</script>