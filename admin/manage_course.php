<?php
include('db_connect.php');

$query = "SELECT id, program_code, program_name FROM program ORDER BY program_code";
$result = mysqli_query($conn, $query);

$programs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[] = $row;
    }
}
?>

<style>
    .card-header {
        border-bottom: none;
    }

    /* .form-control {
        width: auto;
        height: auto;
    } */

    /* .remove {
        display: block !important;
    } */
</style>

<section class="content container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <!-- Section Header -->
            <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
                <h3><i class="fas fa-plus-circle"></i> Add Course</h3>
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"> Course Management</li>
                    <li class="breadcrumb-item active"> Add Course</li>
                </ol>
            </section>
            <div class="card shadow mb-4">
                <form id="manage_course">
                    <div class="card-header bg-transparent">
                        <h5 class='card-title'></h5>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table" id="dynamic_field">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Period</th>
                                        <th>Level</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th style="width: 7%;">Lec</th>
                                        <th style="width: 7%;">Lab</th>
                                        <th style="width: 7%;">Units</th>
                                        <th>Complab</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php
                                        $program_id = $_SESSION['login_program_id'];
                                        ?>
                                        <input type="hidden" name="program_id[]" value="<?php echo $program_id; ?>">
                                        <input type="hidden" class="form-control" name="year[]" id="year" value="<?php echo date('Y'); ?>">
                                        <td><button type="button" class="add btn btn-flat btn-primary btn-block"><i class="fa fa-plus-circle"></i></button></td>
                                        <td>
                                            <select class="form-control" id="period1" name="period[]">
                                                <option value="1st Semester">1st Semester</option>
                                                <option value="2nd Semester">2nd Semester</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" id="level1" name="level[]">
                                                <option value="1st Year">1st Year</option>
                                                <option value="2nd Year">2nd Year</option>
                                                <option value="3rd Year">3rd Year</option>
                                                <option value="4th Year">4th Year</option>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="course_code[]" id="code1">
                                        </td>
                                        <td><input type="text" class="form-control" name="course_name[]" id="name1">
                                        </td>
                                        <td><input type="number" min="0" class="form-control" name="lec[]" id="lec1" onchange="calculateUnits(1)"></td>
                                        <td><input type="number"  min="0" class="form-control" name="lab[]" id="lab1" onchange="calculateUnits(1)"></td>
                                        <td><input type="number"  min="0" class="form-control" name="units[]" id="units1" readonly></td>
                                        <td align="center">
                                            <select class="form-control" id="comlab1" name="is_comlab[]">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end bg-transparent">
                        <button type="submit" class="btn btn-flat btn-success "><i class="fa fa-check-circle"></i> Save
                            Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    function calculateUnits(index) {
        // Get the lecture and lab values
        var lec = document.getElementById('lec' + index).value;
        var lab = document.getElementById('lab' + index).value;

        // Convert values to integers (or 0 if not a valid number)
        lec = parseInt(lec) || 0;
        lab = parseInt(lab) || 0;

        // Calculate the units
        var units = lec + Math.floor(lab / 3);

        // Set the units value
        document.getElementById('units' + index).value = units;
    }

    $(document).ready(function() {
        var no = 1;
        $('.add').on('click', function(e) {
            if ($("#code" + no).val() == "" || $("#name" + no).val() == "" || $("#lec" + no).val() == "" || $("#lab" +
                    no).val() == "" || $("#units" + no).val() == "") {
                alert_toast('Please Fill-up Required Fields', 'danger');
            } else {
                no++;
                var program_id = <?php echo $_SESSION['login_program_id']; ?>;

                $('#dynamic_field').append(`<tr id='row${no}'>
                    <input type="hidden" class="form-control" name="year[]" id="year" value="<?php echo date('Y'); ?>">
                    <input type="hidden" class="form-control" name="program_id[]" value="${program_id}">
                    <td><button class='btn btn-flat btn-danger remove btn-block' id='${no}'><i class='fa fa-times'></i></button></td>
                    <td><select class='form-control' name='period[]' id='period${no}'><option value='1st Semester'>1st Semester</option><option value='2nd Semester'>2nd Semester</option></select></td>
                    <td><select class='form-control' name='level[]' id='level${no}'><option value='1st Year'>1st Year</option><option value='2nd Year'>2nd Year</option><option value='3rd Year'>3rd Year</option><option value='4th Year'>4th Year</option><option value='5th Year'>5th Year</option></select></td>
                    <td><input type='text' class='form-control' name='course_code[]' id='code${no}'></td>
                    <td><input type='text' class='form-control' name='course_name[]' id='name${no}'></td>
                    <td><input type='number'  min="0" class='form-control' name='lec[]' id='lec${no}' onchange='calculateUnits(${no})'></td>
                    <td><input type='number'  min="0" class='form-control' name='lab[]' id='lab${no}' onchange='calculateUnits(${no})'></td>
                    <td><input type='number'  min="0" class='form-control' name='units[]' id='units${no}' readonly></td>
                    <td align='center'><select class='form-control' id='comlab${no}' name='comlab[]'><option value='0'>No</option><option value='1'>Yes</option></select></td>
                </tr>`);
            }
            e.preventDefault();
            return false;
        });

        $('#dynamic_field').on('click', '.remove', function(e) {
            var button_id = $(this).attr("id");
            $("#row" + button_id + "").remove();
            no--;
            e.preventDefault();
            return false;
        });

        document.getElementById('manage_course').addEventListener('submit', function(e) {
            e.preventDefault();

            start_load();
            var form = this;
            $.ajax({
                url: 'ajax.php?action=save_course',
                method: 'POST',
                data: $(form).serialize(),
                success: function(resp) {
                    console.log(resp);
                    if (resp == 1) {
                        alert_toast("Data successfully saved", 'success');
                        window.location.href = 'index.php?page=courses';
                    } else {
                        alert_toast("Course already exists", 'danger');
                    }
                }
            });
        });
    });
</script>
