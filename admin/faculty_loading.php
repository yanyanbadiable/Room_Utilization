<?php include('db_connect.php'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <!-- Section Header -->
            <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
                <h3><i class="fa fa-calendar"></i> Faculty Loading</h3>
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"> Faculty Loading</li>
                </ol>
            </section>
            <div class="container-fluid" style="margin-top: 15px;">
                <div class="card">
                    <div class="card-header">
                        <h5>Search by Instructor</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group" id="displaylevel">
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
                                <div class="form-group" id="displayinstructor">
                                    <label>Instructor</label>
                                    <select class="select2 form-control" id="instructor">
                                        <option>Please Select</option>
                                        @foreach($instructors as $instructor)
                                        <option value="{{$instructor->id}}">{{strtoupper($instructor->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4" id="displaysearch">
                                <label>Search</label>
                                <button class='btn btn-flat btn-primary btn-block' onclick='displaycourses(level.value,instructor.value)'>Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-sm-5' id='displaycourses'></div>
                    <div class='col-sm-7' id='displaycalendar'></div>
                </div>
            </div>

            <div id="displaygetunitsloaded"></div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    $('.draggable').data('duration', '03:00');
    //hide yung instructor at search button
    $('#displayinstructor').hide();
    $('#displaysearch').hide();
    
    //lalabas yung instructor
    $('#displaylevel').on('change',function(){
        $('#displayinstructor').fadeIn();
    })
    
    //lalabas yung search button
    $('#displayinstructor').on('change',function(){
        $('#displaysearch').fadeIn();
    })
})


//upon clicking the search button
//kukuninniya lang yung available at walang nakafaculty load

function displaycourses(level,instructor){
    var array = {};
    array['level'] = level;
    array['instructor'] = instructor;
    $.ajax({
        type: "GET",
        url: "/ajax/admin/faculty_loading/courses_to_load",
        data: array,
        success: function(data){
            $('#displaycourses').html(data).fadeIn();
            init_events($('.draggable div.callout'));
            getCurrentLoad(instructor,level);
        }
    })
}

function search(event,value,level){
   var array = {};
   array['value'] = value;
   array['level'] = level;
   $.ajax({
       type: "GET",
       url: "/ajax/admin/faculty_loading/search_courses",
       data: array,
       success: function(data){
           $('#searchcourse').html(data).fadeIn();
           init_events($('.draggable div.callout'));
       }
   })
}

//mga nakafaculty load sa kanya sa ngayon.
function getCurrentLoad(instructor,level){
    var array = {};
    array['instructor'] = instructor;
    array['level'] = level;
    $.ajax({
        type: "GET",
        url: "/ajax/admin/faculty_loading/current_load",
        data: array,
        success: function(data){
            $('#displaycalendar').html(data).fadeIn();
        }
    })
}


function init_events(ele) {
    ele.each(function () {
      var eventObject = {
        title: $(this).attr("data-object")
      }
      $(this).data('eventObject', eventObject);
      $(this).draggable({
        zIndex        : 1070,
        revert        : true,
        revertDuration: 0 
      })
    })
}
</script>