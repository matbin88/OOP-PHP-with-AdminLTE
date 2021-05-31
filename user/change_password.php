<?php

require_once '../core/init.php';

$user = new User();

if(!$user->isLoggedIn()) {
	Redirect::to('../login.php');
}

$message = "";

if(Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'current_password' => array(
                'required' => true,    
                'min' => 6
            ),
            'new_password' => array(
                'required' => true,    
                'min' => 6
            ),
            'confirm_password' => array(
                'required' => true,    
                'min' => 6,
                'matches' => 'new_password'
            )
        ));
        
        if($validation->passed()) {
            try {                

                if(Hash::make(Input::get('current_password'), $user->data()->salt) !== $user->data()->password) {
                    $message .= 'Your current password is wrong.';
                } else {
                    $salt = Hash::salt(32);
                    $user->update(array(
                        'password' => Hash::make(Input::get('new_password'), $salt),
                        'salt' => $salt
                    ));
                }

                if($message == "")
                    Session::flash('profile_update', "Password Updated Successfully!");

            } catch(Exception $e) {              
                $message = $e->getMessage();
            }
        } else {
            foreach($validation->errors() as $error) {              
                $message .= $error.'<br>';
            }
        }
    }
}

$user = new User();

$display = "none";
if(isset($message) && $message != "")
    $display = "block";

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
                            <h3>Change Password</h3>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Alert -->

                            <?php  if(Session::exists('profile_update')) { ?>
                            <div class="alert alert-success form-control"
                                style="text-align:center;padding:10px;display:block">
                                <?php echo trim(Session::flash('profile_update')); ?>
                            </div>
                            <?php } ?>
                            <div class="alert alert-danger"
                                style="text-align:center;padding:10px;display:<?=$display?>;">
                                <?php if(isset($message)) { echo $message; } ?></div>
                            <div class="card">
                                <!-- /.card-header -->
                                <form action="" method="post" id="profileForm" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <label for="current_password" class="">Current Password</label>
                                        <div class="input-group mb-3">
                                            <input type="password" name="current_password" id="current_password" autocomplete="off"
                                                class="form-control" placeholder="Password">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-lock"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <label for="new_password" class="">New Password</label>
                                        <div class="input-group mb-3">
                                            <input type="password" name="new_password" id="new_password" autocomplete="off"
                                                class="form-control" placeholder="Password">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-lock"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <label for="confirm_password" class="">Confirm Password</label>
                                        <div class="input-group mb-3">
                                            <input type="password" name="confirm_password" id="confirm_password"
                                                autocomplete="off" class="form-control" placeholder="Confirm password">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-lock"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer clearfix">
                                        <div class="m-0 float-right">
                                            <input type="hidden" id="token" name="token"
                                                value="<?php echo Token::generate(); ?>" />
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
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
    <!-- jquery-validation -->
    <script src="../plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="../plugins/jquery-validation/additional-methods.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>

    <script>
    $(function() {

        $('#profileForm').validate({
            rules: {
                current_password: {
                    required: true,
                    minlength: 6
                },
                new_password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#new_password"
                }
            },
            messages: {
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long"
                },
                confirm_password: {
                    required: "Please confirm your password",
                    minlength: "Your password must be at least 5 characters long",
                    equalTo: "new password and confirm password must match"
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.input-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
    </script>

</body>

</html>