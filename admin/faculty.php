<?php
include('db_connect.php');

$query = "SELECT users.*, program.program_code, program.program_name FROM users INNER JOIN program ON users.program_id = program.id WHERE users.type = 1";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if ($result) {
    // Fetch all rows from the result set
    $instructors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $instructors[] = $row;
    }
} else {
    // Handle query error
    echo "Error: " . mysqli_error($conn);
}
?>

<div class="container-fluid">
    <div class="row">
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-users"></i> List of Instructors</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> List of Instructors</li>
            </ol>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card card-default shadow mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="card-title">List of Instructors</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID Number</th>
                                        <th>Name</th>
                                        <th width="40%">Program</th>
                                        <th width="20%">Designation</th>
                                        <th width="5%">Profile</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($instructors as $instructor) : ?>
                                        <?php
                                        $infoQuery = "SELECT * FROM faculty WHERE user_id = " . $instructor['id'];
                                        // var_dump($instructor['id']);
                                        $infoResult = mysqli_query($conn, $infoQuery);
                                        $info = mysqli_fetch_assoc($infoResult);
                                        ?>
                                        <tr>
                                            <td><?php echo $instructor['username']; ?></td>
                                            <td><?php echo strtoupper($instructor['lname']) . ', ' . strtoupper($instructor['fname']); ?></td>
                                            <td><?php echo $instructor['program_name'] . ' (' . $instructor['program_code'] . ')'; ?></td>
                                            <td><?php echo $info['designation']; ?></td>
                                            <td><a href="index.php?page=view_faculty&id=<?php echo $instructor['id'] ?>" class="btn btn-flat btn-success"><i class="fa fa-user"></i></a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<style>
    .card-header {
        border-bottom: none;
    }
</style>
