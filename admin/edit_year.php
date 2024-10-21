<?php
include 'db_connect.php';
session_start();
$program_id = $_SESSION['login_program_id'];

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}
?>

<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" id="edit_year">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title">
                        Edit Curriculum Year
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                    <input type="hidden" name="program_id" value="<?php echo $program_id; ?>">
                    <div class="form-group">
                        <label>Curriculum Year</label>
                        <input type="text" name="updated_year" class="form-control" id="year" value="<?php echo $year; ?>">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-flat btn-primary" onclick="submitForm()">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function submitForm() {
        var formData = $('#edit_year').serialize();
        $.ajax({
            type: "POST",
            url: "ajax.php?action=edit_year",
            data: formData,
            success: function(data) {
                if (data.trim() === '1') {
                    $('#editModal').modal('hide');
                    $('.modal-backdrop').hide();
                    window.location.href = "#page-top";
                    alert_toast('Curriculum Year Updated Successfully!', 'success');
                    location.reload();
                } else {
                    $('#displayEditModal').html(data).fadeIn();
                }
            },
            error: function() {
                alert_toast('Something Went Wrong!', 'danger');
            }
        });
    }
</script>