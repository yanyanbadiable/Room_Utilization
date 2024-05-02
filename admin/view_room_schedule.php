<?php include('db_connect.php'); ?>
<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM rooms WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Section Header -->
        <section class="content-header col-md-12 d-flex flex-wrap align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-cogs"></i> View Room Schedule</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="#"> Room Management</a></li>
                <li class="breadcrumb-item"><a href="#"> Room Schedules</a></li>
                <li class="breadcrumb-item active">View Rooms Schedule</li>
            </ol>
        </section>
    </div>
    <div class="row">
    <div class="col-lg-12">
        <div class="shadow mb-4">
            <div class="row">
                <div class="col-lg-12">
                    <div class="box box-default">
                        <div class="box-header">
                            <h3 class="box-title"></h3>
                            <div class="box-tools pull-right">
                                <!--<a  target="_blank" href="{{url('/registrar_college/print_curriculum',array($program_code,$curriculum_year))}}" class='btn btn-flat btn-primary'><i class='fa fa-print'></i> Print Curriculum</a>-->
                            </div>
                        </div>
                        <div class="box-body">
                            <?php
                            // Define the days of the week
                            $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

                            // Define time slots
                            $timeSlots = [
                                '08:00 AM - 09:00 AM',
                                '09:00 AM - 10:00 AM',
                                '10:00 AM - 11:00 AM',
                                '11:00 AM - 12:00 PM',
                                '12:00 PM - 01:00 PM',
                                '01:00 PM - 02:00 PM',
                                '02:00 PM - 03:00 PM',
                                '03:00 PM - 04:00 PM',
                                '04:00 PM - 05:00 PM',
                                '05:00 PM - 06:00 PM',
                            ];
                            ?>
                            <div class='table-responsive'>
                                <table class="table table-condensed">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <?php foreach ($daysOfWeek as $day): ?>
                                                <th><?php echo $day; ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($timeSlots as $timeSlot): ?>
                                            <tr>
                                                <td><?php echo $timeSlot; ?></td>
                                                <?php foreach ($daysOfWeek as $day): ?>
                                                    <td></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<!-- FullCalendar CSS -->
<link rel="stylesheet" href="node_modules/fullcalendar/dist/fullcalendar.min.css">

<!-- FullCalendar JavaScript -->
<script src="node_modules/fullcalendar/dist/fullcalendar.min.js"></script>
