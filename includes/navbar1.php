<?php
include '../admin/db_connect.php';

// Get user program info
$user_department_id = $_SESSION['login_department_id'];
$department = $conn->query("SELECT id, department_code FROM department WHERE id = $user_department_id");
$row = $department->fetch_assoc();

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

?>

<style>
    #accordionSidebar {
        background-color: #a91414;
    }

    hr.sidebar-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.20) !important;
    }

    .sidebar-brand-icon img {
        width: 40px;
        height: 40px;
        background-color: #ffffff;
        border-radius: 50%;
    }

    ._hover:hover {
        color: #e74a3b !important;
    }

    ._hover:hover a {
        background-color: #e74a3b !important;
    }

    #alert_toast {
        position: fixed;
        z-index: 9999;
        top: 3rem;
        right: 0;
        min-width: 14rem;
    }

    /* span {
        margin-left: 5px;
    } */

    @media print {
        #accordionSidebar {
            display: none;
        }
    }
</style>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?page=home">
                <div class="sidebar-brand-icon">
                    <img src="../assets/img/1-removebg-preview.jpeg" alt="logo">
                </div>
                <div class="sidebar-brand-text ml-3">ADRUFWMS</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item <?php echo $page == 'home' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=home">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span class="ml-1">Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">Main</div>


            <!-- Nav Item - Faculty Loading -->
            <li class="nav-item <?php echo $page == 'faculty_loading' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=faculty_loading">
                    <i class="fa fa-list"></i>
                    <span class="ml-1">Faculty Loading</span>
                </a>
            </li>

            <!-- Nav Item - Room Utilization -->
            <li class="nav-item <?php echo $page == 'room_report' ? 'active' : ''; ?>">
                <a class="nav-link" href="index.php?page=room_report">
                    <i class="far fa-file-alt"></i> <span class="ml-1">Room Utilization</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">Management</div>

            <!-- Nav Item - Course Management -->
            <li class="nav-item <?php echo in_array($page, ['courses', 'course_offering']) ? 'active' : ''; ?>">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-book"></i>
                    <span class="ml-1">Course Management</span>
                </a>
                <div id="collapseUtilities" class="collapse <?php echo in_array($page, ['courses', 'course_offering']) ? 'show' : ''; ?>" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-danger py-2 collapse-inner rounded">
                        <a class="_hover collapse-item text-white" href="index.php?page=courses">
                            <i class="fa fa-edit"></i> <span class="ml-1">Manage Course</span>
                        </a>
                        <a class="_hover collapse-item text-white" href="index.php?page=course_offering">
                            <i class="fa fa-edit"></i> <span class="ml-1">Course Offering</span>
                        </a>
                        <a class="_hover collapse-item text-white" href="index.php?page=course_scheduling">
                            <i class="fa fa-edit"></i> <span class="ml-1">Course Scheduling</span>
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Space Management -->
            <li class="nav-item <?php echo in_array($page, ['section', 'room']) ? 'active' : ''; ?>">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRoom" aria-expanded="true" aria-controls="collapseRoom">
                    <i class="fas fa-cogs"></i>
                    <span class="ml-1">Room Management</span>
                </a>
                <div id="collapseRoom" class="collapse" aria-labelledby="headingRoom" data-parent="#accordionSidebar">
                    <div class="bg-danger py-2 collapse-inner rounded ">
                        <a class="_hover collapse-item text-white" href="index.php?page=section">
                            <i class="fas fa-cogs"></i> <span class="ml-1">Manage Section</span>
                        </a>
                        <a class="_hover collapse-item text-white" href="index.php?page=room">
                            <i class="fas fa-cogs"></i> <span class="ml-1">Manage Room</span>
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Faculty Management -->
            <li class="nav-item <?php echo in_array($page, ['manage_faculty', 'faculty']) ? 'active' : ''; ?>">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFaculty" aria-expanded="true" aria-controls="collapseFaculty">
                    <i class="fas fa-user"></i>
                    <span class="ml-1">Faculty Management</span>
                </a>
                <div id="collapseFaculty" class="collapse" aria-labelledby="headingFaculty" data-parent="#accordionSidebar">
                    <div class="bg-danger py-2 collapse-inner rounded ">
                        <a class="_hover collapse-item text-white" href="index.php?page=manage_faculty">
                            <i class="fa fa-user-plus"></i> <span class="ml-1">Add Faculty</span>
                        </a>
                        <a class="_hover collapse-item text-white" href="index.php?page=faculty">
                            <i class="fas fa-list"></i> <span class="ml-1">View Faculty</span>
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Report Management -->
            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReport" aria-expanded="true" aria-controls="collapseReport">
                    <i class="fas fa-pencil-alt"></i>
                    <span class="ml-1">Report Management</span>
                </a>
                <div id="collapseReport" class="collapse" aria-labelledby="headingReport" data-parent="#accordionSidebar">
                    <div class="bg-danger py-2 collapse-inner rounded ">
                        <a class="_hover collapse-item text-white" href="index.php?page=room_report">
                            <i class="far fa-file-alt"></i> <span class="ml-1">Room Utilization</span>
                        </a>
                    </div>
                </div>
            </li> -->

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-3 d-none d-lg-inline text-gray-600 small"><?php echo $row['department_code'] ?> - ADMIN</span>
                                <img class="img-profile rounded-circle" src="../assets/img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="index.php?page=change_password">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <main id="view-panel">
                    <div class="container-fluid">
                        <div class="toast mx-3 my-2" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-body text-white d-flex align-items-center">
                                <i class="icon big-icon mr-2"></i>
                                <div>
                                    <h6 class="mb-1" id="notificationText"></h6>
                                    <h6 class="message m-0"></h6>
                                </div>
                            </div>
                        </div>

                        <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>
                        <?php include $page . '.php'; ?>
                    </div>
                </main>
                <!-- End of Page Content -->

            </div>
            <!-- End of Main Content -->
        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Logout?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Are you sure you want to logout?</div>
                <div class="modal-footer">
                    <a class="btn btn-primary" href="ajax.php?action=logout">Logout</a>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <!-- <script src="../assets/vendor/jquery/jquery.min.js"></script> -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="../assets/js/sb-admin-2.min.js"></script>

</body>