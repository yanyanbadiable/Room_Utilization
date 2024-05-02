<?php 
include('db_connect.php'); 

$course_id = '';
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    $qry = $conn->query("SELECT * FROM courses WHERE id=" . $course_id)->fetch_array();
    if ($qry) {
        foreach ($qry as $k => $v) {
            $$k = $v;
        }
    } else {
        echo "Invalid course ID.";
        // Handle invalid course ID scenario
    }
}
?>

<div class="container-fluid">
    <div class="card p-3">
        <div class="card-header">
            <b>Manage Course</b>
            <span class="">

                <button class="btn btn-primary btn-block btn-sm col-sm-2 float-right" type="button" id="new_course">
                    <i class="fa fa-plus"></i> New</button>
            </span>
        </div>
        <div id="msg"></div>
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Year</th>
                    <th class="text-center">Period</th>
                    <th class="text-center">Level</th> <!-- Added Level column header -->
                    <th class="text-center">Program</th>
                    <th class="text-center">Course Code</th>
                    <th class="text-center">Course Name</th>
                    <th class="text-center">Lec</th> <!-- Added Lec column header -->
                    <th class="text-center">Lab</th>
                    <th class="text-center">Units</th>
                    <th class="text-center">Is_ComLab?</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody id="dynamic_field">
                <?php
                $i = 1;
                $course =  $conn->query("SELECT courses.*, program.program_code FROM courses INNER JOIN program ON courses.program_id = program.id;");
                while ($row = $course->fetch_assoc()) :
                ?>
                    <tr id="row-template">
                        <td class="text-center"><?php echo $i++ ?></td>
                        <td class="">
                            <p><b><?php echo $row['year'] ?></b></p>
                        </td>
                        <td class="">
                            <p><b><?php echo $row['period'] ?></b></p>
                        </td>
                        <td class=""> <!-- Level -->
                            <p><b><?php echo $row['level'] ?></b></p>
                        </td>
                        <td class="">
                            <p><b><?php echo $row['program_code'] ?></b></p>
                        </td>
                        <td class="">
                            <p><b><?php echo $row['course_code'] ?></b></p>
                        </td>
                        <td class="">
                            <p><b><?php echo $row['course_name'] ?></b></p>
                        </td>
                        <td class=""> <!-- Lec -->
                            <p><b><?php echo $row['lec'] ?></b></p>
                        </td>
                        <td class="">
                            <p><b><?php echo $row['lab'] ?></b></p>
                        </td>
                        <td class="">
                            <p><b><?php echo $row['units'] ?></b></p>
                        </td>
                        <td class="">
                            <p><b><?php echo $row['is_comlab'] == 1 ? 'Yes' : 'No'; ?></b></p>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary edit_course" type="button" data-id="<?php echo $row['id'] ?>">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete_course" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>

<script>
    $(document).ready(function() {
        $('table').dataTable()
    })
    $('#new_course').click(function() {
        uni_modal("New Entry", "manage_course.php", 'mid-large')
    })

    $('.edit_course').click(function() {
        uni_modal("Edit Course", "manage_course.php?id=" + $(this).attr('data-id'), 'mid-large')

    })
    $('.delete_course').click(function() {
        _conf("Are you sure to delete this course?", "delete_course", [$(this).attr('data-id')], 'mid-large')
    })

    function delete_course($id) {
        start_load()
        $.ajax({
            url: 'ajax.php?action=delete_course',
            method: 'POST',
            data: {
                id: $id
            },
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1500)

                }
            }
        })
    }
</script>