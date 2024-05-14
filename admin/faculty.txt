<?php include('db_connect.php'); ?>
<style>
	input[type=checkbox] {
		/* Double-sized Checkboxes */
		-ms-transform: scale(1.5);
		/* IE */
		-moz-transform: scale(1.5);
		/* FF */
		-webkit-transform: scale(1.5);
		/* Safari and Chrome */
		-o-transform: scale(1.5);
		/* Opera */
		transform: scale(1.5);
		padding: 10px;
	}
	.card-header{
		border-bottom: none; 
	}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<!-- Section Header -->
			<section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
				<h3><i class="fas fa-users"></i> Faculty List</h3>
				<ol class="breadcrumb bg-transparent p-0 m-0">
					<li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
					<li class="breadcrumb-item active"> Faculty Management</li>
					<li class="breadcrumb-item active"> View Faculty</li>
				</ol>
			</section>
			<section class="content">
				<div class="card shadow mb-4">
					<div class="card-header bg-transparent">
						<h5>Faculty List</h5>
						<!-- <span class="">

							<button class="btn btn-primary btn-block btn-sm col-sm-2 float-right" type="button" id="new_faculty">
								<i class="fa fa-plus"></i> New</button>
						</span> -->
					</div>
					<div class="card-body">

						<table class="table table-bordered table-condensed table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">ID No</th>
									<th class="">Name</th>
									<th class="">Designation</th>
									<th class="">Email</th>
									<th class="">Contact</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$i = 1;
								$faculty =  $conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name from faculty order by concat(lastname,', ',firstname,' ',middlename) asc");
								while ($row = $faculty->fetch_assoc()) :
								?>
									<tr>

										<td class="text-center"><?php echo $i++ ?></td>
										<td class="">
											<p><b><?php echo $row['id_no'] ?></b></p>

										</td>
										<td class="">
											<p><b><?php echo ucwords($row['name']) ?></b></p>

										</td>
										<td class="">
											<p><b><?php echo $row['designation'] ?></b></p>

										</td>
										<td class="">
											<p><b><?php echo $row['email'] ?></b></p>
										</td>
										<td class="">
											<p><b><?php echo $row['contact'] ?></b></p>

										</td>
										<td class="text-center">
											<button class="btn btn-sm btn-outline-primary view_faculty" type="button" data-id="<?php echo $row['id'] ?>">View</button>
											<button class="btn btn-sm btn-outline-primary edit_faculty" type="button" data-id="<?php echo $row['id'] ?>">Edit</button>
											<button class="btn btn-sm btn-outline-danger delete_faculty" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
										</td>
									</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</section>
		</div>
		<!-- Table Panel -->
	</div>
</div>

</div>
<style>
	td {
		vertical-align: middle !important;
	}

	td p {
		margin: unset
	}

	img {
		max-width: 100px;
		max-height: s150px;
	}
</style>
<script>
	$(document).ready(function() {
		$('table').dataTable()
	})
	$('#new_faculty').click(function() {
		uni_modal("New Entry", "manage_faculty.php", 'mid-large')
	})
	$('.view_faculty').click(function() {
		uni_modal("Faculty Details", "view_faculty.php?id=" + $(this).attr('data-id'), '')

	})
	$('.edit_faculty').click(function() {
		uni_modal("Manage Job Post", "manage_faculty.php?id=" + $(this).attr('data-id'), 'mid-large')

	})
	$('.delete_faculty').click(function() {
		_conf("Are you sure to delete this topic?", "delete_faculty", [$(this).attr('data-id')], 'mid-large')
	})

	function delete_faculty($id) {
		start_load()
		$.ajax({
			url: 'ajax.php?action=delete_faculty',
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