<?php include 'db_connect.php' ?>
<?php
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$qry = $conn->query("SELECT * FROM schedules where id=$id")->fetch_array();
	foreach ($qry as $k => $v) {
		$$k = $v;
	}

	$faculty_result = $conn->query("SELECT CONCAT(lastname, ', ', firstname, ' ', middlename) as faculty_name FROM faculty WHERE id=$faculty_id");
    $faculty_row = $faculty_result->fetch_assoc();
    $faculty_name = $faculty_row['faculty_name'];

	$schedule = $conn->query("SELECT 
		schedules.*, rooms.room, subjects.subject 
		FROM schedules 
		INNER JOIN rooms ON schedules.room_id = rooms.id 
		INNER JOIN subjects ON schedules.subject_id = subjects.id 
		WHERE schedules.id = $id")->fetch_array();
	foreach ($schedule as $k => $v) {
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<p>Schedule for: <b><?php echo ucwords($faculty_name) ?></b></p>
	<p>Subject: <b><?php echo $subject ?></b></p>
	<p>Room: <b><?php echo $room ?></b></p>
	<p>Time Start: <b><?php echo date('h:i A', strtotime("2020-01-01 $time_from")) ?></b></p>
	<p>Time End: <b><?php echo date('h:i A', strtotime("2020-01-01 $time_to")) ?></b></p>
	<hr class="divider">
</div>
<div class="modal-footer display">
	<div class="row">
		<div class="col-md-12">
			<button class="btn float-right btn-secondary" type="button" data-dismiss="modal">Close</button>
			<button class="btn float-right btn-danger mr-2" type="button" id="delete_schedule">Delete</button>
			<button class="btn float-right btn-primary mr-2" type="button" id="edit">Edit</button>
		</div>
	</div>
</div>
<style>
	p {
		margin: unset;
	}

	#uni_modal .modal-footer {
		display: none;
	}

	#uni_modal .modal-footer.display {
		display: block;
	}
</style>
<script>
	$('#edit').click(function() {
		uni_modal('Edit Schedule', 'manage_schedule.php?id=<?php echo $id ?>', 'mid-large')
	})
	$('#delete_schedule').click(function() {
		_conf("Are you sure to delete this schedule?", "delete_schedule", [$(this).attr('data-id')])
	})

	function delete_schedule($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_schedule',
			method: 'POST',
			data: {
				id: $id
			},
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Data successfully deleted", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)

				}
			}
		})
	}
</script>