<?php include 'db_connect.php';
$user_program_id = $_SESSION['login_program_id'];
?>

<div class="container-fluid">
    <div class="row">
        <section class="content-header col-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-fw fa-tachometer-alt"></i> Dashboard</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </section>
        <section class="content col-12">
            <div class="row">
                <?php
                $cards = [
                    ['icon' => 'fas fa-th-list', 'title' => 'Course List', 'link' => 'index.php?page=courses', 'query' => 'SELECT COUNT(id) as total FROM courses WHERE program_id = ?'],
                    ['icon' => 'fas fa-door-open', 'title' => 'Room List', 'link' => 'index.php?page=room', 'query' => 'SELECT COUNT(id) as total FROM rooms WHERE program_id = ?'],
                    ['icon' => 'fas fa-layer-group', 'title' => 'Section List', 'link' => 'index.php?page=section', 'query' => 'SELECT COUNT(id) as total FROM sections WHERE program_id = ?'],
                    ['icon' => 'fas fa-user-tie', 'title' => 'Faculty List', 'link' => 'index.php?page=faculty', 'query' => 'SELECT COUNT(id) as total FROM faculty WHERE program_id = ?']
                ];

                foreach ($cards as $card) {
                    $stmt = $conn->prepare($card['query']);
                    $stmt->bind_param("i", $user_program_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $total = $result->fetch_assoc()['total'];
                ?>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-3">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <a href="<?php echo $card['link']; ?>" class="text-xs font-weight-bold text-danger text-uppercase mb-1 d-block" style="font-size: 1rem;">
                                            <?php echo $card['title']; ?>
                                        </a>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" style="font-size: 4rem;"><?php echo $total; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="<?php echo $card['icon']; ?> fa-2x text-gray-300" style="font-size: 4rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    </div>
</div>