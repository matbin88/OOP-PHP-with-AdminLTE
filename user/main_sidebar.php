<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
        <img src="../dist/img/s.png" alt="SUMS Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SUMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo $user->getUserImage(); ?>" class="img-circle elevation-2" alt="User Image" id="user_sidebar_image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo escape($user->data()->username); ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="change_password.php" class="nav-link">
                        <i class="nav-icon fas fa-key"></i>
                        <p>
                            Change Password
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            Logout
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<script>
refreshImage("user_sidebar_image", "<?php echo $user->getUserImage(); ?>");

function refreshImage(imgElement, imgURL) {
    // create a new timestamp 
    var timestamp = new Date().getTime();

    var el = document.getElementById(imgElement);

    var queryString = "?t=" + timestamp;

    el.src = imgURL + queryString;
}
</script>