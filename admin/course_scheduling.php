<?php include('db_connect.php'); ?>

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
                                <div class='form-group'>
                                    <label for="program_code">Academic Program</label>
                                    <?php
                                    $program_id = $_SESSION['login_program_id'];

                                    $query = "SELECT program_code FROM program WHERE id = ?";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("i", $program_id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $program = $result->fetch_assoc();
                                    ?>
                                     <input type="text" class='form-control' id='program_code' value="<?php echo $program['program_code']; ?>" disabled>
                                </div>
                            </div>
                            <div class='col-sm-4'>
                                <div class='form-group'>
                                    <label>Year Level</label>
                                    <select class=' form-control' id='level' onchange='getSection(program_code.value,this.value)'>
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
    function getSection(program_code, level) {
        var array = {};
        array['program_code'] = program_code;
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


    function getCoursesOffered(program_code, level, section_id) {
        var array = {};
        array['program_code'] = program_code;
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