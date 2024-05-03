<?php
include('db_connect.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if program code is set in the URL
if (isset($_GET['program_id'])) {
    // Get the program code from the URL parameter
    $program_code = $_GET['program_id'];

    // Query to fetch the program details based on program code
    $program_query = $conn->prepare("SELECT * FROM program WHERE id = ?");
    $program_query->bind_param("s", $program_code);
    $program_query->execute();
    $program_result = $program_query->get_result();
    $program = $program_result->fetch_assoc();

    // Assign program details to $row
    $row = $program;

    $courses_query = $conn->prepare("SELECT * FROM courses WHERE program_id = ?");
    $courses_query->bind_param("s", $program_code);
    $courses_query->execute();
    $courses_result = $courses_query->get_result();
    $courses = $courses_result->fetch_all(MYSQLI_ASSOC);

    // Fetch years from the courses result
    $years = array_column($courses, 'year');
}
?>
<style>
    .card-header {
        border-bottom: none;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fa fa-hourglass-half"></i>
                Course Offering
                <small style="font-size: 1.1rem;"><?php echo $row['program_code'] ?></small>
            </h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> Course Management</li>
                <li class="breadcrumb-item active">Course Offerings</li>
            </ol>
        </section>

        <div class="container-fluid" style="margin-top: 15px;">
            <div class="row">
                <div class="col-sm-5">
                    <div class="card card-solid card-default shadow mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title"><?php echo $row['program_name'] ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Level</label>
                                <select class="form-control" onchange="getsections(this.value)">
                                    <option>Please Select</option>
                                    <option>1st Year</option>
                                    <option>2nd Year</option>
                                    <option>3rd Year</option>
                                    <option>4th Year</option>
                                </select>
                            </div>
                            <div class="form-group" id="displaysections">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="card card-default shadow mb-4" id="displaysearchcourse">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title">Search Course</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Curriculum Year</label>
                                        <select class="form-control" id="cy">
                                            <?php foreach (array_unique($years) as $year) : ?>
                                                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Level</label>
                                        <select class="form-control" id='level'>
                                            <option>1st Year</option>
                                            <option>2nd Year</option>
                                            <option>3rd Year</option>
                                            <option>4th Year</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Period</label>
                                        <select class="form-control" id='period'>
                                            <option>1st Semester</option>
                                            <option>2nd Semester</option>
                                            <option>Summer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <!-- Pass the values of cy, level, and period to the searchcourse function -->
                                <a id="search_button" class="btn btn-flat btn-block btn-success text-white" onclick='searchcourse($("#cy").val(), $("#level").val(), $("#period").val(), $("#section_name").val())'>Search</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6" id="displaycourses">

                </div>
                <div class="col-sm-6" id="displayoffered"></div>
            </div>
        </div>
    </div>
</div>
<script>
    function getsections(level) {
        $.ajax({
            type: "GET",
            url: "ajax.php?action=get_section",
            data: {
                level: level
            },
            success: function(response) {
                var data = JSON.parse(response);
                $('#displaysections').html(data.html);
                $('#displaysearcourse');
            },
            error: function() {
                console.error('Error fetching sections.');
            }
        });
    }

    // Add onchange event handlers to the curriculum year and section dropdowns
    $('#cy, #section_name').on('change', function() {
        // Check if both the curriculum year and section have values
        var cy = $("#cy").val();
        var section_name = $("#section_name").val();
        if (cy && section_name) {
            // Enable the search button
            $('#search_button').prop('disabled', false);
        } else {
            // Disable the search button
            $('#search_button').prop('disabled', true);
        }
    });

    function searchcourse(cy, level, period, section_name) {
        var array = {};
        array['cy'] = cy;
        array['level'] = level;
        array['period'] = period;
        array['section_name'] = section_name;
        // Assuming you have already defined these variables in your JavaScript or they are coming from somewhere else
        var program_id = '<?php echo $program_code; ?>'; // Get the program_id from PHP
        // Assuming you have an input element with the id 'section_name'
        var section_name = $("#section_name").val(); // Get the section name from the input field
        var curriculum_year = cy; // Use the selected curriculum year

        if (section_name != "") {
            $.ajax({
                type: "GET",
                url: "add_course_offer.php?program_id=" + program_id + "&section_name=" + section_name + "&curriculum_year=" + curriculum_year,
                data: array,
                success: function(data) {
                    $('#displaycourses').html(data).fadeIn();
                    searchoffering(cy, level, period, section_name, program_id); // Pass program_id to searchoffering function
                },
                error: function() {
                    console.error('Error fetching courses.');
                }
            })
        } else {
            toast.error('Please input a section', 'Notification!');
        }
    }


    function searchoffering(cy, level, period, section_name, program_id) {
        var array = {};
        array['cy'] = cy;
        array['level'] = level;
        array['period'] = period;
        array['section_name'] = section_name;
        array['program_id'] = program_id; // Include program_id in the data

        $.ajax({
            type: "GET",
            url: "course_offered.php",
            data: array,
            success: function(data) {
                $('#displayoffered').html(data).fadeIn();
            },
            error: function() {
                console.error('Error fetching offered courses.');
            }
        });
    }
</script>