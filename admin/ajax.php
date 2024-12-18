<?php
ob_start();
$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();
if ($action == 'login') {
	$login = $crud->login();
	if ($login)
		echo $login;
}
if ($action == 'logout') {
	$logout = $crud->logout();
	if ($logout)
		echo $logout;
}

if ($action == 'save_user') {
	$save = $crud->save_user();
	if ($save)
		echo $save;
}

if ($action == 'delete_user') {
	$delete = $crud->delete_user();
	if ($delete)
		echo $delete;
}

if ($action == "change_password") {
	$save = $crud->change_password();
	if ($save)
		echo $save;
}

if ($action == "account_setting") {
	$save = $crud->account_setting();
	if ($save)
		echo $save;
}

if ($action == "save_program") {
	$save = $crud->save_program();
	if ($save)
		echo $save;
}
if ($action == "delete_program") {
	$delete = $crud->delete_program();
	if ($delete)
		echo $delete;
}

if ($action == "save_department") {
	$save = $crud->save_department();
	if ($save)
		echo $save;
}
if ($action == "delete_department") {
	$delete = $crud->delete_department();
	if ($delete)
		echo $delete;
}

if ($action == "save_building") {
	$save = $crud->save_building();
	if ($save)
		echo $save;
}
if ($action == "delete_building") {
	$delete = $crud->delete_building();
	if ($delete)
		echo $delete;
}

if ($action == "save_academic_rank") {
	$save = $crud->save_academic_rank();
	if ($save)
		echo $save;
}
if ($action == "delete_academic_rank") {
	$delete = $crud->delete_academic_rank();
	if ($delete)
		echo $delete;
}

if ($action == "save_designation") {
	$save = $crud->save_designation();
	if ($save)
		echo $save;
}
if ($action == "delete_designation") {
	$delete = $crud->delete_designation();
	if ($delete)
		echo $delete;
}

if ($action == "save_semester") {
	$save = $crud->save_semester();
	if ($save)
		echo $save;
}
if ($action == "delete_semester") {
	$delete = $crud->delete_semester();
	if ($delete)
		echo $delete;
}

if ($action == "save_course") {
	$save = $crud->save_course();
	if ($save)
		echo $save;
}

if ($action == "edit_course") {
	$edit = $crud->edit_course();
	if ($edit)
		echo $edit;
}

if ($action == "edit_year") {
	$edit = $crud->edit_year();
	if ($edit)
		echo $edit;
}

if ($action == "delete_course") {
	$delete = $crud->delete_course();
	if ($delete)
		echo $delete;
}

if ($action == "save_section") {
	$save = $crud->save_section();
	if ($save)
		echo $save;
}

if ($action == "delete_section") {
	$delete = $crud->delete_section();
	if ($delete)
		echo $delete;
}

if ($action == "save_room") {
	$save = $crud->save_room();
	if ($save)
		echo $save;
}
if ($action == "delete_room") {
	$delete = $crud->delete_room();
	if ($delete)
		echo $delete;
}

if ($action == "save_faculty") {
	$save = $crud->save_faculty();
	if ($save)
		echo $save;
}
if ($action == 'edit_faculty') {
	$save = $crud->edit_faculty();
	if ($save)
		echo $save;
}


if ($action == "remove_schedule") {
	$save = $crud->remove_schedule();
	if ($save)
		echo $save;
}

if ($action == "attach_schedule") {
	$crud = $crud->attach_schedule();
	if ($crud)
		echo $crud;
}

if ($action == "delete_schedule") {
	$save = $crud->delete_schedule();
	if ($save)
		echo $save;
}


if ($action == "add_course_offer") {
	$save = $crud->add_course_offer();
	if ($save)
		echo $save;
}

if ($action == "remove_course_offer") {
	$save = $crud->remove_course_offering();
	if ($save)
		echo $save;
}

if ($action == "add_schedule") {
	$save = $crud->add_schedule();
	if ($save)
		echo $save;
}

if ($action == "remove_schedule") {
	$save = $crud->remove_schedule();
	if ($save)
		echo $save;
}

if ($action == "add_faculty_load") {
	$save = $crud->add_faculty_load();
	if ($save)
		echo $save;
}

if ($action == "add_overload") {
	$save = $crud->add_overload();
	if ($save)
		echo $save;
}

if ($action == "remove_faculty_load") {
	$save = $crud->remove_faculty_load();
	if ($save)
		echo $save;
}

ob_end_flush();
