<?php
include '../db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['level'])) {

    $value = $_GET['instructor'];
    $level = $_GET['level'];

    $collection = [];

    if (!$curriculum->isEmpty()) {
        foreach ($curriculum as $curricula) {
            $offering_query = "SELECT * FROM offerings_infos_table WHERE curriculum_id = {$curricula->id}";
            $offering_result = mysqli_query($connection, $offering_query);

            if ($offering_result && mysqli_num_rows($offering_result) > 0) {
                while ($offer = mysqli_fetch_assoc($offering_result)) {
                    $schedules_query = "SELECT DISTINCT offering_id FROM room_schedules WHERE offering_id = {$offer['id']} AND instructor IS NULL AND is_active = 1";
                    $schedules_result = mysqli_query($connection, $schedules_query);

                    if ($schedules_result && mysqli_num_rows($schedules_result) > 0) {
                        while ($schedule = mysqli_fetch_assoc($schedules_result)) {
                            $collection[] = (object)[
                                'level' => $offer['level'],
                                'offering_id' => $offer['id'],
                                'section_name' => $offer['section_name'],
                                'curriculum_id' => $curricula->id
                            ];
                        }
                    }
                }
            }
        }
    }

    $color_array = ['info', 'danger', 'warning', 'danger'];
    $ctr = 0;
}
?>
<div class='col-sm-12'>
    <?php if (!empty(array_filter($collection, function ($data) use ($level) {
        return $data->level == $level;
    }))) { ?>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Course</th>
                <th>Schedule</th>
                <th>Add to Calendar</th>
            </tr>
            <?php foreach (array_filter($collection, function ($data) use ($level) {
                return $data->level == $level;
            }) as $data) {
                $curricula_query = "SELECT * FROM curriculum WHERE id = {$data->curriculum_id}";
                $curricula_result = mysqli_query($connection, $curricula_query);
                $curricula = mysqli_fetch_assoc($curricula_result); ?>
                <tr>
                    <td>
                        <div align="center"><?php echo $curricula['course_code']; ?><br><?php echo $data->section_name; ?>
                        </div>
                    </td>
                    <td>
                        <div class='callout callout-<?php echo $color_array[$ctr]; ?>'>
                            <div align="center">
                                <?php
                                $schedule3_query = "SELECT DISTINCT room FROM room_schedules WHERE offering_id = {$data->offering_id}";
                                $schedule3_result = mysqli_query($connection, $schedule3_query);
                                if ($schedule3_result && mysqli_num_rows($schedule3_result) > 0) {
                                    while ($schedule3 = mysqli_fetch_assoc($schedule3_result)) {
                                        echo $schedule3['room'];
                                    }
                                }
                                ?><br>
                                <?php
                                $schedule2_query = "SELECT DISTINCT time_starts, time_end, room FROM room_schedules WHERE offering_id = {$data->offering_id}";
                                $schedule2_result = mysqli_query($connection, $schedule2_query);
                                if ($schedule2_result && mysqli_num_rows($schedule2_result) > 0) {
                                    while ($schedule2 = mysqli_fetch_assoc($schedule2_result)) {
                                        $days_query = "SELECT day FROM room_schedules WHERE offering_id = {$data->offering_id} AND time_starts = '{$schedule2['time_starts']}' AND time_end = '{$schedule2['time_end']}' AND room = '{$schedule2['room']}'";
                                        $days_result = mysqli_query($connection, $days_query);
                                        if ($days_result && mysqli_num_rows($days_result) > 0) {
                                            while ($day = mysqli_fetch_assoc($days_result)) {
                                                echo $day['day'];
                                            }
                                        }
                                        echo date('g:iA', strtotime($schedule2['time_starts'])) . '-' . date('g:iA', strtotime($schedule2['time_end'])) . "<br>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-primary add-to-calendar" data-offering-id="<?php echo $data->offering_id; ?>">Add</button>
                    </td>
                </tr>
            <?php $ctr++;
            } ?>
        </table>
    <?php } else { ?>
        <div class='row'>
            <div class="callout callout-warning">
                <div align="center">
                    <h5>No Course to be Found!</h5>
                </div>
            </div>
        </div>
    <?php } ?>
</div>