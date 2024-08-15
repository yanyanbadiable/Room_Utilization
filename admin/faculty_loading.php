<?php
include('db_connect.php');

$query = "SELECT * FROM faculty";
$result = $conn->query($query);

$instructors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }
}
?>

<div class="container-fluid p-2">
    <div class="row">
        <div class="col-sm-12">
            <!-- Section Header -->
            <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3 p-0">
                <h3><i class="fa fa-calendar"></i> Faculty Loading</h3>
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"> Faculty Loading</li>
                </ol>
            </section>
            <div class="container-fluid p-0" style="margin-top: 15px;">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h5 class="m-0" >Search by Instructor</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group" id="displayLevel">
                                    <label>Level</label>
                                    <select class="form-control" id="level">
                                        <option>Please Select</option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group" id="displayInstructor">
                                    <label>Instructor</label>
                                    <select class="form-control" id="instructor">
                                        <option>Please Select</option>
                                        <?php foreach ($instructors as $instructor) : ?>
                                            <?php
                                            $middle_initial = !empty($instructor['mname']) ? strtoupper(substr($instructor['mname'], 0, 1)) . '.' : '';
                                            $name = $instructor['lname'] . ', ' . $instructor['fname'] . ' ' . $middle_initial; 
                                            ?>
                                            <option value="<?php echo $instructor['id']; ?>"><?php echo strtoupper($name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4" id="displaySearch">
                                <label>Search</label>
                                <button class='btn btn-flat btn-primary btn-block' onclick='displayCourses(level.value,instructor.value)'>Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-sm-5' id='displayCourses'></div>
                    <div class='col-sm-7' id='displayCalendar'></div>
                </div>
            </div>

            <div id="displayGetUnitsLoaded"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        
        $('#displayInstructor').hide();
        $('#displaySearch').hide();

        $('#displayLevel').on('change', function() {
            $('#displayInstructor').fadeIn();
        })

        $('#displayInstructor').on('change', function() {
            $('#displaySearch').fadeIn();
        })
    })


    function displayCourses(level, instructor) {
        var array = {};
        array['level'] = level;
        array['instructor'] = instructor;
        $.ajax({
            type: "GET",
            url: "FL_Ajax/courses_to_load.php",
            data: array,
            success: function(data) {
                $('#displayCourses').html(data).fadeIn();
                getCurrentLoad(instructor, level);
            }
        })
    }

    function search(event, value, level) {
        var array = {};
        array['value'] = value;
        array['level'] = level;
        $.ajax({
            type: "GET",
            url: "FL_Ajax/search_courses.php",
            data: array,
            success: function(data) {
                $('#searchCourses').html(data).fadeIn();
            }
        })
    }

    function getCurrentLoad(instructor, level) {
        var array = {};
        array['instructor'] = instructor;
        array['level'] = level;
        $.ajax({
            type: "GET",
            url: "FL_Ajax/current_load.php",
            data: array,
            success: function(data) {
                $('#displayCalendar').html(data).fadeIn();
            }
        })
    }

</script>