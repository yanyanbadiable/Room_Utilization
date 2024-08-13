<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
	$id = $_GET['id'];

	$facultyQuery = "
    SELECT faculty.*, 
           program.program_code, 
           unit_loads.designation 
    FROM faculty 
    INNER JOIN program ON faculty.program_id = program.id 
    INNER JOIN unit_loads ON faculty.designation = unit_loads.id 
    WHERE faculty.id = ?";

	if ($stmt = $conn->prepare($facultyQuery)) {
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$facultyResult = $stmt->get_result();
		$faculty = $facultyResult->fetch_assoc();
		$stmt->close();
	} else {
		echo "Error preparing statement: (" . $conn->errno . ") " . $conn->error;
	}

	$programsQuery = "SELECT DISTINCT id, program_code FROM program";
	$programResult = mysqli_query($conn, $programsQuery);

	if ($programResult) {
		$programs = [];
		while ($row = mysqli_fetch_assoc($programResult)) {
			$programs[] = $row;
		}
	} else {
		echo "Error fetching programs: " . mysqli_error($conn);
	}

	$designationsQuery = "SELECT DISTINCT id, designation FROM unit_loads";
	$designationResult = mysqli_query($conn, $designationsQuery);

	if ($designationResult) {
		$designations = [];
		while ($row = mysqli_fetch_assoc($designationResult)) {
			$designations[] = $row;
		}
	} else {
		echo "Error fetching designations: " . mysqli_error($conn);
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
								<input class="form-control" value="<?php echo $faculty['id_number']; ?>" name="id_number" placeholder="ID Number*" type="text" required>
								<div class="invalid-feedback">ID Number is required.</div>
							</div>
							<div class="col-md-9">
								<label><b>Email</b></label>
								<input class="form-control" name='email' value="<?php echo $faculty['email']; ?>" placeholder='Email Address' type="email" required>
								<div class="invalid-feedback">Valid Email Address is required.</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-3">
								<label><b>First Name</b></label>
								<input class="form-control" value="<?php echo $faculty['fname']; ?>" name='firstname' placeholder='First Name*' type="text" required>
								<div class="invalid-feedback">First Name is required.</div>
							</div>
							<div class="col-md-3">
								<label><b>Middle Name</b></label>
								<input class="form-control" value="<?php echo $faculty['mname']; ?>" name='middlename' placeholder='Middle Name' type="text">
							</div>
							<div class="col-md-3">
								<label><b>Last Name</b></label>
								<input class="form-control" value="<?php echo $faculty['lname']; ?>" name='lastname' placeholder='Last Name*' type="text" required>
								<div class="invalid-feedback">Last Name is required.</div>
							</div>
							<div class="col-md-3">
								<label><b>Extension Name</b></label>
								<input class="form-control" value="<?php echo $faculty['extname']; ?>" name='extensionname' placeholder='Extension Name' type="text">
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6">
								<label><b>Address</b></label>
								<input class="form-control" value="<?php echo $faculty['street']; ?>" name='street' placeholder='Street Address' type="text">
							</div>
							<div class="col-md-6">
								<label><b>Barangay</b></label>
								<input class="form-control" value="<?php echo $faculty['barangay']; ?>" name='barangay' placeholder='Barangay*' type="text" required>
								<div class="invalid-feedback">Barangay is required.</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6">
								<label><b>Municipality/City</b></label>
								<input class="form-control" value="<?php echo $faculty['municipality']; ?>" name='municipality' placeholder='Municipality/City*' type="text" required>
								<div class="invalid-feedback">Municipality/City is required.</div>
							</div>
							<div class="col-md-6">
								<label><b>Province</b></label>
								<input class="form-control" value="<?php echo $faculty['province']; ?>" name='province' placeholder='Province*' type="text" required>
								<div class="invalid-feedback">Province is required.</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6">
								<label><b>Sex</b></label>
								<select class="form-control" name='gender' required>
									<option value=''>Select Sex</option>
									<option value='Male' <?php if ($faculty['gender'] == 'Male') echo "selected='selected'"; ?>>Male</option>
									<option value='Female' <?php if ($faculty['gender'] == 'Female') echo "selected='selected'"; ?>>Female</option>
								</select>
								<div class="invalid-feedback">Gender is required.</div>
							</div>
							<div class="col-md-6">
								<label><b>Contact Number</b></label>
								<input class="form-control" value="<?php echo $faculty['contact']; ?>" name='contact' placeholder='Contact Number' type="text" required pattern="^\+639\d{9}$">
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
									<option value="" disabled selected hidden>Select Department</option>
									<?php foreach ($programs as $program) : ?>
										<option value="<?php echo $program['id']; ?>" <?php if ($faculty['program_code'] == $program['program_code']) echo "selected='selected'"; ?>>
											<?php echo $program['program_code']; ?>
										</option>
									<?php endforeach; ?>
								</select>
								<div class="invalid-feedback">Department is required.</div>
							</div>
							<div class="col-md-6">
								<label><b>Employee Status</b></label>
								<select name="designation" class="form-control" required>
									<option value="" disabled selected hidden>Select Employee Type</option>
									<?php foreach ($designations as $designation) : ?>
										<option value="<?php echo $designation['id']; ?>" <?php if ($faculty['designation'] == $designation['designation']) echo "selected='selected'"; ?>>
											<?php echo $designation['designation']; ?>
										</option>
									<?php endforeach; ?>
								</select>
								<div class="invalid-feedback">Employee Status is required.</div>
							</div>
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
	$(document).ready(function() {

		$('#instructorForm').submit(function(event) {
			event.preventDefault();


			if (this.checkValidity() === false) {
				$(this).addClass('was-validated');
				return;
			}

			$('input[type="submit"]').prop('disabled', true);
			editInstructor();
		});
	});



	function editInstructor() {
		var array = {};
		array['id'] = $("input[name='id']").val();
		array['id_number'] = $("input[name='id_number']").val();
		array['firstname'] = $("input[name='firstname']").val();
		array['middlename'] = $("input[name='middlename']").val();
		array['lastname'] = $("input[name='lastname']").val();
		array['extensionname'] = $("input[name='extensionname']").val();
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
					// resetForm();
					location.reload();
				} else {
					alert_toast('Failed to update instructor', 'danger');
					$('input[type="submit"]').prop('disabled', false);
				}
			},
			error: function() {
				alert_toast('Something Went Wrong!', 'danger');
				$('input[type="submit"]').prop('disabled', false);
			}
		});
	}

	function resetForm() {
		$('#instructorForm')[0].reset();
		$('#instructorForm').removeClass('was-validated');
		$('input[type="submit"]').prop('disabled', false);
	}
</script>