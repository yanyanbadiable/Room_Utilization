<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
	$id = $_GET['id'];

	// Fetch user
	$userQuery = "SELECT * FROM users WHERE id = ?";
	$stmt = $conn->prepare($userQuery);
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$userResult = $stmt->get_result();
	$user = $userResult->fetch_assoc();
	$stmt->close();

	// Fetch instructor info along with program code
	$infoQuery = "SELECT faculty.*, program.program_code 
	              FROM faculty 
	              LEFT JOIN program ON faculty.program_id = program.id 
	              WHERE faculty.user_id = ?";
	$stmt = $conn->prepare($infoQuery);
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$infoResult = $stmt->get_result();
	$info = $infoResult->fetch_assoc();
	$stmt->close();

	// Fetch programs
	$programsQuery = "SELECT DISTINCT id, program_name, program_code FROM program";
	$programsResult = $conn->query($programsQuery);
	$programs = [];
	while ($row = $programsResult->fetch_assoc()) {
		$programs[] = $row;
	}
}
?>

<div class="container-fluid">
	<section class="content-header row align-items-center justify-content-between mb-3">
		<h3><i class="fa fa-user-edit"></i> Update Instructor</h3>
		<ol class="breadcrumb bg-transparent p-0 m-0">
			<li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
			<li class="breadcrumb-item active">Update Instructor</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<form class="form-horizontal needs-validation col-md-12" method='post' id="instructorForm" novalidate>
				<div class="card card-default shadow mb-4">
					<div class="card-header">
						<h3 class="card-title"><b>Personal Information</b></h3>
					</div>
					<div class="card-body">
						<input type="hidden" name="id" value="<?php echo $id; ?>">
						<div class="form-group row">
							<div class="col-md-3">
								<label><b>ID Number</b></label>
								<input class="form-control" value="<?php echo $user['username']; ?>" name="username" placeholder="ID Number*" type="text" required>
								<div class="invalid-feedback">ID Number is required.</div>
							</div>
							<div class="col-md-9">
								<label><b>Email</b></label>
								<input class="form-control" name='email' value="<?php echo $info['email']; ?>" placeholder='Email Address' type="email" required>
								<div class="invalid-feedback">Valid Email Address is required.</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-3">
								<label><b>First Name</b></label>
								<input class="form-control" value="<?php echo $user['fname']; ?>" name='firstname' placeholder='First Name*' type="text" required>
								<div class="invalid-feedback">First Name is required.</div>
							</div>
							<div class="col-md-3">
								<label><b>Middle Name</b></label>
								<input class="form-control" value="<?php echo $user['mname']; ?>" name='middlename' placeholder='Middle Name' type="text">
							</div>
							<div class="col-md-3">
								<label><b>Last Name</b></label>
								<input class="form-control" value="<?php echo $user['lname']; ?>" name='lastname' placeholder='Last Name*' type="text" required>
								<div class="invalid-feedback">Last Name is required.</div>
							</div>
							<div class="col-md-3">
								<label><b>Extension Name</b></label>
								<input class="form-control" value="<?php echo $user['extname']; ?>" name='extensionname' placeholder='Extension Name' type="text">
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6">
								<label><b>Address</b></label>
								<input class="form-control" value="<?php echo $info['street']; ?>" name='street' placeholder='Street Address' type="text">
							</div>
							<div class="col-md-6">
								<label><b>Barangay</b></label>
								<input class="form-control" value="<?php echo $info['barangay']; ?>" name='barangay' placeholder='Barangay*' type="text" required>
								<div class="invalid-feedback">Barangay is required.</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6">
								<label><b>Municipality/City</b></label>
								<input class="form-control" value="<?php echo $info['municipality']; ?>" name='municipality' placeholder='Municipality/City*' type="text" required>
								<div class="invalid-feedback">Municipality/City is required.</div>
							</div>
							<div class="col-md-6">
								<label><b>Province</b></label>
								<input class="form-control" value="<?php echo $info['province']; ?>" name='province' placeholder='Province*' type="text" required>
								<div class="invalid-feedback">Province is required.</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6">
								<label><b>Gender</b></label>
								<select class="form-control" name='gender' required>
									<option value=''>Select Gender</option>
									<option value='Male' <?php if ($info['gender'] == 'Male') echo "selected='selected'"; ?>>Male</option>
									<option value='Female' <?php if ($info['gender'] == 'Female') echo "selected='selected'"; ?>>Female</option>
								</select>
								<div class="invalid-feedback">Gender is required.</div>
							</div>
							<div class="col-md-6">
								<label><b>Contact Number</b></label>
								<input class="form-control" value="<?php echo $info['contact']; ?>" name='contact' placeholder='Contact Number' type="text" required pattern="^\+639\d{9}$">
								<div class="invalid-feedback">Valid Contact Number is required (12 digits starting with +639).</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card card-default shadow mb-4">
					<div class="card-header">
						<h3 class="card-title"><b>Other Information</b></h3>
					</div>
					<div class="card-body">
						<div class="form-group row">
							<div class="col-md-6">
								<label><b>Department</b></label>
								<select name="program" class="form-control" required>
									<option value="">Select Department</option>
									<?php foreach ($programs as $program) : ?>
										<option value="<?php echo $program['id']; ?>" <?php if ($info['program_code'] == $program['program_code']) echo "selected='selected'"; ?>><?php echo $program['program_code']; ?></option>
									<?php endforeach; ?>
								</select>
								<div class="invalid-feedback">Department is required.</div>
							</div>
							<div class="col-md-6">
								<label><b>Employee Status</b></label>
								<select name="designation" class="form-control" required>
									<option value="">Select Employee Type</option>
									<option value="Full Time" <?php if ($info['designation'] == 'Full Time') echo "selected='selected'"; ?>>Full Time</option>
									<option value="Part Time" <?php if ($info['designation'] == 'Part Time') echo "selected='selected'"; ?>>Part Time</option>
								</select>
								<div class="invalid-feedback">Employee Status is required.</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<input type='submit' class='btn btn-primary col-md-12' value='Update'>
					</div>
				</div>
			</form>
		</div>
	</section>
