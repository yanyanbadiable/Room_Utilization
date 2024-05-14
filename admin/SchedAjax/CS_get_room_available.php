<?php
include('../db_connect.php');

// Initialize $rooms to an empty array
$rooms = [];

// Check if it's an AJAX request and all required GET parameters are set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['day']) && isset($_GET['time_start']) && isset($_GET['time_end']) && isset($_GET['course_offering_info_id']) && isset($_GET['section_id'])) {
    $day = $_GET['day'];
    $time_start = date('H:i:s', strtotime($_GET['time_start']));
    $time_end = date('H:i:s', strtotime($_GET['time_end']));
    $course_offering_info_id = $_GET['course_offering_info_id'];
    $section_id = $_GET['section_id'];

    // Fetch conflict schedules
    $conflict_schedules = [];
    $conflict_query = "SELECT room_id FROM schedules
                      WHERE course_offering_info_id = '$course_offering_info_id'
                      AND day = '$day'
                      AND (
                          (time_start BETWEEN '$time_start' AND '$time_end')
                          OR (time_end BETWEEN '$time_start' AND '$time_end')
                          OR ('$time_start' BETWEEN time_start AND time_end)
                          OR ('$time_end' BETWEEN time_start AND time_end)
                      )";

    $conflict_result = mysqli_query($conn, $conflict_query);
    if ($conflict_result) {
        while ($row = mysqli_fetch_assoc($conflict_result)) {
            $conflict_schedules[] = $row['room_id'];
        }
    } else {
        echo "Error fetching conflict schedules: " . mysqli_error($conn);
    }


    // Fetch available rooms
    if (!empty($conflict_schedules)) {
        $room_conditions = implode(',', array_map('intval', $conflict_schedules));
        $query = "SELECT rooms.*, building.building FROM rooms
              JOIN building ON rooms.building_id = building.id
              WHERE rooms.is_available = 1 AND rooms.id NOT IN ($room_conditions)";
    } else {
        $query = "SELECT rooms.*, building.building FROM rooms
              JOIN building ON rooms.building_id = building.id
              WHERE rooms.is_available = 1";
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
        // Close the modal
        $('#myModal').modal('hide'); // Replace 'myModal' with the ID of your modal
    });

    function submitScheduleForm(event) {
        event.preventDefault(); // Prevent default form submission

        var formData = $('#add_schedule').serialize(); // Serialize form data
        $.ajax({
            url: 'ajax.php?action=add_schedule',
            method: 'POST',
            data: formData,
            success: function(resp) {
                console.log(resp);
                if (resp.status === 'success') {
                    // If the response status is 'success', display a success message
                    alert_toast('Schedule successfully saved', 'success');
                    $.ajax({
                        type: "GET",
                        url: "ajax.php?action=get_schedule",
                        data: {
                            course_offering_info_id: <?= $course_offering_info_id ?>
                        },
                        success: function(data) {
                            $('#calendar').fullCalendar('removeEvents');
                            $('#calendar').fullCalendar('addEventSource', JSON.parse(data));
                            $('#calendar').fullCalendar('rerenderEvents');
                        }
                    });
                } else if (resp.status === 'error' && resp.message === 'Same schedule already exists.') {
                    // If the response status is 'error' and the message indicates that the schedule already exists, show appropriate message
                    alert_toast('Schedule already exists', 'warning');
                } else {
                    // If the response status is not 'success' or there's no specific message, show a generic error message
                    alert_toast(resp.message || 'Error occurred while saving schedule', 'danger');
                }
            },
            error: function() {
                // Handle error
                alert_toast('Something went wrong!', 'danger');
            }
        });
    }
</script>