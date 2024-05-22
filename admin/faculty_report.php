<?php include('db_connect.php'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
                <h3><i class="far fa-file-alt"></i> Faculty Reports</h3>
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"> Report Management</li>
                    <li class="breadcrumb-item active"> Faculty Reports</li>
                </ol>
            </section>
            <section class="content">
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h5>List of Instructors</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="15%">ID Number</th>
                                                    <th width="35%">Name</th>
                                                    <th width="40%">College</th>
                                                    <th width="10%">Faculty Loading</th>
                                                </tr>
                                            </thead>
                                    </thead>
                                    <tbody>
                                        @foreach($instructors as $instructor)
                                        <?php $users = \App\User::where('id', $instructor->id)->first(); ?>
                                        <tr>
                                            <td>{{$instructor->username}}</td>
                                            <td>{{strtoupper($instructor->lastname)}}, {{strtoupper($instructor->name)}}</td>
                                            <?php $info = \App\instructors_infos::where('instructor_id', $instructor->id)->first(); ?>
                                            <td>{{$info->department}} {{$info->college}}</td>
                                            <td><a href="{{url('/admin/instructor/edit_faculty_loading',array($instructor->id))}}" class="btn btn-flat btn-success"><i class="fa fa-calendar-check-o"></i></a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script src='{{asset('plugins/datatables/jquery.dataTables.js')}}'></script>
<script src='{{asset('plugins/datatables/dataTables.bootstrap.js')}}'></script>
<script>
    $('#example1').DataTable();
</script>