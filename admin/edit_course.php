<?php
include 'db_connect.php';
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    $course_query = "SELECT * FROM courses WHERE id = ?";
    $course_stmt = $conn->prepare($course_query);
    $course_stmt->bind_param("i", $course_id);
    $course_stmt->execute();
    $course_result = $course_stmt->get_result();
    if ($course_result->num_rows > 0) {
        $course = $course_result->fetch_assoc();
    }
}
?>

<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" id="edit_course">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">
                        <span class="course-code"><?php echo $course['course_code']; ?></span> -
                        <span class="course-name"><?php echo $course['course_name']; ?></span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="program_code" value="<?php echo $course['program_id']; ?>">
                    <input type="hidden" name="year" value="<?php echo $course['year']; ?>">
                    <input type="hidden" name="course_id" id="course_id" value="<?php echo $course['id']; ?>">
                    <div class="form-group">
                        <label>Course Code</label>
                        <input type="text" name="course_code" class="form-control" id="course_code" value="<?php echo $course['course_code']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" name="course_name" class="form-control" id="course_name" value="<?php echo $course['course_name']; ?>">
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Lec</label>
                                <input type="text" name="lec" class="form-control" id="lec" value="<?php echo $course['lec']; ?>">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Lab</label>
                                <input type="text" name="lab" class="form-control" id="lab" value="<?php echo $course['lab']; ?>">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Units</label>
                                <input type="text" name="units" class="form-control" id="units" value="<?php echo $course['units']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Computer Lab?</label>
                        <select class='form-control' name='comlab' id="is_comlab">
                            <option <?php if ($course['is_comlab'] == 0) echo 'selected="selected"'; ?> value='0'>No</option>
                            <option <?php if ($course['is_comlab'] == 1) echo 'selected="selected"'; ?> value='1'>Yes</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-flat btn-primary" onclick="submitForm()">Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<script>
    function submitForm() {
        var formData = $('#edit_course').serialize();
        $.ajax({
            type: "POST",
            url: "ajax.php?action=edit_course",
            data: formData,
            success: function(data) {
                if (data.trim() === '1') {  
                    $('#editModal').modal('hide');
                    $('.modal-backdrop').hide(); 
                    // window.location.reload()
                    alert_toast('Course Successfully Updated', 'success');
                } else {
                    $('#displayeditmodal').html(data).fadeIn();
                   
                }
            },
            error: function() {
                alert_toast('Something Went Wrong!', 'danger');
            }
        });
    }
</script>

