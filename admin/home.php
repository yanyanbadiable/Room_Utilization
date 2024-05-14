<?php include 'db_connect.php' ?>

<style>
    span.float-right.summary_icon {
        font-size: 3rem;
        position: absolute;
        bottom: 10;
        left: 1rem;
        color: #ffffff;
    }

    h1 {
        font-size: 32px;
        font-weight: bold;
    }

    body {
        background-color: #ffffff;

    }

    .card {
        position: relative;
        border-radius: 10px;
        color: #ffffff;
        background-color: #DD4B39;
        box-shadow: rgba(0, 0, 0, 0.30) 0px 3px 8px;
    }

    .card-body {
        font-size: 3rem;
        flex-direction: column;
        display: flex;
        justify-content: end;
        align-items: end;
    }

    .card-body .total {
        display: flex;
        flex-direction: column;
        justify-content: end;
        align-items: end;
    }

    .card-body .total h3 {
        font-size: 20px;
    }

    .card-footer {
        display: flex;
        justify-content: end;
        align-items: end;
    }

    .card-footer * {
        color: #ffffff;
    }

    .card-footer a:hover {
        text-decoration: none;
        color: rgba(255, 255, 255, 0.80);
    }
</style>



<div class="container-fluid">
    <div class="row">
        <section class="content-header col-md-12 d-flex align-items-center justify-content-between mb-3">
            <h3><i class="fas fa-fw fa-tachometer-alt"></i> Dashboard</h3>
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="index.php?page=home"><i class="fa fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"> Dashboard</li>
            </ol>
        </section>
        <section class="content">
            <div class="col-sm-12">
                <div class="grid row gap-4 row-gap-4">
                    <div class=" col-md-12 mb-4 col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-body">
                                <span class="float-right summary_icon"><i class="fa fa-list"></i></span>
                                <div class="total">
                                    <?php
                                    $total_courses = $conn->query("SELECT count(id) as total FROM courses")->fetch_assoc()['total'];
                                    echo $total_courses;
                                    ?>
                                    <h3>Course List</h3>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a class="text-danger" href="index.php?page=courses"><i class="fa fa-eye text-danger"></i> View Course List</a>
                            </div>
                        </div>
                    </div>

                    <div class=" col-md-12 mb-4 col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-body">
                                <span class="float-right summary_icon"><i class="fas fa-door-open"></i></span>
                                <div class="total">
                                    <?php
                                    $total_rooms = $conn->query("SELECT count(id) as total FROM rooms")->fetch_assoc()['total'];
                                    echo $total_rooms;
                                    ?>
                                    <h3>Room List</h3>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a class="text-danger" href="index.php?page=room"><i class="fa fa-eye text-danger"></i> View Room List</a>
                            </div>
                        </div>
                    </div>

                    <div class=" col-md-12 mb-4 col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-body">
                                <span class="float-right summary_icon"><i class="fas fa-layer-group"></i></span>
                                <div class="total">
                                    <?php
                                    $total_sections = $conn->query("SELECT count(id) as total FROM sections")->fetch_assoc()['total'];
                                    echo $total_sections;
                                    ?>
                                    <h3>Section List</h3>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a class="text-danger" href="index.php?page=section"><i class="fa fa-eye text-danger"></i> View Section List</a>
                            </div>
                        </div>
                    </div>

                    <div class=" col-md-12 mb-4 col-lg-6 col-xl-4">
                        <div class="card">
                            <div class="card-body">
                                <span class="float-right summary_icon"><i class="fa fa-user-tie"></i></span>
                                <div class="total">
                                    <?php
                                    $total_faculties = $conn->query("SELECT count(id) as total FROM faculty")->fetch_assoc()['total'];
                                    echo $total_faculties;
                                    ?>
                                    <h3>Faculty List</h3>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a class="text-danger" href="index.php?page=faculty"><i class="fa fa-eye text-danger"></i> View Faculty List</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>