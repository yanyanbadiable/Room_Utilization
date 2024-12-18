<?php
include('db_connect.php');
$user_department_id = $_SESSION['login_department_id'];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 p-0">
            <!-- Section Header -->
            <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
                <h3><i class="fa fa-spinner"></i> Course Scheduling</h3>
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"> Course Scheduling</li>
                    <li class="breadcrumb-item active"> Academic Program</li>
                </ol>
            </section>
            <div class="container-fluid p-2" style="margin-top: 15px;">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h5 class="card-title m-0">Academic Programs</h5>
                    </div>
                    <div class="card-body">
                        <div class='row'>
                            <div class='col-sm-4'>
                                <div class="form-group">
                                    <label class="control-label">Program Code</label>
                                    <select class="form-control" name="program_id" id="program_id" onchange="getSection(this.value, document.getElementById('level').value)">
                                        <option>Please Select</option>
                                        <?php
                                        $program = $conn->query("SELECT id, program_code FROM program WHERE department_id = $user_department_id");
                                        while ($row = $program->fetch_assoc()) { ?>
                                            <option value="<?php echo $row['id']; ?>" <?php echo isset($program_id) && $program_id == $row['id'] ? 'selected' : ''; ?>>
                                                <?php echo $row['program_code']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class='col-sm-4'>
                                <div class='form-group'>
                                    <label>Year Level</label>
                                    <select class='form-control' id='level' onchange='getSection(document.getElementById("program_id").value, this.value)'>
                                        <option>Please Select</option>
                                        <option value='1st Year'>1st Year</option>
                                        <option value='2nd Year'>2nd Year</option>
                                        <option value='3rd Year'>3rd Year</option>
                                        <option value='4th Year'>4th Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class='col-sm-4' id='displaySection'>
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id='displayOffered' onchange='getCoursesOffered()'></div>
            </div>
        </div>
    </div>
</div>
<script>
    function getSection(program_id, level) {
        var array = {};
        array['program_id'] = program_id;
        array['level'] = level;
        $.ajax({
            type: "GET",
            url: "SchedAjax/CS_get_section.php",
            data: array,
            success: function(data) {
                $('#displaySection').html(data).fadeIn();
                $('#displayOffered').html('').hide();
            },
            error: function() {
                console.error('Error fetching sections.');
            }
        });
    }

    function getCoursesOffered(program_id, level, section_id) {
        var array = {};
        array['program_id'] = program_id;
        array['level'] = level;
        array['section_id'] = section_id;
        $.ajax({
            type: "GET",
            url: "SchedAjax/CS_get_course_offered.php",
            data: array,
            success: function(data) {
                console.log(data);
                $('#displayOffered').html(data).fadeIn();
            }
        });
    }
</script>