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

		$username = $_POST['username'];
		$password = $_POST['password'];

		// Prepare the SQL query
		$stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();

		// Check if user exists
		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();

			if (password_verify($password, $user['password'])) {
				// Store user data in session, excluding password
				foreach ($user as $key => $value) {
					if ($key !== 'password') {
						$_SESSION['login_' . $key] = $value;
					}
				}

				// Check user type
				if ($_SESSION['login_type'] != 0) {
					// Unset session variables and return status 2 for non-privileged users
					session_unset();
					return 2;
				}

				// Return status 1 for successful login
				return 1;
			}
		}

		// Return status 3 for invalid credentials
		return 3;
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
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// Retrieve form data
			$username = $_POST['username'];
			$firstname = $_POST['firstname'];
			$middlename = $_POST['middlename'];
			$lastname = $_POST['lastname'];
			$extensionname = $_POST['extensionname'];
			$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
			$program_id = $_POST['program_id'];
			$type = 1;

			// Insert data into the database using prepared statements
			$query = "INSERT INTO users (username, fname, mname, lname, extname, password, type, program_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

			$stmt = mysqli_prepare($this->db, $query);

			// Bind parameters
			mysqli_stmt_bind_param($stmt, "ssssssii", $username, $firstname, $middlename, $lastname, $extensionname, $password, $type, $program_id);

			if (mysqli_stmt_execute($stmt)) {
				echo '1';
			} else {
				echo 'Error: ' . mysqli_stmt_error($stmt);
			}

			mysqli_stmt_close($stmt);
		}
	}

	function edit_user()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// Get data from the POST request
			$id = $_POST['id'];
			$username = $_POST['username'];
			$firstname = $_POST['firstname'];
			$middlename = $_POST['middlename'];
			$lastname = $_POST['lastname'];
			$extensionname = $_POST['extensionname'];
			$program_id = $_POST['program_id'];

			// Prepare and execute the query
			$stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
			if (!$stmt) {
				echo "Error preparing query: " . $this->db->error;
				return;
			}
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();

			// Check if user exists
			if ($result->num_rows > 0) {
				// Prepare update query
				$update_query = "UPDATE users SET username = ?, fname = ?, mname = ?, lname = ?, extname = ?, program_id = ? WHERE id = ?";
				$stmt = $this->db->prepare($update_query);
				if (!$stmt) {
					echo "Error preparing update query: " . $this->db->error;
					return;
				}
				$stmt->bind_param("ssssssi", $username, $firstname, $middlename, $lastname, $extensionname, $program_id, $id);

				// Execute the update query
				if ($stmt->execute()) {
					echo '1';
				} else {
					echo "Error updating user: " . $stmt->error;
				}
			} else {
				echo "User not found.";
			}
			$stmt->close();
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

		$stmt = $this->db->prepare("INSERT INTO courses (year, period, level, program_id, course_code, course_name, lec, lab, units, is_comlab, hours) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssssddddd", $year_val, $period_val, $level_val, $program_id_val, $course_code_val, $course_name_val, $lec_val, $lab_val, $units_val, $is_comlab_val, $hours_val);

		foreach ($year as $key => $value) {
			if (array_key_exists($key, $year)) {
				// Set values for each iteration
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

				if ($is_comlab_val && $lec_val) {
					// Lab and lecture course: 1 unit = 4 hours (3 for lab, 1 for lecture)
					$hours_val = $units_val * 4;
				} elseif ($is_comlab_val || $lec_val) {
					// Lab or lecture course: 1 unit = 3 hours (lab only) or 2 hours (lecture only)
					$hours_val = $units_val * 3;
				} else {
					// Non-lab and non-lecture course: 1 unit = 1 hour
					$hours_val = $units_val;
				}

				$check_stmt = $this->db->prepare("SELECT COUNT(*) FROM courses WHERE course_code = ? AND course_name = ?");
				$check_stmt->bind_param("ss", $course_code_val, $course_name_val);
				$check_stmt->execute();
				$check_stmt->bind_result($count);
				$check_stmt->fetch();
				$check_stmt->close();

				if ($count > 0) {
					$res = 0;
					continue;
				}

				$save = $stmt->execute();

				if (!$save) {
					$res = 0;
				}
			}
		}

		return $res;
	}



	function edit_course()
	{
		// Assuming this function is within a class context and $this->db represents your database connection

		if (isset($_POST['course_id'], $_POST['course_code'], $_POST['course_name'], $_POST['lec'], $_POST['lab'], $_POST['units'], $_POST['comlab'], $_POST['program_code'], $_POST['year'])) {
			$course_id = $_POST['course_id'];

			// Retrieve form data from POST    
			$course_code = $_POST['course_code'];
			$course_name = $_POST['course_name'];
			$lec = $_POST['lec'];
			$lab = $_POST['lab'];
			$units = $_POST['units'];
			$is_comlab = $_POST['comlab'];
			$program_code = $_POST['program_code'];
			$year = $_POST['year'];

			// Prepare and execute the update query
			$update_query = "UPDATE courses SET course_code = ?, course_name = ?, lec = ?, lab = ?, units = ?, is_comlab = ? WHERE id = ?";
			$update_stmt = $this->db->prepare($update_query);
			$update_stmt->bind_param("ssdddsi", $course_code, $course_name, $lec, $lab, $units, $is_comlab, $course_id); // Use 'i' for integer, 'd' for double/float

			// Execute the statement
			$update_result = $update_stmt->execute();

			if ($update_result) {
				// Send response of "1" for success
				return 1;
			} else {
				// Send response of "0" for failure
				return 0;
			}
		} else {
			// Send response of 0 for missing parameters
			return 0;
		}
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

		// Check if room already exists
		$existing_room = $this->db->query("SELECT id FROM rooms WHERE room = '$room' AND building_id = '$building_id'")->fetch_assoc();

		// If room does not exist, insert it
		if (empty($id)) {
			// If room does not exist, insert it
			if (!$existing_room) {
				$data = "room = '$room', ";
				$data .= "description = '$description', ";
				$data .= "building_id = '$building_id' ";
				$save = $this->db->query("INSERT INTO rooms SET $data");
				return 1;
			} else {
				// Room already exists, return error code
				return 0;
			}
		} else {
			// If updating existing room, ensure it's not a duplicate
			if (empty($existing_room) || $existing_room['id'] == $id) {
				$data = "room = '$room', ";
				$data .= "description = '$description', ";
				$data .= "building_id = '$building_id' ";
				$save = $this->db->query("UPDATE rooms SET $data WHERE id = $id");
				return 2;
			} else {
				// Room already exists with different ID, return error code
				return 0;
			}
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

		// var_dump($existing_section);

		if (empty($id)) {
			if (!$existing_section) {
				$save = $this->db->query("INSERT INTO sections SET $data");
				return 1;
			} else {
				return 0;
			}
		} else {
			if (empty($existing_section) || $existing_section['id'] == $id) {
				$save = $this->db->query("UPDATE sections SET $data WHERE id = $id");
				return 2;
			} else {

				return 0;
			}
		}
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
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// Retrieve form data
			$program_id = $_POST['program_id'];
			$gender = $_POST['gender'];
			$designation = $_POST['designation'];
			$street = $_POST['street'];
			$barangay = $_POST['barangay'];
			$municipality = $_POST['municipality'];
			$province = $_POST['province'];
			$contact = $_POST['contact'];
			$email = $_POST['email'];

			// Query to get the latest ID from the users table
			$latestUserIdQuery = "SELECT id FROM users ORDER BY id DESC LIMIT 1";
			// Execute the query to get the latest user ID
			$latestUserIdResult = mysqli_query($this->db, $latestUserIdQuery);

			if ($latestUserIdResult && mysqli_num_rows($latestUserIdResult) > 0) {
				$row = mysqli_fetch_assoc($latestUserIdResult);
				$latestUserId = $row['id'];

				// var_dump($latestUserId);
			} else {
				echo "No users found";
				return; // Exit the function if no user found
			}

			// Insert data into the database using prepared statements
			$insertQuery = "INSERT INTO faculty (program_id, gender, designation, street, barangay, municipality, province, contact, email, user_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$stmt = mysqli_prepare($this->db, $insertQuery);

			// Bind parameters
			mysqli_stmt_bind_param($stmt, "issssssssi", $program_id, $gender, $designation, $street, $barangay, $municipality, $province, $contact, $email, $latestUserId);

			// Execute statement
			if (mysqli_stmt_execute($stmt)) {
				// Insertion successful
				echo '1'; // Return '1' to indicate success to the client-side JavaScript
			} else {
				// Insertion failed
				echo 'Error: ' . mysqli_stmt_error($stmt); // Return error message to the client-side JavaScript
			}

			// Close statement
			mysqli_stmt_close($stmt);
		}
	}

	function add_load()
	{
		$latest_user_query = "SELECT id FROM users ORDER BY id DESC LIMIT 1";
		$latest_user_result = mysqli_query($this->db, $latest_user_query);
		$latest_user_row = mysqli_fetch_assoc($latest_user_result);
		$user_id = $latest_user_row['id'];

		$faculty_info_query = "SELECT designation FROM faculty WHERE user_id = ?";
		$faculty_info_stmt = mysqli_prepare($this->db, $faculty_info_query);
		mysqli_stmt_bind_param($faculty_info_stmt, "i", $user_id);
		mysqli_stmt_execute($faculty_info_stmt);
		$faculty_info_result = mysqli_stmt_get_result($faculty_info_stmt);
		$faculty_info_row = mysqli_fetch_assoc($faculty_info_result);
		$designation = $faculty_info_row['designation'];

		if ($designation == 'Full Time') {
			$units = 36;
		} else {
			$units = 15;
		}

		$insert_load_query = "INSERT INTO unit_loads (users_id, units, designation) VALUES (?, ?, ?)";
		$insert_load_stmt = mysqli_prepare($this->db, $insert_load_query);
		mysqli_stmt_bind_param($insert_load_stmt, "iis", $user_id, $units, $designation);

		if (mysqli_stmt_execute($insert_load_stmt)) {
			echo 'Load added successfully!';
		} else {
			echo 'Failed to add load!';
		}
	}



	function edit_faculty()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$idno = $_POST['id'];
			$program_id = $_POST['program_id'];
			$gender = $_POST['gender'];
			$designation = $_POST['designation'];
			$street = $_POST['street'];
			$barangay = $_POST['barangay'];
			$municipality = $_POST['municipality'];
			$province = $_POST['province'];
			$contact = $_POST['contact'];
			$email = $_POST['email'];

			$query = "SELECT * FROM faculty WHERE user_id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $idno);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows > 0) {
				$update_query = "
            UPDATE faculty 
            SET 
                program_id = ?, 
                gender = ?, 
                designation = ?, 
                street = ?, 
                barangay = ?, 
                municipality = ?, 
                province = ?, 
                contact = ?, 
                email = ?
            WHERE user_id = ?";

				$stmt = $this->db->prepare($update_query);
				$stmt->bind_param(
					"issssssssi",
					$program_id,
					$gender,
					$designation,
					$street,
					$barangay,
					$municipality,
					$province,
					$contact,
					$email,
					$idno
				);

				// Execute the update query
				if ($stmt->execute()) {
					echo '1';
				} else {
					echo "Error updating instructor info: " . $stmt->error;
				}
				$stmt->close();
			} else {
				echo "Instructor with ID number $idno not found.";
			}
		}
	}


	function delete_faculty()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM faculty where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function get_schedule()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['course_offering_info_id'])) {
			$course_offering_info_id = $_GET['course_offering_info_id'];
	
			$event_array = [];
	
			$stmt = $this->db->prepare("SELECT * FROM schedules WHERE course_offering_info_id = ?");
			if (!$stmt) {
				throw new Exception("Error preparing statement: " . $this->db->error);
			}
			$stmt->bind_param("i", $course_offering_info_id);
			$stmt->execute();
			$schedules_result = $stmt->get_result();
	
			// Fetch each schedule and its related course details
			while ($sched = $schedules_result->fetch_assoc()) {
				$course_detail_stmt = $this->db->prepare("
					SELECT 
						courses.course_code, 
						rooms.room, 
						program.program_code, 
						sections.level,
						sections.section_name
					FROM courses 
					JOIN course_offering_info ON course_offering_info.courses_id = courses.id
					JOIN rooms ON rooms.id = ?
					JOIN sections ON sections.id = course_offering_info.section_id
					JOIN program ON program.id = sections.program_id
					WHERE course_offering_info.id = ?
				");
				if (!$course_detail_stmt) {
					throw new Exception("Error preparing course detail statement: " . $this->db->error);
				}
				$course_detail_stmt->bind_param("ii", $sched['room_id'], $course_offering_info_id);
				$course_detail_stmt->execute();
				$course_detail_result = $course_detail_stmt->get_result();
	
				if (!$course_detail_result) {
					throw new Exception("Error fetching course detail information: " . $this->db->error);
				}
				$course_detail = $course_detail_result->fetch_assoc();
	
				$day_map = [
					'M' => 'Monday',
					'T' => 'Tuesday',
					'W' => 'Wednesday',
					'Th' => 'Thursday',
					'F' => 'Friday',
					'Sa' => 'Saturday',
					'Su' => 'Sunday',
				];
				$color_map = [
					'M' => 'LightSalmon',
					'T' => 'lightblue',
					'W' => 'LightSalmon',
					'Th' => 'lightblue',
					'F' => 'LightSalmon',
					'Sa' => 'lightblue',
					'Su' => 'LightSalmon',
				];
				$day = $day_map[$sched['day']] ?? '';
				$color = $color_map[$sched['day']] ?? '';
	
				$section_name_concatenated = $course_detail['program_code'] . '-' . substr($course_detail['level'], 0, 1) . $course_detail['section_name'];
	
				$event_array[] = [
					'id' => $sched['id'],
					'title' => $course_detail['course_code'] . '<br>' . $course_detail['room'] . '<br>' . $section_name_concatenated,
					'start' => date('Y-m-d', strtotime('next ' . $day)) . 'T' . $sched['time_start'],
					'end' => date('Y-m-d', strtotime('next ' . $day)) . 'T' . $sched['time_end'],
					'color' => $color,
					'textColor' => 'black',
					'extendedProps' => [
						'course_offering_info_id' => $course_offering_info_id
					]
				];
			}
			
			return json_encode($event_array);
		}
	}
	


	// function getFullCalendar()
	// {
	// 	if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['section_id']) && isset($_GET['course_id'])) {
	// 		$section_id = $_GET['section_id'];
	// 		$course_id = $_GET['course_id'];

	// 		// Query to fetch schedules for the given course offering
	// 		$query = "SELECT * FROM schedules WHERE section_id = ? AND course_id = ?";
	// 		$stmt = $this->db->prepare($query);
	// 		$stmt->bind_param("ii", $section_id, $course_id);
	// 		$stmt->execute();
	// 		$result = $stmt->get_result();

	// 		// Array to store the events
	// 		$events = [];

	// 		// Loop through the result set and create events
	// 		while ($row = $result->fetch_assoc()) {
	// 			// Construct the event data
	// 			$event = [
	// 				'id' => $row['id'],
	// 				'title' => $row['title'],
	// 				'start' => $row['start_date'] . 'T' . $row['start_time'],
	// 				'end' => $row['end_date'] . 'T' . $row['end_time'],
	// 			];

	// 			// Push the event to the events array
	// 			$events[] = $event;
	// 		}

	// 		// Return the events as JSON
	// 		echo json_encode($events);
	// 	}
	// }

	function add_course_offer()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['section_id']) && isset($_GET['course_id'])) {
			$section_id = $_GET['section_id'];
			$course_id = $_GET['course_id'];

			// Query to check if the offering already exists
			$check_query = $this->db->prepare("SELECT * FROM course_offering_info WHERE courses_id = ? AND section_id = ?");
			$check_query->bind_param("ss", $course_id, $section_id);
			$check_query->execute();
			$check_result = $check_query->get_result();

			// Fetch the courses details
			$course_query = $this->db->prepare("SELECT level FROM courses WHERE id = ?");
			$course_query->bind_param("s", $course_id);
			$course_query->execute();
			$course_result = $course_query->get_result();
			$course_row = $course_result->fetch_assoc();
			$level = $course_row['level'];

			// If the offering doesn't exist, add it to the database
			if ($check_result->num_rows === 0) {
				$offering_query = $this->db->prepare("INSERT INTO course_offering_info (courses_id, section_id) VALUES (?, ?)");
				$offering_query->bind_param("ss", $course_id, $section_id);
				$offering_query->execute();
				echo 'Offered Subject!';
			} else {
				echo 'Offered Subject Already Exists!';
			}
		}
	}

	function remove_course_offering()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['section_id']) && isset($_GET['courses_id'])) {
			$section_id = $_GET['section_id'];
			$courses_id = $_GET['courses_id'];

			// Prepare and execute SELECT query
			$check_if_exists_query = "SELECT * FROM course_offering_info WHERE courses_id = ? AND section_id = ? LIMIT 1";
			$stmt = $this->db->prepare($check_if_exists_query);
			if (!$stmt) {
				echo 'Error preparing query: ' . $this->db->error;
				return;
			}
			$stmt->bind_param("ii", $courses_id, $section_id);
			$stmt->execute();
			$result = $stmt->get_result();

			$check_if_exists = $result->fetch_assoc();


			if ($check_if_exists) {
				$delete_query = "DELETE FROM course_offering_info WHERE courses_id = ? AND section_id = ?";
				$stmt = $this->db->prepare($delete_query);
				if (!$stmt) {
					echo 'Error preparing delete query: ' . $this->db->error;
					return;
				}
				$stmt->bind_param("ii", $courses_id, $section_id);
				$stmt->execute();


				if ($stmt->affected_rows > 0) {

					echo 'Removed Course Offered!';
				} else {
					echo 'Failed to remove Course Offered!';
				}
			} else {
				echo 'Course Offered not found!';
			}
		} else {
			echo 'Invalid Request!';
		}
	}

	function add_schedule()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// Retrieving data from the request
			$course_offering_info_id = $_POST['course_offering_info_id'];
			$day = $_POST['day'];
			$time_start = $_POST['time_start'];
			$time_end = $_POST['time_end'];
			$section_id = $_POST['section_id'];
			$room_id = $_POST['room_id'];

			$query = "SELECT * FROM course_offering_info
                  JOIN schedules ON course_offering_info.id = schedules.course_offering_info_id
                  WHERE course_offering_info.section_id = '$section_id'
                  AND schedules.day = '$day'
                  AND schedules.time_start = '" . date('H:i:s', strtotime($time_start)) . "'
                  AND schedules.time_end = '" . date('H:i:s', strtotime($time_end)) . "'";

			// Execute the query
			$result = mysqli_query($this->db, $query);

			// Check if the same schedule exists
			if (mysqli_num_rows($result) == 0) {
				// Insert new schedule
				$new_schedule_query = "INSERT INTO schedules (day, time_start, time_end, room_id, course_offering_info_id)
                                   VALUES ('$day', '" . date('H:i:s', strtotime($time_start)) . "', '" . date('H:i:s', strtotime($time_end)) . "', '$room_id', '$course_offering_info_id')";

				// Execute the insert query
				if (mysqli_query($this->db, $new_schedule_query)) {
					// Respond with success
					header('Content-Type: application/json');
					echo json_encode(array('status' => 'success'));
				} else {
					// Respond with error
					header('Content-Type: application/json');
					echo json_encode(array('status' => 'error', 'message' => mysqli_error($this->db)));
				}
			} else {
				// Respond with error (same schedule found)
				header('Content-Type: application/json');
				echo json_encode(array('status' => 'error', 'message' => 'Same schedule already exists.'));
			}
		}
	}

	function remove_schedule()
	{
	}

	function add_faculty_load()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['course_offering_info_id'])) {

			$instructor = $_GET['instructor'];
			$course_offering_info_id = $_GET['course_offering_info_id'];

			// Get instructor info
			$info_query = "SELECT * FROM faculty WHERE user_id = ?";
			$info_stmt = $this->db->prepare($info_query);
			$info_stmt->bind_param('i', $instructor);
			$info_stmt->execute();
			$info_result = $info_stmt->get_result();
			$info = $info_result->fetch_assoc();

			$loads_query = "SELECT SUM(units) AS total_units FROM courses
                        INNER JOIN course_offering_info ON courses.id = course_offering_info.courses_id
                        INNER JOIN schedules ON schedules.course_offering_info_id = course_offering_info.id
                        WHERE schedules.users_id = ?";

			$loads_stmt = $this->db->prepare($loads_query);
			$loads_stmt->bind_param('i', $instructor);
			$loads_stmt->execute();
			$loads_result = $loads_stmt->get_result();

			// var_dump($loads_query);
			// var_dump($instructor);
			// var_dump($loads_stmt->error);

			$loads_row = $loads_result->fetch_assoc();
			$total_units = $loads_row['total_units'];

			// Get load units
			$load_units_query = "SELECT SUM(units) AS total_units FROM unit_loads WHERE users_id = ?";
			$load_units_stmt = $this->db->prepare($load_units_query);
			$load_units_stmt->bind_param('i', $instructor);
			$load_units_stmt->execute();
			$load_units_result = $load_units_stmt->get_result();
			$load_units_row = $load_units_result->fetch_assoc();
			$total_load_units = $load_units_row['total_units'];

			if ($total_units >= $total_load_units) {
				http_response_code(404);
				return;
			}

			// Get schedules
			$schedules_query = "SELECT * FROM schedules WHERE course_offering_info_id = ?";
			$schedules_stmt = $this->db->prepare($schedules_query);
			$schedules_stmt->bind_param('i', $course_offering_info_id);
			$schedules_stmt->execute();
			$schedules_result = $schedules_stmt->get_result();

			if ($schedules_result->num_rows > 0) {
				while ($schedule = $schedules_result->fetch_assoc()) {
					$conflict_query = "SELECT DISTINCT course_offering_info_id, day, time_start, time_end 
									   FROM schedules
									   WHERE users_id = ? AND day = ? AND
									   ((time_start < ? AND time_end > ?) OR
										(time_start < ? AND time_end > ?) OR
										(time_start >= ? AND time_end <= ?))";
					$conflict_stmt = $this->db->prepare($conflict_query);
					$conflict_stmt->bind_param(
						'isssssss',
						$instructor,
						$schedule['day'],
						$schedule['time_end'],
						$schedule['time_start'],
						$schedule['time_start'],
						$schedule['time_end'],
						$schedule['time_start'],
						$schedule['time_end']
					);
					$conflict_stmt->execute();
					$conflict_result = $conflict_stmt->get_result();

					if ($conflict_result->num_rows == 0) {
						// No conflict, update schedule
						$update_query = "UPDATE schedules SET users_id = ? WHERE id = ?";
						$update_stmt = $this->db->prepare($update_query);
						$update_stmt->bind_param('ii', $instructor, $schedule['id']);
						$update_stmt->execute();
						echo 'Successfully Added Faculty Load!';
					} else {
						// Schedule conflict occurred
						http_response_code(409);
						echo 'Schedule conflict occurred! Conflicting schedules: ';

						// Fetch and display conflicting schedules
						while ($conflicting_schedule = $conflict_result->fetch_assoc()) {
							var_dump($conflicting_schedule);
						}

						return;
					}
				}
			}
		}
	}


	function remove_faculty_load()
	{
		$response = array();

		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['offering_id'])) {
			$instructor = $_GET['instructor'];
			$offering_id = $_GET['offering_id'];


			$stmt = $this->db->prepare("SELECT id FROM schedules WHERE users_id = ? AND course_offering_info_id = ?");
			$stmt->bind_param("ii", $instructor, $offering_id);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows > 0) {
				$response['success'] = true;
				$response['message'] = "Schedules updated successfully.";

				while ($row = $result->fetch_assoc()) {
					$schedule_id = $row['id'];

					$update_stmt = $this->db->prepare("UPDATE schedules SET users_id = NULL, is_loaded = 0 WHERE id = ?");
					$update_stmt->bind_param("i", $schedule_id);
					$update_stmt->execute();
				}
			} else {
				$response['success'] = false;
				$response['message'] = "No schedules found for instructor $instructor and offering ID $offering_id.";
			}
		} else {
			$response['success'] = false;
			$response['message'] = "Invalid request parameters.";
		}

		// Return the response as JSON
		echo json_encode($response);
	}
}
