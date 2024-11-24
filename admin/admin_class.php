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

		$stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();

			if (password_verify($password, $user['password'])) {
				foreach ($user as $key => $value) {
					if ($key !== 'password') {
						$_SESSION['login_' . $key] = $value;
					}
				}
				// Check user type
				if ($_SESSION['login_type'] == 0) {
					return 1;
				} elseif ($_SESSION['login_type'] == 2) {
					return 2;
				}
				session_unset();
			}
		}
		return 3;
	}

	function logout()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_user()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$id = isset($_POST['id']) ? $_POST['id'] : null;
			$username = $_POST['username'];
			$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
			$program_id = $_POST['program_id'];
			$type = 0;

			if (!$id) {
				$check_query = "SELECT * FROM users WHERE program_id = ?";
				$stmt = mysqli_prepare($this->db, $check_query);
				mysqli_stmt_bind_param($stmt, "i", $program_id);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_store_result($stmt);

				if (mysqli_stmt_num_rows($stmt) > 0) {
					echo '3';
					mysqli_stmt_close($stmt);
					return;
				}
				mysqli_stmt_close($stmt);
			}

			if ($id) {
				if ($password) {
					$query = "UPDATE users SET username = ?, password = ?, program_id = ?, type = ? WHERE id = ?";
					$stmt = mysqli_prepare($this->db, $query);
					mysqli_stmt_bind_param($stmt, "sssii", $username, $password, $program_id, $type, $id);
				} else {
					$query = "UPDATE users SET username = ?, program_id = ?, type = ? WHERE id = ?";
					$stmt = mysqli_prepare($this->db, $query);
					mysqli_stmt_bind_param($stmt, "siii", $username, $program_id, $type, $id);
				}

				if (mysqli_stmt_execute($stmt)) {
					echo '2';
				} else {
					echo 'Error: ' . mysqli_stmt_error($stmt);
				}
			} else {
				$query = "INSERT INTO users (username, password, program_id, type) VALUES (?, ?, ?, ?)";
				$stmt = mysqli_prepare($this->db, $query);
				mysqli_stmt_bind_param($stmt, "ssii", $username, $password, $program_id, $type);

				if (mysqli_stmt_execute($stmt)) {
					echo '1';
				} else {
					echo 'Error: ' . mysqli_stmt_error($stmt);
				}
			}

			mysqli_stmt_close($stmt);
		}
	}

	function delete_user()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = " . $id);
		if ($delete)
			return 1;
	}


	function change_password()
	{
		if (isset($_POST['idno']) && (isset($_POST['username']) || (isset($_POST['password']) && isset($_POST['password_confirmation'])))) {
			$user_id = $_POST['idno'];

			$query = "SELECT * FROM users WHERE id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $user_id);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows == 1) {
				$row = $result->fetch_assoc();
				$updates = [];
				$params = [];
				$types = '';

				if (!empty($_POST['username'])) {
					$username = $_POST['username'];
					if (strlen($username) < 3) {
						echo "Username must be at least 3 characters long.";
						return;
					}
					$updates[] = "username = ?";
					$params[] = $username;
					$types .= 's';
				}

				if (!empty($_POST['password']) && !empty($_POST['password_confirmation'])) {
					$password = $_POST['password'];
					$password_confirmation = $_POST['password_confirmation'];

					if (strlen($password) < 6 || $password !== $password_confirmation) {
						echo "Password must be at least 6 characters long and match the confirmation.";
						return;
					}

					$hashed_password = password_hash($password, PASSWORD_DEFAULT);
					$updates[] = "password = ?";
					$params[] = $hashed_password;
					$types .= 's';
				}

				if (!empty($updates)) {
					$update_query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
					$params[] = $user_id;
					$types .= 'i';

					$update_stmt = $this->db->prepare($update_query);
					$update_stmt->bind_param($types, ...$params);
					$update_stmt->execute();

					if ($update_stmt->affected_rows > 0) {
						echo "Account updated successfully.";
					} else {
						echo "No changes made.";
					}
				} else {
					echo "No valid data to update.";
				}
			} else {
				echo "User not found.";
			}
		} else {
			echo "Please provide a valid user ID, and at least a username or password.";
		}
	}

	function account_setting()
	{
		if (isset($_POST['idno']) && (isset($_POST['username']) || (isset($_POST['password']) && isset($_POST['password_confirmation'])))) {
			$user_id = $_POST['idno'];

			$query = "SELECT * FROM users WHERE id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->bind_param("i", $user_id);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows == 1) {
				$row = $result->fetch_assoc();
				$updates = [];
				$params = [];
				$types = '';

				if (!empty($_POST['username'])) {
					$username = $_POST['username'];
					if (strlen($username) < 3) {
						echo "Username must be at least 3 characters long.";
						return;
					}
					$updates[] = "username = ?";
					$params[] = $username;
					$types .= 's';
				}

				if (!empty($_POST['password']) && !empty($_POST['password_confirmation'])) {
					$password = $_POST['password'];
					$password_confirmation = $_POST['password_confirmation'];

					if (strlen($password) < 6 || $password !== $password_confirmation) {
						echo "Password must be at least 6 characters long and match the confirmation.";
						return;
					}

					$hashed_password = password_hash($password, PASSWORD_DEFAULT);
					$updates[] = "password = ?";
					$params[] = $hashed_password;
					$types .= 's';
				}

				if (!empty($updates)) {
					$update_query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
					$params[] = $user_id;
					$types .= 'i';

					$update_stmt = $this->db->prepare($update_query);
					$update_stmt->bind_param($types, ...$params);
					$update_stmt->execute();

					if ($update_stmt->affected_rows > 0) {
						echo "Account updated successfully.";
					} else {
						echo "No changes made.";
					}
				} else {
					echo "No valid data to update.";
				}
			} else {
				echo "User not found.";
			}
		} else {
			echo "Please provide a valid user ID, and at least a username or password.";
		}
	}

	function save_building()
	{

		extract($_POST);
		$data = "building = '$building', ";
		$data .= "program_id = '$program_id'";

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

	function save_designation()
	{
		extract($_POST);
		$data = "designation = '$designation', ";
		$data .= "hours = '$hours', ";
		$data .= "administrative = '$administrative', ";
		$data .= "research = '$research', ";
		$data .= "ext_service = '$ext_service', ";
		$data .= "consultation = '$consultation', ";
		$data .= "instructional = '$instructional', ";
		$data .= "others = '$others' ";

		if (empty($id)) {
			$save = $this->db->query("INSERT INTO designation set $data");
		} else {
			$save = $this->db->query("UPDATE designation set $data where id = $id");
		}
		if ($save)
			return 1;
	}

	function delete_designation()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM designation where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function save_academic_rank()
	{
		extract($_POST);
		$data = "academic_rank = '$academic_rank', ";
		$data .= "hours = '$hours', ";
		$data .= "administrative = '$administrative', ";
		$data .= "research = '$research', ";
		$data .= "ext_service = '$ext_service', ";
		$data .= "consultation = '$consultation', ";
		$data .= "instructional = '$instructional', ";
		$data .= "others = '$others' ";

		if (empty($id)) {
			$save = $this->db->query("INSERT INTO unit_loads set $data");
		} else {
			$save = $this->db->query("UPDATE unit_loads set $data where id = $id");
		}
		if ($save) {
			return 1; // Success
		} else {
			// Log the error and return a failure response
			error_log("Database Error: " . $this->db->error);
			return 0; // Failure
		}
	}

	function delete_academic_rank()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM unit_loads where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function save_semester()
	{
		extract($_POST);
		$data = "sem_name = '$sem_name', ";
		$data .= "start_date = '$start_date', ";
		$data .= "end_date = '$end_date' ";

		if (empty($id)) {
			$save = $this->db->query("INSERT INTO semester set $data");
		} else {
			$save = $this->db->query("UPDATE semester set $data where id = $id");
		}
		if ($save)
			return 1;
	}

	function delete_semester()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM semester where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function save_program()
	{
		extract($_POST);
		$data = " program_code = '$program_code' ";
		$data .= ", program_name = '$program_name' ";
		$data .= ", department = '$department' ";

		if (empty($id)) {
			$save = $this->db->query("INSERT INTO program set $data");
		} else {
			$save = $this->db->query("UPDATE program set $data where id = $id");
		}
		if ($save)
			return 1;
	}
	function delete_program()
	{
		extract($_POST);
		$delete = $this->db->query("DELETE FROM program where id = " . $id);
		if ($delete) {
			return 1;
		}
	}

	function save_course()
	{
		$res = 1;
		extract($_POST);

		$stmt = $this->db->prepare("INSERT INTO courses (year, period, level, program_id, course_code, course_name, lec, lab, units, is_comlab, hours, cmo_no, series) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

		$stmt->bind_param("sssissddddsss", $year_val, $period_val, $level_val, $program_id_val, $course_code_val, $course_name_val, $lec_val, $lab_val, $units_val, $is_comlab_val, $hours_val, $cmo_no_val, $series_val);

		foreach ($period as $key => $value) {
			$year_val = $year;
			$period_val = $period[$key];
			$level_val = $level[$key];
			$program_id_val = $program_id[$key];
			$course_code_val = $course_code[$key];
			$course_name_val = $course_name[$key];
			$lec_val = $lec[$key];
			$lab_val = $lab[$key];
			$units_val = $units[$key];
			$is_comlab_val = $is_comlab[$key];
			$hours_val = $hours[$key];
			$cmo_no_val = $cmo_no;
			$series_val = $series;

			$check_stmt = $this->db->prepare("SELECT COUNT(*) FROM courses WHERE course_code = ? AND course_name = ? AND program_id = ? AND year = ?");
			$check_stmt->bind_param("ssis", $course_code_val, $course_name_val, $program_id_val, $year_val);
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

		return $res;
	}

	function edit_course()
	{

		if (isset($_POST['course_id'], $_POST['course_code'], $_POST['course_name'], $_POST['lec'], $_POST['lab'], $_POST['units'], $_POST['comlab'], $_POST['program_code'], $_POST['year'])) {
			$course_id = $_POST['course_id'];
			$course_code = $_POST['course_code'];
			$course_name = $_POST['course_name'];
			$lec = $_POST['lec'];
			$lab = $_POST['lab'];
			$units = $_POST['units'];
			$is_comlab = $_POST['comlab'];

			$update_query = "UPDATE courses SET course_code = ?, course_name = ?, lec = ?, lab = ?, units = ?, is_comlab = ? WHERE id = ?";
			$update_stmt = $this->db->prepare($update_query);
			$update_stmt->bind_param("ssdddsi", $course_code, $course_name, $lec, $lab, $units, $is_comlab, $course_id);

			$update_result = $update_stmt->execute();

			if ($update_result) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	function edit_year()
	{
		if (isset($_POST['program_id'], $_POST['year'], $_POST['updated_year'], $_POST['cmo_no'], $_POST['series'])) {
			$program_id = $_POST['program_id'];
			$year = $_POST['year'];
			$updated_year = $_POST['updated_year'];
			$cmo_no = $_POST['cmo_no'];
			$series = $_POST['series'];

			$update_query = "UPDATE courses 
                         SET year = ?, cmo_no = ?, series = ? 
                         WHERE program_id = ? AND year = ?";
			$update_stmt = $this->db->prepare($update_query);

			$update_stmt->bind_param("sssis", $updated_year, $cmo_no, $series, $program_id, $year);

			$update_result = $update_stmt->execute();

			if ($update_result) {
				return 1;
			} else {
				return 0;
			}
		} else {
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

		$existing_room = $this->db->query("SELECT id FROM rooms WHERE room = '$room' AND building_id = '$building_id'")->fetch_assoc();

		if (empty($id)) {
			if (!$existing_room) {
				$data = "room = '$room', ";
				$data .= "description = '$description', ";
				$data .= "is_lab = '$is_lab', ";
				$data .= "program_id = '$program_id', ";
				$data .= "building_id = '$building_id' ";
				$save = $this->db->query("INSERT INTO rooms SET $data");
				return 1;
			} else {
				return 0;
			}
		} else {
			if (empty($existing_room) || $existing_room['id'] == $id) {
				$data = "room = '$room', ";
				$data .= "description = '$description', ";
				$data .= "is_lab = '$is_lab', ";
				$data .= "building_id = '$building_id' ";
				$save = $this->db->query("UPDATE rooms SET $data WHERE id = $id");
				return 2;
			} else {
				return 0;
			}
		}
	}

	function delete_room()
	{
		extract($_POST);


		$id = intval($id);

		$delete = $this->db->query("DELETE FROM rooms WHERE id = $id");

		if ($delete) {
			return 1;
		} else {
			return 2;
		}
	}


	function save_section()
	{
		extract($_POST);
		$data = "program_id = '$program_id', ";
		$data .= "level = '$level', ";
		$data .= "section_name = '$section_name', ";
		$data .= "no_of_students = '$no_of_students' ";

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
			$id_number = $_POST['id_number'];
			$fname = $_POST['firstname'];
			$mname = $_POST['middlename'];
			$lname = $_POST['lastname'];
			$extname = $_POST['extensionname'];
			$program_id = $_POST['program_id'];
			$gender = $_POST['gender'];
			$academic_rank = $_POST['academic_rank'];
			$designation = $_POST['designation'];
			$post_graduate_studies = $_POST['post_graduate_studies'];
			$street = $_POST['street'];
			$barangay = $_POST['barangay'];
			$municipality = $_POST['municipality'];
			$province = $_POST['province'];
			$contact = $_POST['contact'];
			$email = $_POST['email'];

			$query = "INSERT INTO faculty (id_number, fname, mname, lname, extname, program_id, gender, academic_rank, designation, post_graduate_studies, street, barangay, municipality, province, contact, email) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$stmt = mysqli_prepare($this->db, $query);

			if ($stmt) {
				mysqli_stmt_bind_param($stmt, "sssssisiisssssss", $id_number, $fname, $mname, $lname, $extname, $program_id, $gender, $academic_rank, $designation, $post_graduate_studies, $street, $barangay, $municipality, $province, $contact, $email);

				if (mysqli_stmt_execute($stmt)) {
					echo '1';
				} else {
					echo 'Error: ' . mysqli_stmt_error($stmt);
				}

				mysqli_stmt_close($stmt);
			} else {
				echo 'Error: ' . mysqli_error($this->db);
			}
		}
	}
	function edit_faculty()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			// Retrieve POST data
			$idno = $_POST['id'];
			$id_number = $_POST['id_number'];
			$fname = $_POST['firstname'];
			$mname = $_POST['middlename'];
			$lname = $_POST['lastname'];
			$extname = $_POST['extensionname'];
			$gender = $_POST['gender'];
			$academic_rank = $_POST['academic_rank'];
			$designation = $_POST['designation'];
			$post_graduate_studies = $_POST['post_graduate_studies'];
			$street = $_POST['street'];
			$barangay = $_POST['barangay'];
			$municipality = $_POST['municipality'];
			$province = $_POST['province'];
			$contact = $_POST['contact'];
			$email = $_POST['email'];

			$query = "SELECT * FROM faculty WHERE id = ?";
			$stmt = $this->db->prepare($query);
			if (!$stmt) {
				echo "Error preparing SELECT query: " . $this->db->error;
				return;
			}
			$stmt->bind_param("i", $idno);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows > 0) {
				$update_query = "
                UPDATE faculty 
                SET 
                    id_number = ?, 
                    fname = ?, 
                    mname = ?, 
                    lname = ?, 
                    extname = ?, 
                    gender = ?, 
					academic_rank = ?,
                    designation = ?,
					post_graduate_studies = ?, 
                    street = ?, 
                    barangay = ?, 
                    municipality = ?, 
                    province = ?, 
                    contact = ?, 
                    email = ?
                WHERE id = ?";

				$stmt = $this->db->prepare($update_query);
				if (!$stmt) {
					echo "Error preparing UPDATE query: " . $this->db->error;
					return;
				}
				$stmt->bind_param(
					"ssssssiisssssssi",
					$id_number,
					$fname,
					$mname,
					$lname,
					$extname,
					$gender,
					$academic_rank,
					$designation,
					$post_graduate_studies,
					$street,
					$barangay,
					$municipality,
					$province,
					$contact,
					$email,
					$idno
				);

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

	function remove_schedule()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id']) && isset($_POST['offering_id'])) {
			$schedule_id = $_POST['schedule_id'];
			$course_offerings_info_id = $_POST['offering_id'];

			$offering_query = "SELECT * FROM course_offering_info WHERE id = ?";
			$stmt = $this->db->prepare($offering_query);
			$stmt->bind_param("i", $course_offerings_info_id);
			$stmt->execute();
			$offering_result = $stmt->get_result();
			$offering = $offering_result->fetch_assoc();

			$schedule_query = "SELECT * FROM schedules WHERE id = ?";
			$stmt = $this->db->prepare($schedule_query);
			$stmt->bind_param("i", $schedule_id);
			$stmt->execute();
			$schedule_result = $stmt->get_result();
			$schedule = $schedule_result->fetch_assoc();

			$update_query = "UPDATE schedules SET is_active = 0, course_offering_info_id = NULL WHERE id = ?";
			$stmt = $this->db->prepare($update_query);
			$stmt->bind_param("i", $schedule_id);
			$stmt->execute();
			if ($stmt->affected_rows > 0) {
				echo 'Schedule successfully removed!';
			} else {
				echo 'Failed to remove the schedule!';
			}
		}
	}

	function attach_schedule()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id']) && isset($_POST['offering_id'])) {
			$schedule_id = $_POST['schedule_id'];
			$section_id = $_POST['section_id'];
			$course_offerings_info_id = $_POST['offering_id'];

			$schedule_query = "SELECT day, time_start, time_end FROM schedules WHERE id = ?";
			$stmt = $this->db->prepare($schedule_query);
			$stmt->bind_param("i", $schedule_id);
			$stmt->execute();
			$schedule_result = $stmt->get_result();
			$schedule = $schedule_result->fetch_assoc();

			if (!$schedule) {
				echo json_encode(['status' => 'error', 'message' => 'Invalid schedule ID!']);
				return;
			}

			$day = $schedule['day'];
			$time_start = $schedule['time_start'];
			$time_end = $schedule['time_end'];

			$conflict_query = "SELECT * FROM course_offering_info
            JOIN schedules ON course_offering_info.id = schedules.course_offering_info_id
            WHERE course_offering_info.section_id = ? 
            AND schedules.day = ? 
            AND schedules.time_start = ? 
            AND schedules.time_end = ?";

			$stmt = $this->db->prepare($conflict_query);
			$stmt->bind_param("isss", $section_id, $day, $time_start, $time_end);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows > 0) {
				echo json_encode(['status' => 'error', 'message' => 'Schedule conflict exists.']);
				return;
			}

			$offering_query = "SELECT * FROM course_offering_info WHERE id = ? AND section_id = ?";
			$stmt = $this->db->prepare($offering_query);
			$stmt->bind_param("ii", $course_offerings_info_id, $section_id);
			$stmt->execute();
			$offering_result = $stmt->get_result();
			$offering = $offering_result->fetch_assoc();

			if ($offering) {
				$update_query = "UPDATE schedules SET is_active = 1, course_offering_info_id = ? WHERE id = ?";
				$stmt = $this->db->prepare($update_query);
				$stmt->bind_param("ii", $course_offerings_info_id, $schedule_id);
				$stmt->execute();

				if ($stmt->affected_rows > 0) {
					echo json_encode(['status' => 'success', 'message' => 'Schedule successfully attached!']);
				} else {
					echo json_encode(['status' => 'error', 'message' => 'Failed to attach schedule!']);
				}
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Invalid offering!']);
			}
		}
	}


	function delete_schedule()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id']) && isset($_POST['offering_id'])) {
			$schedule_id = $_POST['schedule_id'];
			$course_offerings_info_id = $_POST['offering_id'];

			$offering_query = "SELECT * FROM course_offering_info WHERE id = $course_offerings_info_id";
			$offering_result = $this->db->query($offering_query);
			$offering = $offering_result->fetch_assoc();

			$schedule_query = "SELECT * FROM schedules WHERE id = $schedule_id";
			$schedule_result = $this->db->query($schedule_query);
			$schedule = $schedule_result->fetch_assoc();

			$delete_query = "DELETE FROM schedules WHERE id = ?";
			$stmt = $this->db->prepare($delete_query);
			$stmt->bind_param("i", $schedule_id);
			$stmt->execute();

			if ($stmt->affected_rows > 0) {
				echo 'Schedule successfully deleted.';
			} else {
				echo 'Failed to delete the schedule.';
			}
		}
	}


	function add_course_offer()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['section_id']) && isset($_GET['course_id'])) {
			$section_id = $_GET['section_id'];
			$course_id = $_GET['course_id'];

			$check_query = $this->db->prepare("SELECT * FROM course_offering_info WHERE courses_id = ? AND section_id = ?");
			$check_query->bind_param("ss", $course_id, $section_id);
			$check_query->execute();
			$check_result = $check_query->get_result();

			$course_query = $this->db->prepare("SELECT level FROM courses WHERE id = ?");
			$course_query->bind_param("s", $course_id);
			$course_query->execute();
			$course_result = $course_query->get_result();
			$course_row = $course_result->fetch_assoc();
			$level = $course_row['level'];

			if ($check_result->num_rows === 0) {
				$offering_query = $this->db->prepare("INSERT INTO course_offering_info (courses_id, section_id) VALUES (?, ?)");
				$offering_query->bind_param("ss", $course_id, $section_id);
				$offering_query->execute();
				echo 'Successfully Offered Subject!';
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

			$check_if_exists_query = "SELECT * FROM course_offering_info WHERE courses_id = ? AND section_id = ? LIMIT 1";
			$stmt = $this->db->prepare($check_if_exists_query);
			$stmt->bind_param("ii", $courses_id, $section_id);
			$stmt->execute();
			$result = $stmt->get_result();

			$check_if_exists = $result->fetch_assoc();


			if ($check_if_exists) {
				$delete_query = "DELETE FROM course_offering_info WHERE courses_id = ? AND section_id = ?";
				$stmt = $this->db->prepare($delete_query);
				$stmt->bind_param("ii", $courses_id, $section_id);
				$stmt->execute();

				if ($stmt->affected_rows > 0) {

					echo 'Successfully Removed Course Offered!';
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
			$total_hours = $_POST['total_hours'];

			$query = "SELECT * FROM course_offering_info
                  JOIN schedules ON course_offering_info.id = schedules.course_offering_info_id
                  WHERE course_offering_info.section_id = '$section_id'
                  AND schedules.day = '$day'
                  AND schedules.time_start = '" . date('H:i', strtotime($time_start)) . "'
                  AND schedules.time_end = '" . date('H:i', strtotime($time_end)) . "'";

			// Execute the query
			$result = mysqli_query($this->db, $query);

			if (mysqli_num_rows($result) == 0) {

				$new_schedule_query = "INSERT INTO schedules (day, time_start, time_end, room_id, course_offering_info_id, total_hours)
                                   VALUES ('$day', '" . date('H:i', strtotime($time_start)) . "', '" . date('H:i', strtotime($time_end)) . "', '$room_id', '$course_offering_info_id', '$total_hours')";

				if (mysqli_query($this->db, $new_schedule_query)) {
					header('Content-Type: application/json');
					echo json_encode(array('status' => 'success'));
				} else {
					header('Content-Type: application/json');
					echo json_encode(array('status' => 'error', 'message' => mysqli_error($this->db)));
				}
			} else {
				header('Content-Type: application/json');
				echo json_encode(array('status' => 'error', 'message' => 'Same schedule already exists.'));
			}
		}
	}

	function add_faculty_load()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['course_offering_info_id'])) {

			$instructor = $_GET['instructor'];
			$course_offering_info_id = $_GET['course_offering_info_id'];

			$info_query = "SELECT faculty.*, designation.designation, designation.hours 
			FROM faculty 
			LEFT JOIN designation ON faculty.designation = designation.id 
			WHERE faculty.id = ?";
			$info_stmt = $this->db->prepare($info_query);
			$info_stmt->bind_param('i', $instructor);
			$info_stmt->execute();
			$info_result = $info_stmt->get_result();
			$info = $info_result->fetch_assoc();

			$total_hours_query = "
            SELECT DISTINCT course_offering_info_id, hours 
            FROM courses 
            INNER JOIN course_offering_info ON course_offering_info.courses_id = courses.id  
            INNER JOIN schedules ON schedules.course_offering_info_id = course_offering_info.id
            WHERE schedules.faculty_id = ? AND schedules.is_active = 1
        	";
			$total_hours_stmt = $this->db->prepare($total_hours_query);
			$total_hours_stmt->bind_param('i', $instructor);
			$total_hours_stmt->execute();
			$total_hours_result = $total_hours_stmt->get_result();

			$total_hours = 0;

			while ($row = $total_hours_result->fetch_assoc()) {
				$total_hours += (float) $row['hours'];
			}

			$course_hours_query = "
            SELECT hours FROM courses 
            INNER JOIN course_offering_info ON course_offering_info.courses_id = courses.id  
            WHERE course_offering_info.id = ? 
        	";
			$course_hours_stmt = $this->db->prepare($course_hours_query);
			$course_hours_stmt->bind_param('i', $course_offering_info_id);
			$course_hours_stmt->execute();
			$course_hours_result = $course_hours_stmt->get_result();
			$course_hours_row = $course_hours_result->fetch_assoc();
			$course_hours = $course_hours_row['hours'];

			$course_hours = isset($course_hours_row['hours']) ? (float) $course_hours_row['hours'] : 0;
			$check_course_hour = $total_hours + $course_hours;

			$load_hours_query = "SELECT unit_loads.hours FROM faculty 
                             INNER JOIN unit_loads ON faculty.academic_rank = unit_loads.id 
                             WHERE faculty.id = ?";

			$load_hours_stmt = $this->db->prepare($load_hours_query);
			$load_hours_stmt->bind_param('i', $instructor);
			$load_hours_stmt->execute();
			$load_hours_result = $load_hours_stmt->get_result();
			$load_hours_row = $load_hours_result->fetch_assoc();
			$total_load_hours = $load_hours_row['hours'];

			if (empty($info['designation'])) {
				$effective_load_hours = $total_load_hours;
				// var_dump($load_hours_row['hours']);
			} else {
				$effective_load_hours = $info['hours'];
			}
			// var_dump($total_hours);
			// var_dump($course_hours);
			// var_dump($total_load_hours);
			// var_dump($check_course_hour);
			// var_dump($info['hours']);

			$schedules_query = "SELECT * FROM schedules WHERE course_offering_info_id = ? AND faculty_id IS NULL";
			$schedules_stmt = $this->db->prepare($schedules_query);
			$schedules_stmt->bind_param('i', $course_offering_info_id);
			$schedules_stmt->execute();
			$schedules_result = $schedules_stmt->get_result();

			if ($schedules_result->num_rows > 0) {
				while ($schedule = $schedules_result->fetch_assoc()) {
					$conflict_query = "SELECT DISTINCT faculty_id, day, time_start, time_end 
                            FROM schedules
                            WHERE faculty_id = ? 
                            AND day = ? 
                            AND (
                                (time_start < ? AND time_end > ?)
                                OR (time_start >= ? AND time_end <= ?)
                                OR (time_start <= ? AND time_end >= ?)
                )";
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

						if ($check_course_hour >= $effective_load_hours) {
							http_response_code(404);
							return;
						} else {
							$update_query = "UPDATE schedules SET faculty_id = ? WHERE id = ?";
							$update_stmt = $this->db->prepare($update_query);
							$update_stmt->bind_param('ii', $instructor, $schedule['id']);
							$update_stmt->execute();

							if ($update_stmt->affected_rows > 0) {
								echo "Updated schedule ID: " . $schedule['id'] . " to faculty ID: " . $instructor;
							} else {
								echo "No update performed for schedule ID: " . $schedule['id'];
							}
						}
					} else {
						http_response_code(409);
						return;
					}
				}
			} else {
				http_response_code(404);
				echo 'No schedules found for the given course offering info ID.';
			}
		} else {
			http_response_code(400);
			echo 'Required parameters are missing.';
		}
	}

	function remove_faculty_load()
	{
		$response = array();

		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['offering_id'])) {
			$instructor = $_GET['instructor'];
			$offering_id = $_GET['offering_id'];


			$stmt = $this->db->prepare("SELECT id FROM schedules WHERE faculty_id = ? AND course_offering_info_id = ?");
			$stmt->bind_param("ii", $instructor, $offering_id);
			$stmt->execute();
			$result = $stmt->get_result();

			if ($result->num_rows > 0) {
				$response['success'] = true;
				$response['message'] = "Schedules removed successfully.";

				while ($row = $result->fetch_assoc()) {
					$schedule_id = $row['id'];
					$update_stmt = $this->db->prepare("UPDATE schedules SET faculty_id = NULL, is_overload = 0 WHERE id = ?");
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

		echo json_encode($response);
	}

	function add_overload()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['instructor']) && isset($_GET['course_offering_info_id'])) {
			$instructor = $_GET['instructor'];
			$course_offering_info_id = $_GET['course_offering_info_id'];

			$schedules_query = "SELECT * FROM schedules WHERE course_offering_info_id = ? AND faculty_id IS NULL";
			$schedules_stmt = $this->db->prepare($schedules_query);
			$schedules_stmt->bind_param('i', $course_offering_info_id);
			$schedules_stmt->execute();
			$schedules_result = $schedules_stmt->get_result();

			if ($schedules_result->num_rows > 0) {
				while ($schedule = $schedules_result->fetch_assoc()) {
					$conflict_query = "SELECT DISTINCT faculty_id, day, time_start, time_end
                    FROM schedules
                    WHERE faculty_id = ? 
                    AND day = ? 
                    AND (
                        (time_start < ? AND time_end > ?)
                        OR (time_start >= ? AND time_end <= ?)
                        OR (time_start <= ? AND time_end >= ?)
                    )";
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
						$update_query = "UPDATE schedules SET faculty_id = ?, is_overload = 1 WHERE id = ?";
						$update_stmt = $this->db->prepare($update_query);
						$update_stmt->bind_param('ii', $instructor, $schedule['id']);
						$update_stmt->execute();

						if ($update_stmt->affected_rows > 0) {
							return 1;
						} else {
							return 0;
						}
					} else {
						http_response_code(409);
						return;
					}
				}
			}
		}
	}
}
