
<style>
	.icon-field{
		font-size: 20px;
	}
	.collapse a{
		text-indent:10px;
	}
	nav#sidebar{
		background-color: #A70B0B;
		border-block: 1px solid rgba(255,255,255, 0.50);
	} 
	nav#sidebar .nav-item { 
		color: #ffffff;
		background-color: #A70B0B;
		border-block: 1px solid rgba(255,255,255, 0.40);
	} 
	nav#sidebar a.nav-item:hover, nav#sidebar .nav-item.active {
    	background-color: #731010;
    	color: #fffafa;
	}
</style>

<nav id="sidebar" style="background-color: #A70B0B;">
		
		<div class="sidebar-list" >
				<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-home"></i></span> Home</a>
				<a href="index.php?page=department" class="nav-item nav-department"><span class='icon-field'><i class="far fa-map"></i></span> Department</a>
				<a href="index.php?page=building" class="nav-item nav-building"><span class='icon-field'><i class="fas fa-building"></i></span> Building</a>
				<a href="index.php?page=courses" class="nav-item nav-courses"><span class='icon-field'><i class="fa fa-list"></i></span> Course Offering</a>
				<a href="index.php?page=room" class="nav-item nav-room"><span class='icon-field'><i class="fas fa-door-open"></i></span> Room</a>
				<a href="index.php?page=faculty" class="nav-item nav-faculty"><span class='icon-field'><i class="fa fa-user-tie"></i></span> Faculty</a>
				<a href="index.php?page=schedule" class="nav-item nav-schedule"><span class='icon-field'><i class="fa fa-calendar-day"></i></span> Schedule</a>
				<?php if($_SESSION['login_type'] == 1): ?>
				<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users"></i></span> Users</a>
			<?php endif; ?>
		</div>

</nav>
<script>
	$('.nav_collapse').click(function(){
		console.log($(this).attr('href'))
		$($(this).attr('href')).collapse()
	})
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>
