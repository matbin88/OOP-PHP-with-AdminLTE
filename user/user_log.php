<?php

require_once '../core/init.php';

$user = new User();

if(!$user->isLoggedIn()) {
	Redirect::to('../login.php');
} else if(!$user->hasPermission('admin')) {
    Redirect::to('dashboard.php');
}

$user_log_data = array();

if(!$user_log_id = Input::get('user_log_id')) {
	Redirect::to('dashboard.php');
} else {
	$user_log = new User($user_log_id);
	if(!$user->exists()) {
		Redirect::to(404);
	} else {
		// user exists
		$user_log_data = $user_log->getLogData();
	}
}

?>

<!DOCTYPE html>
<html lang="en">

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
                            <h2>User Log</h2>
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
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td style="width: 10%">Name :</td>
                                                <td><?php echo escape($user_log->data()->username); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Mobile :</td>
                                                <td><?php echo escape($user_log->data()->mobile); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Email :</td>
                                                <td><?php echo escape($user_log->data()->email); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.card -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Log Details</h3>
                                </div>
                                <!-- /.card-header -->
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
                                                foreach($user_log_data as $log)
                                                {                                        
                                            ?>
                                            <tr>
                                                <td><?php echo $i++;?></td>
                                                <td><?php echo date("d-m-Y H:i:s",strtotime($log->logged_in_time));?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>

                    </div>

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