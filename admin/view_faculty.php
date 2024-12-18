<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
	$id = $_GET['id'];

	$facultyQuery = "
    SELECT faculty.*, 
           program.program_name, 
           unit_loads.academic_rank,
		   designation.designation 
    FROM faculty 
    LEFT JOIN program ON faculty.program_id = program.id 
    INNER JOIN unit_loads ON faculty.academic_rank = unit_loads.id 
	LEFT JOIN designation ON faculty.designation = designation.id 
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

	$program_id = $_SESSION['login_department_id'];
	$programsQuery = "SELECT id, program_name FROM program WHERE department_id = ?";
	$stmt = $conn->prepare($programsQuery);

	if ($stmt) {
		$stmt->bind_param("i", $program_id);
		$stmt->execute();
		$result = $stmt->get_result();

		$programs = [];
		while ($program = $result->fetch_assoc()) {
			$programs[] = $program;
		}
	}

	$designationsQuery = "SELECT DISTINCT id, designation FROM designation";
	$designationResult = mysqli_query($conn, $designationsQuery);

	if ($designationResult) {
		$designations = [];
		while ($row = mysqli_fetch_assoc($designationResult)) {
			$designations[] = $row;
		}
	} else {
		echo "Error fetching designations: " . mysqli_error($conn);
	}

	$academic_ranksQuery = "SELECT DISTINCT id, academic_rank FROM unit_loads";
	$academic_rankResult = mysqli_query($conn, $academic_ranksQuery);

	if ($academic_rankResult) {
		$academic_ranks = [];
		while ($row = mysqli_fetch_assoc($academic_rankResult)) {
			$academic_ranks[] = $row;
		}
	} else {
		echo "Error fetching academic_ranks: " . mysqli_error($conn);
	}
}
?>


<div class="container-fluid p-3">
	<section class="content-header row align-items-center justify-content-between mb-3 px-3">
		<h3><i class="fa fa-user-edit"></i> Update Instructor</h3>
		<ol class="breadcrumb bg-transparent p-0 m-0">
			<li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
			<li class="breadcrumb-item active"><a href="index.php?page=faculty">View Instructor</a></li>
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
								<label><b>Gender</b></label>
								<select class="form-control" name='gender' required>
									<option value=''>Select Gender</option>
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
								<label><b>Employee Academic Rank</b></label>
								<select name="academic_rank" class="form-control select2" required>
									<option value="" disabled selected hidden>Select Employee Academic Rank</option>
									<?php foreach ($academic_ranks as $academic_rank) : ?>
										<option value="<?php echo $academic_rank['id']; ?>" <?php if ($faculty['academic_rank'] == $academic_rank['academic_rank']) echo "selected='selected'"; ?>>
											<?php echo $academic_rank['academic_rank']; ?>
										</option>
									<?php endforeach; ?>
								</select>
								<div class="invalid-feedback">Academic Rank is required.</div>
							</div>
							<div class="col-md-6">
								<label><b>Employee Designation</b></label>
								<select name="designation" id="designation" class="form-control select2">
									<option value="" disabled selected hidden>Select Employee Designation</option>
									<?php foreach ($designations as $designation) : ?>
										<option value="<?php echo $designation['id']; ?>" <?php if ($faculty['designation'] == $designation['designation']) echo "selected='selected'"; ?>>
											<?php echo $designation['designation']; ?>
										</option>
									<?php endforeach; ?>
								</select>
								<!-- <div class="invalid-feedback">Employee Designation is required.</div> -->
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-6">
								<label><b>Program</b></label>
								<select class="form-control select2" name="program_id" id="program_id" required>
									<option value="" disabled selected hidden>Select Program</option>
									<?php foreach ($programs as $program) : ?>
										<option value="<?php echo $program['id']; ?>" <?php if ($faculty['program_name'] == $program['program_name']) echo "selected='selected'"; ?>>
											<?php echo $program['program_name']; ?>
										</option>
									<?php endforeach; ?>
								</select>
								<div class="invalid-feedback">Program is required.</div>
							</div>
							<div class="col-md-6">
								<label><b>Post Graduate Studies</b></label>
								<input class="form-control" name="post_graduate_studies" value="<?php echo $faculty['post_graduate_studies']; ?>" placeholder='Postgraduate Studies' type="text">
								<!-- <div class="invalid-feedback">Municipality/City is required.</div> -->
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
		$('#designation').change(function() {
			var selectedDesignation = $(this).find('option:selected').text().trim();
			console.log(selectedDesignation);

			if (selectedDesignation === "Head") {
				$('#program_id').prop('disabled', true).val('');
			} else {
				$('#program_id').prop('disabled', false);
			}
		});

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
		array['program_id'] = $("select[name='program_id']").val();
		array['gender'] = $("select[name='gender']").val();
		array['academic_rank'] = $("select[name='academic_rank']").val();
		array['designation'] = $("select[name='designation']").val();
		array['post_graduate_studies'] = $("input[name='post_graduate_studies']").val();
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
					window.location.href = 'index.php?page=faculty';
					// location.reload();
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