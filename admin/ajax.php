<?php
ob_start();
$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();
if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login_faculty'){
	$login_faculty = $crud->login_faculty();
	if($login_faculty)
		echo $login_faculty;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == 'update_account'){
	$save = $crud->update_account();
	if($save)
		echo $save;
}
if($action == "save_settings"){
	$save = $crud->save_settings();
	if($save)
		echo $save;
}
if($action == "save_department"){
	$save = $crud->save_department();
	if($save)
		echo $save;
}
if($action == "delete_department"){
	$delete = $crud->delete_department();
	if($delete)
		echo $delete;
}

if($action == "save_building"){
	$save = $crud->save_building();
	if($save)
		echo $save;
}
if($action == "delete_building"){
	$delete = $crud->delete_building();
	if($delete)
		echo $delete;
}

if($action == "save_course"){
	$save = $crud->save_course();
	if($save)
		echo $save;
}

if($action == "delete_course"){
	$delete = $crud->delete_course();
	if($delete)
		echo $delete;
}

if($action == "save_section"){
	$save = $crud->save_section();
	if($save)
		echo $save;
}

if($action == "delete_section"){
	$delete = $crud->delete_section();
	if($delete)
		echo $delete;
}

if($action == "save_room"){
	$save = $crud->save_room();
	if($save)
		echo $save;
}
if($action == "delete_room"){
	$delete = $crud->delete_room();
	if($delete)
		echo $delete;
}

// if($action == "save_courses"){
// 	$save = $crud->save_courses();
// 	if($save)
// 		echo $save;
// }

// if($action == "delete_courses"){
// 	$delete = $crud->delete_subject();
// 	if($delete)
// 		echo $delete;
// }

if($action == "save_faculty"){
	$save = $crud->save_faculty();
	if($save)
		echo $save;
}
if($action == "delete_faculty"){
	$save = $crud->delete_faculty();
	if($save)
		echo $save;
}

if($action == "save_schedule"){
	$save = $crud->save_schedule();
	if($save)
		echo $save;
}
if($action == "delete_schedule"){
	$save = $crud->delete_schedule();
	if($save)
		echo $save;
}
if($action == "get_schedule"){
	$get = $crud->get_schedule();
	if($get)
		echo $get;
}

if($action == "get_section"){
	$get = $crud->get_section();
	if($get)
		echo $get;
}


ob_end_flush();
?>
