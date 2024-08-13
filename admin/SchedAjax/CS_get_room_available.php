<?php
session_start();
include('../db_connect.php');

$rooms = [];
$user_program_id = $_SESSION['login_program_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['day']) && isset($_GET['time_start']) && isset($_GET['time_end']) && isset($_GET['course_offering_info_id']) && isset($_GET['section_id'])) {
    $day = $_GET['day'];
    $time_start = date('H:i', strtotime($_GET['time_start']));
    $time_end = date('H:i', strtotime($_GET['time_end']));
    $course_offering_info_id = $_GET['course_offering_info_id'];
    $section_id = $_GET['section_id'];

    $course_query = "SELECT c.is_comlab FROM courses c
                     JOIN course_offering_info coi ON c.id = coi.courses_id
                     WHERE coi.id = '$course_offering_info_id'";
    $course_result = mysqli_query($conn, $course_query);

    $is_comlab = 0;
    if ($course_result && $course_row = mysqli_fetch_assoc($course_result)) {
        $is_comlab = $course_row['is_comlab'];
    } else {
        echo "Error fetching course information: " . mysqli_error($conn);
    }

    $conflict_schedules = [];
    $conflict_query = "SELECT DISTINCT room_id FROM schedules
                       WHERE room_id IS NOT NULL
                       AND day = '$day'
                       AND (
                           (TIME_FORMAT(time_start, '%H:%i') < '$time_end' AND TIME_FORMAT(time_end, '%H:%i') > '$time_start')
                           OR (TIME_FORMAT(time_start, '%H:%i') >= '$time_start' AND TIME_FORMAT(time_end, '%H:%i') <= '$time_end')
                           OR (TIME_FORMAT(time_start, '%H:%i') <= '$time_start' AND TIME_FORMAT(time_end, '%H:%i') >= '$time_end')
                       )
                       AND course_offering_info_id != '$course_offering_info_id'";

    $conflict_result = mysqli_query($conn, $conflict_query);
    if ($conflict_result) {
        while ($row = mysqli_fetch_assoc($conflict_result)) {
            $conflict_schedules[] = $row['room_id'];
        }
    } else {
        echo "Error fetching conflict schedules: " . mysqli_error($conn);
    }

    if ($is_comlab == 1) {
        $lab_condition = "AND rooms.is_lab = 1";
    } else {
        $lab_condition = "AND rooms.is_lab = 0";
    }

    if (!empty($conflict_schedules)) {
        $room_conditions = implode(',', array_map('intval', $conflict_schedules));
        $query = "SELECT rooms.*, building.building FROM rooms
                  JOIN building ON rooms.building_id = building.id
                  WHERE rooms.is_available = 1 AND rooms.id NOT IN ($room_conditions) $lab_condition ORDER BY program_id";
    } else {
        $query = "SELECT rooms.*, building.building FROM rooms
                  JOIN building ON rooms.building_id = building.id
                  WHERE rooms.is_available = 1 $lab_condition  ORDER BY program_id";
    }

    $rooms_result = mysqli_query($conn, $query);
    if ($rooms_result) {
        while ($row = mysqli_fetch_assoc($rooms_result)) {
            $rooms[] = $row;
        }
    } else {
        echo "Error fetching available rooms: " . mysqli_error($conn);
    }
}
?>

<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
            <h4 class="modal-title">Available Rooms</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="text-white">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="" id="add_schedule" onsubmit="submitScheduleForm(event)">
                <input type="hidden" name="course_offering_info_id" value="<?= $course_offering_info_id ?>">
                <input type="hidden" name="day" value="<?= $day ?>">
                <input type="hidden" name="time_start" value="<?= $time_start ?>">
                <input type="hidden" name="time_end" value="<?= $time_end ?>">
                <input type="hidden" name="section_id" value="<?= $section_id ?>">
                <div class="form-group">
                    <label>Available Rooms</label>
                    <select name="room_id" class="form-control">
                        <?php foreach ($rooms as $room) : ?>
                            <option value="<?= $room['id'] ?>"><?= $room['room'] ?> <?= $room['building'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" id="submitBtn" class="btn btn-flat btn-success btn-block">Save and Submit</button>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
    $('#submitBtn').click(function() {
        $('#myModal').modal('hide');
    });

    function submitScheduleForm(event) {
        event.preventDefault();

        var formData = $('#add_schedule').serialize();
        $.ajax({
            url: 'ajax.php?action=add_schedule',
            method: 'POST',
            data: formData,
            success: function(resp) {
                console.log(resp);
                if (resp.status === 'success') {
                    alert_toast('Schedule successfully saved', 'success');
                    location.reload();
                } else if (resp.status === 'error' && resp.message === 'Same schedule already exists.') {
                    alert_toast('Schedule already exists', 'danger');
                } else {
                    alert_toast(resp.message || 'Error occurred while saving schedule', 'danger');
                }
            },
            error: function() {
                alert_toast('Something went wrong!', 'danger');
            }
        });
    }
</script>