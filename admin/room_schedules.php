<?php include('db_connect.php'); ?>
<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM rooms WHERE id=" . $_GET['id'])->fetch_array();
    foreach ($qry as $k => $v) {
        $$k = $v;
    }
}
?>

<section class="col-lg-12">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Room Schedules</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <!-- <table class="table table-bordered"> -->
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Room</th>
                        <th>Description</th>
                        <th>Building</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $rooms = $conn->query("SELECT rooms.*, building.building FROM rooms INNER JOIN building ON rooms.building_id = building.id;");
                    if (!$rooms) {
                        die('Invalid query: ' . $conn->error);
                    }
                    while ($row = $rooms->fetch_assoc()) :
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $i++ ?></td>
                            <td class=""><?php echo htmlspecialchars($row['room']) ?></td>
                            <td class=""><?php echo htmlspecialchars($row['description']) ?></td>
                            <td class=""><?php echo htmlspecialchars($row['building']) ?></td>
                            <td class="text-center">
                                <?php if ($row['is_available'] == 1) : ?>
                                    <span class="badge badge-success" style="font-size: 16px;">Active</span>
                                <?php else : ?>
                                    <span class="badge badge-danger" style="font-size: 16px;">Inactive</span>
                                <?php endif; ?>





                            </td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-primary" href="index.php?page=view_room_schedule">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>