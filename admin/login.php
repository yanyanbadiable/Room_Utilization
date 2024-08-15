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

	<title>ADRUMS</title>

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
		background-image: url("../assets/img/evsu-bg.jpg");
		background-color: rgba(30, 30, 30, 0.90);
		background-blend-mode: multiply;
		background-size: cover;
		background-position: center;
		background-repeat: no-repeat;

	}

	#container {
		display: flex;
		justify-content: center;
		align-items: center;
		position: relative;
	}

	form {
		width: 350px;
		padding: 50px 30px;
		background-color: #ffffff;
		box-shadow: rgba(255, 255, 255, 0.30) 0px 3px 8px;
		border-radius: 30px;
	}

	h1 {
		color: #d30707;
		font-weight: bold;
		text-align: center;
		margin-bottom: 30px;
	}

	button {
		margin-top: 30px;
		width: 100%;
		height: 40px;
		border: none;
		border-radius: 10px;
		background-color: #d30707;
		box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
		color: #ffffff;
	}

	.input-group {
		position: relative;
	}

	.input-group-append {
		position: absolute;
		right: 10px;
		top: 10px;
		cursor: pointer;
	}
</style>

<body>
	<main>
		<div id="container" class="container-lg">

			<form id="login-form" class="">
				<div class="logo" id="logo">
					<h1>LOGIN</h1>
				</div>
				<div class="form-group">
					<label for="username" class="control-label">Username</label>
					<input type="text" id="username" name="username" class="form-control">
				</div>
				<div class="form-group">
					<label for="password" class="control-label">Password</label>
					<input type="password" id="password" name="password" class="form-control">
					<!-- <div class="input-group">
						
						<div class="input-group-append m-0">
							<span id="togglePassword"><i class="fas fa-eye"></i></span>
						</div>
					</div> -->
				</div>
				<button class="align-center">Login</button>
				<!-- class="btn-md btn-block btn-wave col-md-4 btn-primary" -->
			</form>
		</div>
	</main>
	<a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>
</body>
<script>
	$('#login-form').submit(function(e) {
		e.preventDefault();
		$('#login-form button[type="button"]').attr('disabled', true).html('Logging in...');
		if ($(this).find('.alert-danger').length > 0)
			$(this).find('.alert-danger').remove();
		$.ajax({
			url: 'ajax.php?action=login',
			method: 'POST',
			data: $(this).serialize(),
			error: err => {
				console.log(err);
				$('#login-form button[type="button"]').removeAttr('disabled').html('Login');
			},
			success: function(resp) {
				if (resp == 1) {
					location.href = 'index.php?page=home';
				} else if (resp == 2) {
					location.href = '../super_admin/index.php?page=home';
				} else {
					$('#login-form').prepend('<div class="alert alert-danger text-center">Username or password is incorrect.</div>');
					$('#login-form button[type="button"]').removeAttr('disabled').html('Login');
				}
			}
		});
	});

	// $('#togglePassword').click(function() {
    //     let passwordField = $('#password');
    //     if(passwordField.attr('type') === 'password'){
    //         passwordField.attr('type', 'text');
    //         $(this).find('i').toggleClass('fas fa-eye fas fa-eye-slash');
    //     } else {
    //         passwordField.attr('type', 'password');
    //         $(this).find('i').toggleClass('fas fa-eye-slash fas fa-eye');
    //     }
    // });
</script>

</html>