</div>

<script>
	var formIsValid = true;

	$('#instructorForm').submit(function(event) {
		event.preventDefault();

		if (!formIsValid) {
			return;
		}

		$('input[type="submit"]').prop('disabled', true);

		editUser();
		editInstructor();
	});

	function editUser() {
		if (!formIsValid) {
			return;
		}
		var array = {};
		array['id'] = $("input[name='id']").val();
		array['username'] = $("input[name='username']").val();
		array['firstname'] = $("input[name='firstname']").val();
		array['middlename'] = $("input[name='middlename']").val();
		array['lastname'] = $("input[name='lastname']").val();
		array['extensionname'] = $("input[name='extensionname']").val();
		array['program_id'] = $("select[name='program']").val();
		$.ajax({
			type: "POST",
			url: "ajax.php?action=edit_user",
			data: array,
			success: function(data) {
				if (data.trim() === '1') {
					alert_toast('User Successfully Updated', 'success');
					resetForm();
					reloadPage();
				} else {
					alert_toast('Failed to update user', 'danger');
					resetForm();
					reloadPage();
				}
			},
			error: function() {
				alert_toast('Something Went Wrong!', 'danger');
				resetForm();
				reloadPage();
			}
		});
	}

	function editInstructor() {
		if (!formIsValid) {
			return; // Do not proceed if form is not valid
		}
		var array = {};
		array['id'] = $("input[name='id']").val();
		array['program_id'] = $("select[name='program']").val();
		array['gender'] = $("select[name='gender']").val();
		array['designation'] = $("select[name='designation']").val();
		array['street'] = $("input[name='street']").val();
		array['barangay'] = $("input[name='barangay']").val();
		array['municipality'] = $("input[name='municipality']").val();
		array['province'] = $("input[name='province']").val();
		array['contact'] = $("input[name='contact']").val();
		array['email'] = $("input[name='email']").val();

		$.ajax({
			type: "POST",
			url: "ajax.php?action=edit_faculty",
			data: array,
			success: function(data) {
				if (data.trim() === '1') {
					alert_toast('Instructor Successfully Updated', 'success');
					resetForm(); // Reset the form
					reloadPage(); // Call reloadPage() instead of location.reload()
				} else {
					alert_toast('Failed to update instructor', 'danger');
					resetForm(); // Reset the form
					reloadPage(); // Call reloadPage() instead of location.reload()
				}
			},
			error: function() {
				alert_toast('Something Went Wrong!', 'danger');
				resetForm(); // Reset the form
				reloadPage(); // Call reloadPage() instead of location.reload()
			}
		});
	}

	function resetForm() {
		document.getElementById("instructorForm").reset(); // Reset the form
		$('input[type="submit"]').prop('disabled', false); // Enable the submit button
	}
</script>