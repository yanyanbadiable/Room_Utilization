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

    .form-control {
        width: auto;
        height: auto;
    }

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
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="dynamic_field">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Period</th>
                                        <th>Level</th>
                                        <th>Program Code</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Lec</th>
                                        <th>Lab</th>
                                        <th>Units</th>
                                        <th>Complab</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <input type="hidden" class="form-control" name="year[]" id="year" value="<?php echo date('Y'); ?>">
                                        <td><button type="button" class="add btn btn-flat btn-primary"><i class="fa fa-plus-circle"></i></button></td>
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
                                        <td>
                                            <select class="form-control" id="program1" name="program_id[]">
                                                <?php foreach ($programs as $program) : ?>
                                                    <option value="<?php echo $program['id']; ?>">
                                                        <?php echo $program['program_code']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="course_code[]" id="code1">
                                        </td>
                                        <td><input type="text" class="form-control" name="course_name[]" id="name1">
                                        </td>
                                        <td><input type="number" class="form-control" name="lec[]" id="lec1"></td>
                                        <td><input type="number" class="form-control" name="lab[]" id="lab1"></td>
                                        <td><input type="number" class="form-control" name="units[]" id="units1"></td>
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
                        <button type="submit" class="btn btn-flat btn-success"><i class="fa fa-check-circle"></i> Save
                            Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    function fetchPrograms(programSelect) {
        $.ajax({
            url: 'get_program.php',
            method: 'GET',
            success: function(response) {
                var programs = JSON.parse(response);
                programs.forEach(function(program) {
                    $(programSelect).append('<option value="' + program.id + '">' + program.program_code + '</option>');
                });
            }
        });
    }

    $(document).ready(function() {
        var no = 1;
        $('.add').on('click', function(e) {
            if ($("#code" + no).val() == "" || $("#name" + no).val() == "" || $("#lec" + no).val() == "" || $("#lab" +
                    no).val() == "" || $("#units" + no).val() == "") {
                alert_toast('Please Fill-up Required Fields', 'danger');
            } else {
                no++;
                $('#dynamic_field').append(`<tr id='row${no}'>
                    <input type="hidden" class="form-control" name="year[]" id="year" value="<?php echo date('Y'); ?>">
                    <td><button class='btn btn-flat btn-danger remove' id='${no}'><i class='fa fa-times'></i></button></td>
                    <td><select class='form-control' name='period[]' id='period${no}'><option value='1st Semester'>1st Semester</option><option value='2nd Semester'>2nd Semester</option></select></td>
                    <td><select class='form-control' name='level[]' id='level${no}'><option value='1st Year'>1st Year</option><option value='2nd Year'>2nd Year</option><option value='3rd Year'>3rd Year</option><option value='4th Year'>4th Year</option><option value='5th Year'>5th Year</option></select></td>
                    <td><select class='form-control' name='program_id[]' id='program${no}'></select></td>
                    <td><input type='text' class='form-control' name='course_code[]' id='code${no}'></td>
                    <td><input type='text' class='form-control' name='course_name[]' id='name${no}'></td>
                    <td><input type='text' class='form-control' name='lec[]' id='lec${no}'></td>
                    <td><input type='text' class='form-control' name='lab[]' id='lab${no}'></td>
                    <td><input type='text' class='form-control' name='units[]' id='units${no}'></td>
                    <td align='center'><select class='form-control' id='comlab${no}' name='comlab[]'><option value='0'>No</option><option value='1'>Yes</option></select></td>
                </tr>`);

                var programSelect = '#program' + no;
                fetchPrograms(programSelect);
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