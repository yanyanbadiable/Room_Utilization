<?php
include('db_connect.php');
$department_id = $_SESSION['login_department_id'];
?>

<style>
    .card-header {
        border-bottom: none;
    }
</style>

<section class="content container-fluid p-3">
    <div class="row">
        <div class="col-sm-12 px-0">
            <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
                <h3><i class="fas fa-plus-circle"></i> Add Course</h3>
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active">Course Management</li>
                    <li class="breadcrumb-item"><a href="index.php?page=courses">View Course</a></li>
                    <li class="breadcrumb-item active">Add Course</li>
                </ol>
            </section>

            <form id="manage_course">
                <div class="card shadow mb-4">
                    <div class="card-header p-3">
                        <h5 class="mb-0"><b>Curriculum Details</b></h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="cmo_no">CMO No.</label>
                                <input type="text" class="form-control" id="cmo_no" name="cmo_no" placeholder="Enter CMO No.">
                            </div>
                            <div class="col-md-3">
                                <label for="series">Series</label>
                                <input type="text" class="form-control" id="series" name="series" placeholder="Enter Series">
                            </div>
                            <div class="col-md-3">
                                <label for="year">Effective A.Y</label>
                                <input type="text" class="form-control" id="year" name="year" placeholder="Enter Effective Academic Year">
                            </div>
                            <div class="col-md-3">
                                <label for="program_id">Program</label>
                                <select class="form-control" id="program_id" name="program_id" required>
                                    <option value="" disabled selected>Select Program</option>
                                    <?php
                                    $query = $conn->query("SELECT id, program_code FROM program WHERE department_id = '$department_id' ORDER BY program_code ASC");
                                    while ($row = $query->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['program_code']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <div class="card-header p-3">
                        <h5 class="mb-0"><b>Course Details</b></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dynamic_field">
                                <thead>
                                    <tr>
                                        <th class="align-middle text-center">Action</th>
                                        <th>Period</th>
                                        <th>Year Level</th>
                                        <th style="width: 12%;">Course Code</th>
                                        <th>Course Name</th>
                                        <th style="width: 8%;">Lec</th>
                                        <th style="width: 8%;">Lab</th>
                                        <th style="width: 8%;">Units</th>
                                        <th class="align-middle text-center">Complab</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <input type="hidden" class="form-control" name="hours[]" id="hours1">
                                        <td class="align-middle text-center">
                                            <button type="button" class="add btn btn-primary btn-block btn-sm"><i class="fa fa-plus-circle"></i></button>
                                        </td>
                                        <td>
                                            <select class="form-control" id="period1" name="period[]">
                                                <option value="1st Semester">1st Semester</option>
                                                <option value="2nd Semester">2nd Semester</option>
                                                <option value="Mid Year">Mid Year</option>
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
                                        <td><input type="text" class="form-control" name="course_code[]" id="code1"></td>
                                        <td><input type="text" class="form-control" name="course_name[]" id="name1"></td>
                                        <td><input type="number" value="0" min="0" class="form-control" name="lec[]" id="lec1" onchange="calculateUnits(1)"></td>
                                        <td><input type="number" value="0" min="0" class="form-control" name="lab[]" id="lab1" onchange="calculateUnits(1)"></td>
                                        <td><input type="number" value="0" min="0" class="form-control" name="units[]" id="units1" readonly></td>
                                        <td class="text-center">
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
                        <button type="submit" class="btn btn-flat btn-success"><i class="fa fa-check-circle"></i> Save Changes</button>
                    </div>
            </form>
        </div>
    </div>
    </div>
</section>


<script>
    function formatHours(hours) {
        var hoursInt = Math.floor(hours);
        var minutes = (hours - hoursInt) * 60;
        minutes = Math.round(minutes);
        return hoursInt + ':' + (minutes < 10 ? '0' : '') + minutes;
    }

    function calculateUnits(index) {
        var lec = document.getElementById('lec' + index).value;
        var lab = document.getElementById('lab' + index).value;

        lec = parseInt(lec) || 0;
        lab = parseInt(lab) || 0;

        var units = lec + Math.floor(lab / 3);

        document.getElementById('units' + index).value = units;

        var hours = lec + lab;
        document.getElementById('hours' + index).value = formatHours(hours);

    }

    $(document).ready(function() {
        var no = 1;

        $('.add').on('click', function(e) {
            no++;

            $('#dynamic_field').append(`<tr id='row${no}'>
                <input type="hidden" class="form-control" name="hours[]" id="hours${no}">
                <td class="align-middle text-center"><button class='btn btn-sm btn-danger remove btn-block' id='${no}'><i class='fa fa-times'></i></button></td>
                <td><select class='form-control' name='period[]' id='period${no}'><option value='1st Semester'>1st Semester</option><option value='2nd Semester'>2nd Semester</option><option value='Mid Year'>Mid Year</option></select></td>
                <td><select class='form-control' name='level[]' id='level${no}'><option value='1st Year'>1st Year</option><option value='2nd Year'>2nd Year</option><option value='3rd Year'>3rd Year</option><option value='4th Year'>4th Year</option></select></td>
                <td><input type='text' class='form-control' name='course_code[]' id='code${no}'></td>
                <td><input type='text' class='form-control' name='course_name[]' id='name${no}'></td>
                <td><input type='number' value="0" min="0" class='form-control' name='lec[]' id='lec${no}' onchange='calculateUnits(${no})'></td>
                <td><input type='number' value="0" min="0" class='form-control' name='lab[]' id='lab${no}' onchange='calculateUnits(${no})'></td>
                <td><input type='number' value="0" min="0" class='form-control' name='units[]' id='units${no}' readonly></td>
                <td align='center'><select class='form-control' id='comlab${no}' name='is_comlab[]'><option value='0'>No</option><option value='1'>Yes</option></select></td>
            </tr>`);
            e.preventDefault();
            return false;
        });

        $('#dynamic_field').on('click', '.remove', function(e) {
            var button_id = $(this).attr("id");
            $("#row" + button_id).remove();
            no--;
            e.preventDefault();
            return false;
        });

        document.getElementById('manage_course').addEventListener('submit', function(e) {
            e.preventDefault();

            start_load();
            var form = this;
            var formData = $(form).serialize();

            console.log(formData);
            $.ajax({
                url: 'ajax.php?action=save_course',
                method: 'POST',
                data: formData,
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