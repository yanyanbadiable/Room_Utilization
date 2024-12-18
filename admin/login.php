<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('./db_connect.php');
ob_start();
ob_end_flush();
?>

<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">

	<title>ADRUFWMS</title>

	<?php include('../includes/header.php'); ?>
	<?php
	if (isset($_SESSION['login_id']))
		header("location:index.php?page=home");
	?>

</head>
<style>
	body {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
	}

	main {
		max-width: 100%;
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
		background-image: url("../assets/img/EvsuCover.jpg");
		background-color: rgba(30, 30, 30, 0.80);
		background-blend-mode: multiply;
		background-size: cover;
		background-position: center;
		background-repeat: no-repeat;

	}

	.container {
		display: flex;
		justify-content: center;
		align-items: center;
		position: relative;
	}

	.card {
		min-width: 370px;
		padding: 50px 30px;
		background-color: #ffffff;
		/* box-shadow: rgba(255, 255, 255, 0.30) 0px 3px 8px; */
		border-radius: 10px;
	}

	h2 {
		font-family: 'Anton', 'Arial Black', sans-serif;
		color: #d30707;
		font-weight: bold;
		letter-spacing: 2px;
		font-size: 50px;
	}

	button {
		margin-top: 30px;
		width: 100%;
		height: 40px;
		border: none;
		border-radius: 5px;
		background-color: #d30707;
		box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
		cursor: pointer;
	}

	button:disabled {
		box-shadow: none;
		cursor: not-allowed;
		opacity: 0.7;
	}

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

<body>
	<main>
		<div class="container">
			<div class="card shadow mb-4">
				<form id="login-form" class="">
					<div class="logo" id="logo">
						<h2 class="text-center mb-5">ADRUFWMS</h2>
					</div>
					<h3 class="mb-4 text-black">Sign In</h3>
					<div class="form-group mb-3">
						<input type="text" id="username" placeholder="Username" name="username" class="form-control" required>
						<div class="invalid-feedback">
							Please enter your username.
						</div>
					</div>
					<div class="form-group">
						<div class="password-container">
							<input type="password" id="password" name="password" placeholder="Password" class="form-control" required>
							<i class="fa fa-eye eye-icon" onclick="toggleVisibility()"></i>
							<div class="invalid-feedback">
								Please enter your password.
							</div>
						</div>
					</div>
					<button type="submit" class="align-center text-white">Login</button>
				</form>
			</div>
		</div>
	</main>
</body>
<script>
	$('#login-form').submit(function(e) {
		e.preventDefault();

		var form = $(this)[0];

		if (form.checkValidity() === false) {
			$(this).addClass('was-validated');
			return;
		}

		$('#login-form button[type="submit"]').attr('disabled', true).html('Logging in...');

		$.ajax({
			url: 'ajax.php?action=login',
			method: 'POST',
			data: $(this).serialize(),
			error: function(err) {
				console.log(err);
				$('#login-form button[type="submit"]').removeAttr('disabled').html('Login');
			},
			success: function(resp) {
				if (resp == 1) {
					location.href = 'index.php?page=home';
				} else if (resp == 2) {
					location.href = '../super_admin/index.php?page=home';
				} else {
					$('#login-form .alert-danger').remove();

					$('#login-form').prepend(`
                    <div class="alert alert-danger text-center invalid-feedback d-block">
                        Username or password is incorrect.
                    </div>
                `);
					$('#login-form button[type="submit"]').removeAttr('disabled').html('Login');

					setTimeout(function() {
						$('.alert-danger').fadeOut(1500, function() {
							$(this).remove();
						});
					}, 3500);
				}
			}
		});
	});



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
</script>

</html>