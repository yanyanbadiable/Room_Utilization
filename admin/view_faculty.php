<?php
include 'db_connect.php';

if (isset($_GET['instructor_id'])) {
	$id_no = $_GET['instructor_id'];

	// Fetch user information
	$userStmt = $conn->prepare("SELECT * FROM users WHERE id_no = ?");
	if (!$userStmt) {
		die('Error preparing user statement: ' . $conn->error);
	}

	$userStmt->bind_param("s", $id_no);
	$userStmt->execute();
	$userResult = $userStmt->get_result();
	if (!$userResult) {
		die('Error executing user statement: ' . $userStmt->error);
	}

	$user_info = $userResult->fetch_assoc();

	// Fetch instructor information
	$instructorStmt = $conn->prepare("SELECT * FROM instructors_info WHERE id_no = ?");
	if (!$instructorStmt) {
		die('Error preparing instructor statement: ' . $conn->error);
	}

	$instructorStmt->bind_param("s", $id_no);
	$instructorStmt->execute();
	$instructorResult = $instructorStmt->get_result();
	if (!$instructorResult) {
		die('Error executing instructor statement: ' . $instructorStmt->error);
	}

	$instructor_info = $instructorResult->fetch_assoc();
}

$query = "SELECT DISTINCT id, program_name, program_code FROM program";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result) {
	// Fetch all rows from the result set
	$programs = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$programs[] = $row;
	}
} else {
	// Handle query error
	echo "Error: " . mysqli_error($conn);
}
?>
<div class="container-fluid">
	<div class="row">
		<section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
			<h3><i class="fa fa-bullhorn"></i> Update Instructor</h3>
			<ol class="breadcrumb bg-transparent p-0 m-0">
				<li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
				<li class="breadcrumb-item active">Update Instructor</li>
			</ol>
		</section>
		<section class="content">
			<div class="row">
				<form class="form-horizontal" method='post' action='admin/updateinstructor?id=<?php echo $id_no; ?>'>
					<div class="col-md-12">
						<div class="card shadow mb-4">
							<div class="card-header">
								<h3 class="card-title"><b>Personal Information</b></h3>
							</div>
							<div class="card-body">
								<div class="form-group row">
									<div class="col-sm-3">
										<label><b>ID Number</b></label>
										<input class="form-control" value="<?php echo $user_info['username']; ?>" name="username" placeholder="ID Number*" value="<?php $info('instructor_id'); ?>" type="text">
									</div>
									<div class="col-sm-9">
										<label><b>Email</b></label>
										<input class="form-control" name='email' value="<?php echo $user_info['email']; ?>" placeholder='Email Address' value="" type="email">
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-3">
										<label><b>Name</b></label>
										<input class="form-control" value="<?php echo $user_info['name']; ?>" name='name' placeholder='First Name*' value="<?php $info('name'); ?>" type="text">
									</div>
									<div class="col-sm-3">
										<label>&nbsp;</label>
										<input class="form-control" value="<?php echo $user_info['middlename']; ?>" name='middlename' placeholder='Middle Name' value="<?php $info('middlename'); ?>" type="text">
									</div>
									<div class="col-sm-3">
										<label>&nbsp;</label>
										<input class="form-control" value="<?php echo $user_info['lastname']; ?>" name='lastname' placeholder='Last Name*' value="<?php $info('lastname'); ?>" type="text">
									</div>
									<div class="col-sm-3">
										<label>&nbsp;</label>
										<input class="form-control" value="<?php echo $user_info['extensionname']; ?>" name='extensionname' placeholder='Extension Name' value="<?php $info('extensionname'); ?>" type="text">
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-6">
										<label><b>Address</b></label>
										<input class="form-control" value="<?php echo $info['street']; ?>" name='street' placeholder='Street Address' value="<?php $info('street'); ?>" type="text">
									</div>
									<div class="col-sm-6">
										<label>&nbsp;</label>
										<input class="form-control" value="<?php echo $info['barangay']; ?>" name='barangay' placeholder='Barangay' value="<?php $info('barangay'); ?>" type="text">
									</div>
								</div>
								<div class="form-group row">
									<div class="col-sm-6">
										<input class="form-control" value="<?php echo $info['municipality']; ?>" name='municipality' placeholder='Municipality/City' value="<?php $info('municipality'); ?>" type="text">
									</div>
									<div class="col-sm-6">
										<input class="form-control" name='province' placeholder='Province' value="<?php $info('province'); ?>" type="text">
									</div>
								</div>

								<div class="form-group row">
									<div class="col-sm-4">
										<label><b>Gender</b></label>
										<select class="select2 form-control" name='gender' type="text">
											<option value=''>Select Gender</option>
											<option <?php if ($info['gender'] == 'Male') echo 'selected="selected"'; ?> value='Male'>Male</option>
											<option <?php if ($info['gender'] == 'Female') echo 'selected="selected"'; ?> value='Female'>Female</option>
										</select>
									</div>
									<div class="col-sm-4">
										<label><b>Contact Number</b></label>
										<input class="form-control" value="<?php echo $info['tel_no']; ?>" name='tel_no' placeholder='Telephone Number' value="" type="text">
									</div>

									<div class="col-sm-4">
										<label><b>Cellphone Number</b></label>
										<input class="form-control" value="<?php echo $info['cell_no']; ?>" name='cell_no' placeholder='Cellphone Number' value="" type="text">
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-14">
							<div class="card shadow mb-4">
								<div class="card-header">
									<h3 class="card-title"><b>Other Information</b></h3>
								</div>
								<div class="card-body">

									<div class="col-sm-4">
										<label><b>College</b></label>
										<select name="department" class="select2 form-control">
											<option value="">Select College</option>
											<?php foreach ($programs as $program) : ?>
												<option <?php if ($info['department'] == $program['program_code']) echo 'selected="selected"'; ?> value="<?php echo $program['program_code']; ?>"><?php echo $program['program_code']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-sm-4">
										<label><b>Department</b></label>
										<select name="college" class="select2 form-control">
											<option value="">Select Department</option>
											<?php foreach ($programs as $program) : ?>
												<option <?php if ($info['college'] == $program['program_code']) echo 'selected="selected"'; ?> value="<?php echo $program['program_code']; ?>"><?php echo $program['program_code']; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-sm-4">
										<label><b>Employee Status</b></label>
										<select name="employee_type" class="select2 form-control">
											<option value="">Select Employee Type</option>
											<option <?php if ($info['employee_type'] == 'Full Time') echo 'selected="selected"'; ?> value="Full Time">Full Time</option>
											<option <?php if ($info['employee_type'] == 'Part Time') echo 'selected="selected"'; ?> value="Part Time">Part Time</option>
										</select>
									</div>
								</div>

							</div>
						</div>

						<div class='form-group row'>
							<div class='col-sm-12'>
								<input type='submit' class='col-sm-12 btn btn-primary' value='Update'>
							</div>
						</div>
				</form>
			</div>
		</section>

	</div>
</div>