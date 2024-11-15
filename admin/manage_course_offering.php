<?php
include('db_connect.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$years = [];

if (isset($_GET['program_id'])) {

    $program_code = $_GET['program_id'];

    $program_query = $conn->prepare("SELECT * FROM program WHERE id = ?");
    $program_query->bind_param("s", $program_code);
    $program_query->execute();
    $program_result = $program_query->get_result();
    $program = $program_result->fetch_assoc();

    $row = $program;

    $courses_query = $conn->prepare("SELECT DISTINCT year FROM courses WHERE program_id = ? ORDER BY year DESC");
    $courses_query->bind_param("s", $program_code);
    $courses_query->execute();
    $courses_result = $courses_query->get_result();
    $years = $courses_result->fetch_all(MYSQLI_ASSOC);
}
?>
<style>
    .card-header {
        border-bottom: none;
    }
</style>
<div class="container-fluid p-3">
    <div class="row">
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fa fa-hourglass-half"></i>
                Course Offering
                <small style="font-size: 1.1rem;"><?php echo $row['program_code'] ?></small>
            </h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> Course Management</li>
                <li class="breadcrumb-item active"><a href="index.php?page=course_offering">Course Offerings</a></li>
            </ol>
        </section>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-5 pl-0">
                    <div class="card card-solid card-default shadow mb-4">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="card-title m-0"><?php echo $row['program_name'] ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Year Level</label>
                                <select class="form-control" onchange="getSections(this.value)">
                                    <option>Please Select</option>
                                    <option>1st Year</option>
                                    <option>2nd Year</option>
                                    <option>3rd Year</option>
                                    <option>4th Year</option>
                                </select>
                            </div>
                            <div class="form-group" id="displaySections"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-7 pr-0">
                    <div class="card card-default shadow mb-4" id="displaySearchCourse" style="display: none;">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="card-title m-0">Search Course</h5>
                        </div>
                        <div class="card-body pb-3">
                            <div class="row pb-3 pt-2">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Curriculum Year</label>
                                        <select class="form-control" id="year">
                                            <?php foreach ($years as $year) : ?>
                                                <option value="<?php echo $year['year']; ?>"><?php echo $year['year']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Year Level</label>
                                        <select class="form-control" id='level' disabled>
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
                                            <option>Mid Year</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <a id="search_button" class="btn btn-flat btn-block btn-success text-white" onclick='searchCourse($("#year").val(), $("#level").val(), $("#period").val(), $("#section_id").val())'>Search</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 pl-0" id="displayCourses"></div>
                <div class="col-sm-6 pr-0" id="displayOffered"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function getSections(level) {
        var array = {};
        array['level'] = level;
        $('#level').val(level);
        array['program_code'] = "<?php echo $row['program_code'] ?>";
        $.ajax({
            type: "GET",
            url: "C_OfferingAjax/get_section.php",
            data: array,
            success: function(data) {
                $('#displaySections').html(data).fadeIn();

                if (level === "Please Select") {
                    $('#displaySearchCourse').hide();
                } else {
                    $('#displaySearchCourse').fadeIn();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching sections: ' + error);
            }
        });
    }

    function searchCourse(year, level, period, section_id) {
        var array = {};
        array['year'] = year;
        array['level'] = level;
        array['period'] = period;
        array['section_id'] = section_id;
        array['program_id'] = <?php echo $row['id'] ?>;

        if (section_id !== "" && section_id !== "Please Select") {
            $.ajax({
                type: "GET",
                url: "C_OfferingAjax/get_course.php",
                data: array,
                success: function(data) {
                    $('#displayCourses').html(data).fadeIn();
                    searchOffering(year, level, period, section_id);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching courses: ' + error);
                }
            });
        } else {
            alert_toast('Please select a valid section', 'warning');
        }
    }

    function searchOffering(year, level, period, section_id) {
        var array = {};
        array['year'] = year;
        array['level'] = level;
        array['period'] = period;
        array['section_id'] = section_id;

        $.ajax({
            type: "GET",
            url: "C_OfferingAjax/get_course_offered.php",
            data: array,
            success: function(data) {
                $('#displayOffered').html(data).fadeIn();
            },
            error: function(xhr, status, error) {
                alert_toast('Error fetching offered courses: ' + error, 'error');
            }
        });
    }
</script>