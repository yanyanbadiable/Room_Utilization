<?php
include('db_connect.php');

// Prepare and execute the query
$program_query = $conn->prepare("SELECT * FROM program");
$program_query->execute();

// Get the result set
$program_result = $program_query->get_result();

// Fetch all rows as associative arrays
$programs = $program_result->fetch_all(MYSQLI_ASSOC);





?>
<style>
    .card-header {
        border-bottom: none;
    }

    .form-control {
        width: auto;
        height: auto;

    }
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
            <div class="card card-default">
                <form id="manage_course">
                    <div class="card-header bg-transparent"><i></i>
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
                                        <td><button type="button" class="add btn btn-flat btn-primary"><i class="fa fa-plus-circle"></i></button></td>

                                        <td>
                                            <select class=" form-control" id="period1" name="period[]">
                                                <option value="1st Semester">1st Semester</option>
                                                <option value="2nd Semester">2nd Semester</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class=" form-control" id="level1" name="level[]">
                                                <option value="1st Year">1st Year</option>
                                                <option value="2nd Year">2nd Year</option>
                                                <option value="3rd Year">3rd Year</option>
                                                <option value="4th Year">4th Year</option>
                                                <option value="5th Year">5th Year</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control " id="program1" name="program_id[]">
                                                <?php foreach ($programs as $program) : ?>
                                                    <option value="<?php echo $program['id'] ?>"><?php echo $program['program_code']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control  " name="course_code[]" id="code1"></td>
                                        <td><input type="text" class="form-control  " name="course_name[]" id="name1"></td>
                                        <td><input type="number" class="form-control" name="lec[]" id="lec1"></td>
                                        <td><input type="number" class="form-control" name="lab[]" id="lab1"></td>
                                        <td><input type="number" class="form-control" name="units[]" id="units1"></td>
                                        <td align="center"><select class='form-control' id='complab1' name='is_comlab[]'>
                                                <option value='0'>No</option>
                                                <option value='1'>Yes</option>
                                            </select></td>
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
    var no = 1;

    document.addEventListener('DOMContentLoaded', function() {
        var no = 1; // Initialize row counter
        document.querySelector('.add').addEventListener('click', function(event) {
            event.preventDefault();
            var newRow = document.createElement('tr');
            newRow.id = 'row' + no; // Set unique ID for the new row
            newRow.innerHTML = `
            <td><button type="button" class='btn btn-flat remove d-block' id='${no}'><i class='fas fa-times'></i></button></td>
      
            <td><select class='form-control' name='period[]' id='period${no}'><option value='1st Semester'>1st Semester</option><option value='2nd Semester'>2nd Semester</option></select></td>
            <td><select class='form-control' name='level[]' id='level${no}'><option value='1st Year'>1st Year</option><option value='2nd Year'>2nd Year</option><option value='3rd Year'>3rd Year</option><option value='4th Year'>4th Year</option><option value='5th Year'>5th Year</option></select></td>
            <td><select class='form-control' name='program_id[]' id='program${no}'>
                <?php foreach ($programs as $program) : ?>
                    <option value='<?php echo $program['id']; ?>'><?php echo $program['program_code']; ?></option>
                <?php endforeach; ?>
            </select></td>
            <td><input type='text' class='form-control' name='course_code[]' id='code${no}'></td>
            <td><input type='text' class='form-control' name='course_name[]' id='name${no}'></td>
            <td><input type='text' class='form-control' name='lec[]' id='lec${no}'></td>
            <td><input type='text' class='form-control' name='lab[]' id='lab${no}'></td>
            <td><input type='text' class='form-control' name='units[]' id='units${no}'></td>
            <td align='center'><select class='form-control' id='complab${no}' name='is_comlab[]'><option value='0'>No</option><option value='1'>Yes</option></select></td>`;
            document.getElementById('dynamic_field').appendChild(newRow);
            no++; // Increment row counter for next row
        });

        document.getElementById('dynamic_field').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove')) {
                var button_id = e.target.getAttribute('id');
                document.getElementById('row' + button_id).remove();
                no--; // Decrement row counter when a row is removed
            }
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
                    if (resp == 1) {
                        alert_toast("Data successfully saved", 'success');
                        // setTimeout(function() {
                        //     location.reload();
                        // }, 100);
                    } else {
                        $('#msg').html('<div class="alert alert-danger">Invalid Credentials</div>');
                        end_load();
                    }
                }
            });
        });

    });
</script>