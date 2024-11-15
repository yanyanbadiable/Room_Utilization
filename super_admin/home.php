<?php include '../admin/db_connect.php' ?>
<style>
    .card {
        border-left: 0.3rem solid #a91414 !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        /* background-color: #f8f9fas; */
    }

    .card-link {
        text-decoration: none;
    }
</style>
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
                    ['icon' => 'fas fa-users', 'title' => 'Admin List', 'link' => 'index.php?page=user', 'query' => 'SELECT count(id) as total FROM users WHERE type = 0'],
                    ['icon' => 'fas fa-cogs', 'title' => 'Program List', 'link' => 'index.php?page=department', 'query' => 'SELECT count(id) as total FROM program'],
                    ['icon' => 'fas fa-building', 'title' => 'Building List', 'link' => 'index.php?page=building', 'query' => 'SELECT count(id) as total FROM building'],
                    ['icon' => 'fas fa-user-graduate', 'title' => 'Academic Rank List', 'link' => 'index.php?page=unit_loads', 'query' => 'SELECT count(id) as total FROM unit_loads'],
                    ['icon' => 'fas fa-briefcase', 'title' => 'Designation List', 'link' => 'index.php?page=designation', 'query' => 'SELECT count(id) as total FROM designation'],
                    ['icon' => 'fas fa-calendar-week', 'title' => 'Semester List', 'link' => 'index.php?page=semester', 'query' => 'SELECT count(id) as total FROM semester']
                ];

                foreach ($cards as $card) {
                    $total = $conn->query($card['query'])->fetch_assoc()['total'];
                ?>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <a href="<?php echo $card['link']; ?>" class="card-link">
                            <div class="card border-left-danger shadow h-100 py-3">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1" style="font-size: 1rem;">
                                                <?php echo $card['title']; ?>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" style="font-size: 4rem;"><?php echo $total; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="<?php echo $card['icon']; ?> fa-2x text-gray-300" style="font-size: 4rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </section>
    </div>
</div>