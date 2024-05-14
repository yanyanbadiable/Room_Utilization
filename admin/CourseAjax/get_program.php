<?php
include('../db_connect.php');

$query = "SELECT DISTINCT program_code, program_name FROM program ORDER BY program_code";
$result = mysqli_query($conn, $query);

$programs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[] = $row;
    }
}

echo json_encode($programs);
?>
