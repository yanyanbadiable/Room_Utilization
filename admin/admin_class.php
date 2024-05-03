<?php
session_start();
ini_set('display_errors', 1);
class Action
{
	private $db;

	public function __construct()
	{
		ob_start();
		include 'db_connect.php';

		$this->db = $conn;
	}
	function __destruct()
	{
		$this->db->close();
		ob_end_flush();
	}

	function login()
	{

		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '" . $username . "' and password = '" . md5($password) . "' ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			if ($_SESSION['login_type'] != 1) {
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				return 2;
				exit;
			}
			return 1;
		} else {
			return 3;
		}
	}
	function login_faculty()
	{

		extract($_POST);
		$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM faculty where id_no = '" . $id_no . "' ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			return 1;
		} else {
			return 3;
		}
	}
	function login2()
	{

		extract($_POST);
		if (isset($email))
			$username = $email;
		$qry = $this->db->query("SELECT * FROM users where username = '" . $username . "' and password = '" . md5($password) . "' ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			if ($_SESSION['login_alumnus_id'] > 0) {
				$bio = $this->db->query("SELECT * FROM alumnus_bio where id = " . $_SESSION['login_alumnus_id']);
				if ($bio->num_rows > 0) {
					foreach ($bio->fetch_array() as $key => $value) {
						if ($key != 'password' && !is_numeric($key))
							$_SESSION['bio'][$key] = $value;
					}
				}
			}
			if ($_SESSION['bio']['status'] != 1) {
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				return 2;
				exit;
			}
			return 1;
		} else {
			return 3;
		}
	}
	function logout()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user()
	{
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		if (!empty($password))
			$data .= ", password = '" . md5($password) . "' ";
		$data .= ", type = '$type' ";
		$data .= ", department_id = '$department_id' ";

		$chk = $this->db->query("Select * from users where username = '$username' and id !='$id' ")->num_rows;
		if ($chk > 0) {
			return 2;
			exit;
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users set " . $data);
		} else {
			$save = $this->db->query("UPDATE users set " . $data . " where id = " . $id);
		}
		if ($save) {
			return 1;
		}
	}
	function delete_user()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = " . $id);
		if ($delete)
			return 1;
	}
	function signup()
	{
		extract($_POST);
		$data = " name = '" . $firstname . ' ' . $lastname . "' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '" . md5($password) . "' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if ($chk > 0) {
			return 2;
			exit;
		}
		$save = $this->db->query("INSERT INTO users set " . $data);
		if ($save) {
			$uid = $this->db->insert_id;
			$data = '';
			foreach ($_POST as $k => $v) {
				if ($k == 'password')
					continue;
				if (empty($data) && !is_numeric($k))
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if ($_FILES['img']['tmp_name'] != '') {
				$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
				$data .= ", avatar = '$fname' ";
			}
			$save_alumni = $this->db->query("INSERT INTO alumnus_bio set $data ");
			if ($data) {
				$aid = $this->db->insert_id;
				$this->db->query("UPDATE users set alumnus_id = $aid where id = $uid ");
				$login = $this->login2();
				if ($login)
					return 1;
			}
		}
	}
	function update_account()
	{
		extract($_POST);
		$data = " name = '" . $firstname . ' ' . $lastname . "' ";
		$data .= ", username = '$email' ";
		if (!empty($password))
			$data .= ", password = '" . md5($password) . "' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if ($chk > 0) {
			return 2;
			exit;
		}
		$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if ($save) {
			$data = '';
			foreach ($_POST as $k => $v) {
				if ($k == 'password')
					continue;
				if (empty($data) && !is_numeric($k))
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if ($_FILES['img']['tmp_name'] != '') {
				$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
				$data .= ", avatar = '$fname' ";
			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if ($data) {
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if ($login)
					return 1;
			}
		}
	}

	function save_settings()
	{
		extract($_POST);
		$data = " name = '" . str_replace("'", "&#x2019;", $name) . "' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '" . htmlentities(str_replace("'", "&#x2019;", $about)) . "' ";
		if ($_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
			$data .= ", cover_img = '$fname' ";
		}

		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if ($chk->num_rows > 0) {
			$save = $this->db->query("UPDATE system_settings set " . $data);
		} else {
			$save = $this->db->query("INSERT INTO system_settings set " . $data);
		}
		if ($save) {
			$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
			foreach ($query as $key => $value) {
				if (!is_numeric($key))
					$_SESSION['settings'][$key] = $value;
			}

			return 1;
		}
	}

	function save_building()
	{

		extract($_POST);
		$data = "building = '$building', ";
		$data .= "department_id = '$department_id'";

		if (empty($id)) {
			$save = $this->db->query("INSERT INTO building set $data");
		} else {
			$save = $this->db->query("UPDATE building set $data where id = $id");
		}
		if ($save)
			return 1;
	}

	function delete_building()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM building where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function save_department()
	{
		extract($_POST);
		$data = " department_code = '$department_code' ";
		$data .= ", department_name = '$department_name' ";

		if (empty($id)) {
			$save = $this->db->query("INSERT INTO department set $data");
		} else {
			$save = $this->db->query("UPDATE department set $data where id = $id");
		}
		if ($save)
			return 1;
	}
	function delete_department()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM department where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function save_course()
	{
		$res = 1;
		extract($_POST);


		$stmt = $this->db->prepare("INSERT INTO courses (year, period, level, program_id, course_code, course_name, lec, lab, units, is_comlab) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

		$stmt->bind_param("ssssssssss", $year_val, $period_val, $level_val, $program_id_val, $course_code_val, $course_name_val, $lec_val, $lab_val, $units_val, $is_comlab_val);


		foreach ($course_code as $key => $value) {

			$year_val = date("Y");
			$period_val = $period[$key];
			$level_val = $level[$key];
			$program_id_val = $program_id[$key];
			$course_code_val = $course_code[$key];
			$course_name_val = $course_name[$key];
			$lec_val = $lec[$key];
			$lab_val = $lab[$key];
			$units_val = $units[$key];
			$is_comlab_val = $is_comlab[$key];


			$save = $stmt->execute();


			if (!$save) {
				$res = 0;
			}
		}


		$stmt->close();

		return $res;
	}




	function delete_course()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM courses where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function save_room()
	{
		extract($_POST);
		$data = "room = '$room', ";
		$data .= "description = '$description', ";
		$data .= "building_id = '$building_id' ";

		if (empty($id)) {
			$save = $this->db->query("INSERT INTO rooms set $data");
			return 1;
		} else {
			$save = $this->db->query("UPDATE rooms set $data where id = $id");
			return 2;
		}
	}
	function delete_room()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM rooms where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function save_section()
	{
		extract($_POST);
		$data = "program_id = '$program_id', ";
		$data .= "level = '$level', ";
		$data .= "section_name = '$section_name' ";

		// Check if section already exists
		$existing_section = $this->db->query("SELECT id FROM sections WHERE program_id = '$program_id' AND level = '$level' AND section_name = '$section_name'")->fetch_assoc();

		if (empty($id)) {
			// If section does not exist, insert it
			if (!$existing_section) {
				$save = $this->db->query("INSERT INTO sections SET $data");
				return 1;
			} else {
				// Section already exists, return error code
				return 0;
			}
		} else {
			// If updating existing section, ensure it's not a duplicate
			if (!$existing_section || $existing_section['id'] == $id) {
				$save = $this->db->query("UPDATE sections SET $data WHERE id = $id");
				return 2;
			} else {
				// Section already exists with different ID, return error code
				return 0;
			}
		}
	}

	function get_section()
	{
		extract($_GET);

		// Assuming $conn is your database connection

		// Fetch sections based on the selected level and where is_active is 1
		$stmt = $this->db->prepare("SELECT DISTINCT section_name FROM sections WHERE level = ? AND is_active = 1");
		$stmt->bind_param("s", $level);
		$stmt->execute();
		$result = $stmt->get_result();
		$sections = $result->fetch_all(MYSQLI_ASSOC);

		// Generate HTML for the dropdown options
		$html = '<label>Section</label>';
		$html .= '<select class="form-control" id="section_name">';
		$html .= '<option>Please Select</option>';

		foreach ($sections as $section) {
			$html .= '<option value="' . $section['section_name'] . '">' . $section['section_name'] . '</option>';
		}
		$html .= '</select>';

		// Return the HTML as JSON
		$data['html'] = $html;
		return json_encode($data);
	}



	function delete_section()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM sections where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function save_faculty()
	{
		extract($_POST);
		$data = '';
		foreach ($_POST as $k => $v) {
			if (!empty($v)) {
				if ($k != 'id') {
					if (empty($data))
						$data .= " $k='{$v}' ";
					else
						$data .= ", $k='{$v}' ";
				}
			}
		}
		if (empty($id_no)) {
			$i = 1;
			while ($i == 1) {
				$rand = mt_rand(1, 99999999);
				$rand = sprintf("%'08d", $rand);
				$chk = $this->db->query("SELECT * FROM faculty where id_no = '$rand' ")->num_rows;
				if ($chk <= 0) {
					$data .= ", id_no='$rand' ";
					$i = 0;
				}
			}
		}

		if (empty($id)) {
			if (!empty($id_no)) {
				$chk = $this->db->query("SELECT * FROM faculty where id_no = '$id_no' ")->num_rows;
				if ($chk > 0) {
					return 2;
				}
			}
			$save = $this->db->query("INSERT INTO faculty set $data ");
		} else {
			if (!empty($id_no)) {
				$chk = $this->db->query("SELECT * FROM faculty where id_no = '$id_no' and id != $id ")->num_rows;
				if ($chk > 0) {
					return 2;
				}
			}
			$save = $this->db->query("UPDATE faculty set $data where id=" . $id);
		}
		if ($save)
			return 1;
	}
	function delete_faculty()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM faculty where id = " . $id);
		if ($delete) {
			return 1;
		}
	}
	function save_schedule()
	{
		extract($_POST);
		$data = " faculty_id = '$faculty_id' ";
		$data .= ", subject_id = '$subject_id' ";
		$data .= ", schedule_type = '$schedule_type' ";
		$data .= ", room_id = '$room_id' ";
		if (isset($is_repeating)) {
			$data .= ", is_repeating = '$is_repeating' ";
			$rdata = array('dow' => implode(',', $dow), 'start' => $month_from . '-01', 'end' => (date('Y-m-d', strtotime($month_to . '-01 +1 month - 1 day '))));
			$data .= ", repeating_data = '" . json_encode($rdata) . "' ";
		} else {
			$data .= ", is_repeating = 0 ";
			$data .= ", schedule_date = '$schedule_date' ";
		}
		$data .= ", time_from = '$time_from' ";
		$data .= ", time_to = '$time_to' ";

		if (empty($id)) {
			$save = $this->db->query("INSERT INTO schedules set " . $data);
		} else {
			$save = $this->db->query("UPDATE schedules set " . $data . " where id=" . $id);
		}
		if ($save)
			return 1;
	}
	function delete_schedule()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM schedules where id = " . $id);
		if ($delete) {
			return 1;
		}
	}
	function get_schedule()
	{
		extract($_POST);
		$data = array();
		$qry = $this->db->query("SELECT schedules.*, rooms.id as room_id, rooms.room, subjects.id as subject_id, subjects.subject FROM schedules 
								 LEFT JOIN rooms ON schedules.room_id = rooms.id 
								 LEFT JOIN subjects ON schedules.subject_id = subjects.id 
								 WHERE faculty_id = 0 OR faculty_id = $faculty_id");
		while ($row = $qry->fetch_assoc()) {
			if ($row['is_repeating'] == 1) {
				$rdata = json_decode($row['repeating_data']);
				foreach ($rdata as $k => $v) {
					$row[$k] = $v;
				}
			}
			$data[] = $row;
		}
		return json_encode($data);
	}

	function add_course_offer()
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
			// Get section_name and curriculum_id from input
			$section_name = $_GET['section_name'] ?? null;
			$curriculum_id = $_GET['course_id'] ?? null;

			// Check if both section_name and curriculum_id are set
			if ($section_name !== null && $curriculum_id !== null) {
				// Check if the offering already exists
				$sql_check = "SELECT * FROM course_offering WHERE curriculum_id = ? AND section_name = ?";
				$stmt_check = $this->db->prepare($sql_check);
				$stmt_check->bind_param("ss", $curriculum_id, $section_name);
				$stmt_check->execute();
				$result_check = $stmt_check->get_result();

				// If the offering doesn't exist, create a new one
				if ($result_check->num_rows === 0) {
					// Find the curriculum level by ID
					$sql_curriculum = "SELECT level FROM curriculum WHERE id = ?";
					$stmt_curriculum = $this->db->prepare($sql_curriculum);
					$stmt_curriculum->bind_param("s", $curriculum_id);
					$stmt_curriculum->execute();
					$result_curriculum = $stmt_curriculum->get_result();
					$row_curriculum = $result_curriculum->fetch_assoc();
					$level = $row_curriculum['level'];

					// Insert new record
					$sql_insert = "INSERT INTO course_offering (curriculum_id, section_name, level) VALUES (?, ?, ?)";
					$stmt_insert = $this->db->prepare($sql_insert);
					$stmt_insert->bind_param("sss", $curriculum_id, $section_name, $level);
					$stmt_insert->execute();
					echo 'Offered Subject!';
				} else {
					echo 'Offered Subject Already Exists!';
				}
			} else {
				echo 'Missing required parameters!';
			}
		} else {
			echo 'Invalid request!';
		}
	}
}
