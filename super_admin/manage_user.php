<?php
include '../admin/db_connect.php';
if (isset($_GET['id'])) {
	$user = $conn->query("SELECT * FROM users where id =" . $_GET['id']);
	foreach ($user->fetch_array() as $k => $v) {
		$meta[$k] = $v;
	}
}
?>
<div class="container-fluid">
	<div id="msg"></div>

	<form action="" id="manage-user">
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id'] : '' ?>">
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username'] : '' ?>" required autocomplete="off">
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
			<?php if (isset($meta['id'])) : ?>
				<small><i>Leave this blank if you don't want to change the password.</i></small>
			<?php endif; ?>
		</div>
		<div class="form-group">
			<label class="control-label">Department</label>
			<select class="form-control" name="program_id" id="department">
				<?php
				$department = $conn->query("SELECT id, department FROM program");
				while ($row = $department->fetch_assoc()) :
					$selected = isset($meta['program_id']) && $meta['program_id'] == $row['id'] ? 'selected' : '';
				?>
					<option value="<?php echo $row['id'] ?>" <?php echo $selected ?>><?php echo $row['department'] ?></option>
				<?php endwhile; ?>
			</select>
		</div>

	</form>
</div>
<script>
	$('#manage-user').submit(function(e) {
		e.preventDefault();
		start_load()
		$.ajax({
			url: '../admin/ajax.php?action=save_user',
			method: 'POST',
			data: $(this).serialize(),
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Data successfully saved", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)
				} else if (resp == 2) {
					alert_toast("Data successfully updated", 'success')
					setTimeout(function() {
						location.reload()
					}, 1500)
				} else {
					$('#msg').html('<div class="alert alert-danger">Invalid Credentials</div>')
					end_load()
				}
			}
		})
	})
</script>