<?php
include '../admin/db_connect.php';
if (isset($_GET['id'])) {
	$user = $conn->query("SELECT * FROM users where id =" . $_GET['id']);
	foreach ($user->fetch_array() as $k => $v) {
		$meta[$k] = $v;
	}
}
?>
<style>
	.password-container {
		position: relative !important;
	}

	.eye-icon {
		position: absolute !important;
		top: 50%;
		right: 10px;
		transform: translateY(-50%);
		cursor: pointer;
		color: #6c757d;
		cursor: pointer;
	}
</style>
<div class="container-fluid">
	<div id="msg"></div>

	<form action="" id="manage-user">
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id'] : '' ?>">
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username'] : '' ?>" required autocomplete="off">
			<div class="invalid-feedback">
				Please enter your username.
			</div>
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<div class="password-container">
				<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
				<i class="fa fa-eye eye-icon" onclick="toggleVisibility()"></i>
			</div>
			<?php if (isset($meta['id'])) : ?>
				<small><i>Leave this blank if you don't want to change the password.</i></small>
			<?php endif; ?>
		</div>
		<div class="form-group">
			<label class="control-label">Program</label>
			<select class="form-control" name="program_id" id="program_name">
				<?php
				$program_name = $conn->query("SELECT id, program_name FROM program");
				while ($row = $program_name->fetch_assoc()) :
					$selected = isset($meta['program_id']) && $meta['program_id'] == $row['id'] ? 'selected' : '';
				?>
					<option value="<?php echo $row['id'] ?>" <?php echo $selected ?>><?php echo $row['program_name'] ?></option>
				<?php endwhile; ?>
			</select>
		</div>

	</form>
</div>
<script>
	function toggleVisibility() {
		var passwordField = $('#password');
		var fieldType = passwordField.attr('type');

		if (fieldType === 'password') {
			passwordField.attr('type', 'text');
			$('.eye-icon').removeClass('fa-eye').addClass('fa-eye-slash');
		} else {
			passwordField.attr('type', 'password');
			$('.eye-icon').removeClass('fa-eye-slash').addClass('fa-eye');
		}
	}

	$('#manage-user').submit(function(e) {
		e.preventDefault();
		start_load();

		var form = $(this)[0];

		if (form.checkValidity() === false) {
			$(this).addClass('was-validated');
			return;
		}

		$.ajax({
			url: '../admin/ajax.php?action=save_user',
			method: 'POST',
			data: $(this).serialize(),
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Data successfully saved", 'success');
					setTimeout(function() {
						location.reload();
					}, 1500);
				} else if (resp == 2) {
					alert_toast("Data successfully updated", 'success');
					setTimeout(function() {
						location.reload();
					}, 1500);
				} else if (resp === 3) {
					$('#msg').html('<div class="alert alert-danger">A user account for this program already exists.</div>');
					end_load();
				} else {
					$('#msg').html('<div class="alert alert-danger">Invalid Credentials</div>');
					end_load();
				}
			}
		});
	});
</script>