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
	$programsQuery = "SELECT DISTINCT program_name, program_code FROM program";
	$programsResult = $conn->query($programsQuery);
	$programs = [];
	while ($row = $programsResult->fetch_assoc()) {
		$programs[] = $row;
	}
}
?>

<div class="container-fluid">
	<section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
		<h3><i class="fa fa-user-edit"></i> Update Instructor</h3>
		<ol class="breadcrumb bg-transparent p-0 m-0">
			<li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
			<li class="breadcrumb-item active">Update Instructor</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<form class="form-horizontal needs-validation" method='post' action='update_instructor.php?id=<?php echo $user['id']; ?>' novalidate>
				<div class="col-md-12">
					<div class="card card-default shadow mb-4">
						<div class="card-header">
							<h3 class="card-title"><b>Personal Information</b></h3>
						</div>
						<div class="card-body">
							<div class="form-group row">
								<div class="col-sm-3">
									<label><b>ID Number</b></label>
									<input class="form-control" value="<?php echo $user['username']; ?>" name="username" placeholder="ID Number*" type="text" required>
									<div class="invalid-feedback">ID Number is required.</div>
								</div>
								<div class="col-sm-9">
									<label><b>Email</b></label>
									<input class="form-control" name='email' value="<?php echo $info['email']; ?>" placeholder='Email Address' type="email" required>
									<div class="invalid-feedback">Valid Email Address is required.</div>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-3">
									<label><b>First Name</b></label>
									<input class="form-control" value="<?php echo $user['fname']; ?>" name='name' placeholder='First Name*' type="text" required>
									<div class="invalid-feedback">First Name is required.</div>
								</div>
								<div class="col-sm-3">
									<label>&nbsp;</label>
									<input class="form-control" value="<?php echo $user['mname']; ?>" name='middlename' placeholder='Middle Name' type="text">
								</div>
								<div class="col-sm-3">
									<label>&nbsp;</label>
									<input class="form-control" value="<?php echo $user['lname']; ?>" name='lastname' placeholder='Last Name*' type="text" required>
									<div class="invalid-feedback">Last Name is required.</div>
								</div>
								<div class="col-sm-3">
									<label>&nbsp;</label>
									<input class="form-control" value="<?php echo $user['extname']; ?>" name='extensionname' placeholder='Extension Name' type="text">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-6">
									<label><b>Address</b></label>
									<input class="form-control" value="<?php echo $info['street']; ?>" name='street' placeholder='Street Address' type="text">
								</div>
								<div class="col-sm-6">
									<label>&nbsp;</label>
									<input class="form-control" value="<?php echo $info['barangay']; ?>" name='barangay' placeholder='Barangay*' type="text" required>
									<div class="invalid-feedback">Barangay is required.</div>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-6">
									<input class="form-control" value="<?php echo $info['municipality']; ?>" name='municipality' placeholder='Municipality/City*' type="text" required>
									<div class="invalid-feedback">Municipality/City is required.</div>
								</div>
								<div class="col-sm-6">
									<input class="form-control" value="<?php echo $info['province']; ?>" name='province' placeholder='Province*' type="text" required>
									<div class="invalid-feedback">Province is required.</div>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-6">
									<label><b>Gender</b></label>
									<select class="form-control" name='gender' required>
										<option value=''>Select Gender</option>
										<option value='Male' <?php if ($info['gender'] == 'Male') echo "selected='selected'"; ?>>Male</option>
										<option value='Female' <?php if ($info['gender'] == 'Female') echo "selected='selected'"; ?>>Female</option>
									</select>
									<div class="invalid-feedback">Gender is required.</div>
								</div>
								<div class="col-sm-6">
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
								<div class="col-sm-6">
									<label><b>Department</b></label>
									<select name="program" class="form-control" required>
										<option value="">Select Department</option>
										<?php foreach ($programs as $program) : ?>
											<option value="<?php echo $program['program_code']; ?>" <?php if ($info['program_code'] == $program['program_code']) echo "selected='selected'"; ?>><?php echo $program['program_code']; ?></option>
										<?php endforeach; ?>
									</select>
									<div class="invalid-feedback">Department is required.</div>
								</div>
								<div class="col-sm-6">
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
						<div class="col-sm-12">
							<input type='submit' class='btn btn-primary col-sm-12' value='Update'>
						</div>
					</div>
			</form>
		</div>
	</section>
</div>
<script>
	document.getElementById("instructorForm").onsubmit = function(event) {
		var form = document.getElementById("instructorForm");

		if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
		}

		form.classList.add('was-validated');
	};
</script>