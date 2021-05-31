<?php

require_once '../core/init.php';

$user = new User();

if(!$user->isLoggedIn()) {
	Redirect::to('../login.php');
}

?>

<?php include "header.php"; ?>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include "navbar.php"; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include "main_sidebar.php"; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Hello, <?php echo escape($user->data()->username); ?></h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Profile</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td style="width: 10%">Name :</td>
                                                <td><?php echo escape($user->data()->username); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Mobile :</td>
                                                <td><?php echo escape($user->data()->mobile); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Email :</td>
                                                <td><?php echo escape($user->data()->email); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer clearfix">
                                    <div class="m-0 float-right">
                                        <a href="profile_edit.php" class="btn btn-success">EDIT</a>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->

                        </div>

                    </div>

                    <?php if($user->hasPermission('admin')) { ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Users List</h3>
                                </div>
                                <!-- /.card-header -->
                                <?php //echo $user->getAllUsers(); ?>
                                <div class="card-body table-responsive p-0 mb-3">
                                    <table class="table table-head-fixed text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>SlNo</th>
                                                <th>Username</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Option</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 1;
                                                $userList = $user->getAllUsers();
                                                foreach($userList as $singleUser)
                                                {                                        
                                            ?>
                                            <tr>
                                                <td><?php echo $i++;?></td>
                                                <td><?php echo $singleUser->username;?></td>
                                                <td><?php echo $singleUser->mobile;?></td>
                                                <td><?php echo $singleUser->email;?></td>
                                                <td>
                                                    <a href="user_log.php?user_log_id=<?=$singleUser->id;?>"
                                                        class="btn btn-primary form-control">View Log</a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    <?php } else { ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Log Details</h3>
                                </div>
                                <!-- /.card-header -->
                                <?php //echo $user->getAllUsers(); ?>
                                <div class="card-body table-responsive p-0 mb-3">
                                    <table class="table table-head-fixed text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>SlNo</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 1;
                                                $logData = $user->getLogData();
                                                foreach($logData as $log)
                                                {                                        
                                            ?>
                                            <tr>
                                                <td><?php echo $i++;?></td>
                                                <td><?php echo date("d-m-Y H:i:s",strtotime($log->logged_in_time));?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    <?php } ?>

                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include "main_footer.php"; ?>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
</body>

</html>