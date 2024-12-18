<?php

// $conn= new mysqli('sql100.infinityfree.com','if0_37775276','5ce6rvKoxGtMlxx','if0_37775276_scheduling_db')or die("Could not connect to mysql".mysqli_error($con));

$conn = new mysqli('localhost', 'root', '', 'scheduling_db') or die("Could not connect to mysql" . mysqli_error($con));
