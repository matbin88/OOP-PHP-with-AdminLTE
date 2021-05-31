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
            'username' => array(
                'required' => true,
                'min' => 2,
                'max' => 20,
                'alphanumeric' => true
            ),
            'mobile' => array(
                'mobile' => true,
                'unique_except_this' => array(
                    'table' => 'users',
                    'id' => $user->data()->id
                )
            )
        ));
        
        if($validation->passed()) {
            try {
                $file_name = "";
                if(isset($_FILES['userImage']) && $_FILES['userImage']['size'] > 0){
                    $errors= array();
                    $file_name = $_FILES['userImage']['name'];
                    $file_size =$_FILES['userImage']['size'];
                    $file_tmp =$_FILES['userImage']['tmp_name'];
                    $file_type=$_FILES['userImage']['type'];
                    $file_ext=strtolower(end(explode('.',$_FILES['userImage']['name'])));

                    $extensions= array("jpeg","jpg","png");

                    if(in_array($file_ext,$extensions)=== false){
                        $message .= "extension not allowed, please choose a JPEG or PNG file.";
                    }

                    if($file_size > 2097152){
                        $message .= 'File size must be less than 2 MB';
                    }

                    if($message == ""){
                        //remove current photo
                        if($user->data()->photo != "" && $user->data()->photo != NULL)
                        {
                            $fileToRemove = "../uploads/".strtotime($user->data()->joined).".".strtolower(end(explode('.',$user->data()->photo)));
                            unlink($fileToRemove);
                        }

                        move_uploaded_file($file_tmp,"../uploads/".strtotime($user->data()->joined).".".$file_ext);                            
                    }
                }

                $user->update(array(
                    'username'	=> Input::get('username'),
                    'mobile'	=> Input::get('mobile'),
                    'photo'	=> $file_name
                ));

                if($message != "")
                    $savemessage = "Profile Updated Successfully! But Photo is not Uploaded.";
                else
                    $savemessage = "Profile Updated Successfully!";
                    
                Session::flash('profile_update', $savemessage);

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
                            <h3>Edit Profile</h3>
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
                                <div class="card-header">
                                    <h3 class="card-title">Profile</h3>
                                </div>
                                <!-- /.card-header -->
                                <form action="" method="post" id="profileForm" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <label for="name" class="">Username</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="username" id="username" class="form-control"
                                                placeholder="Full name"
                                                value="<?php echo escape($user->data()->username); ?>"
                                                autocomplete="off">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <label for="email" class="">Email</label>
                                        <div class="input-group mb-3">
                                            <input type="email" name="email" id="email" class="form-control"
                                                placeholder="Email" value="<?php echo escape($user->data()->email); ?>">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-envelope"></span>
                                                </div>
                                            </div>
                                        </div> -->
                                        <label for="mobile" class="">Mobile</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="mobile" id="mobile" class="form-control"
                                                placeholder="Mobile"
                                                value="<?php echo escape($user->data()->mobile); ?>">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-phone"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <label for="userImage" class="">Photo</label>
                                        <div class="input-group mb-3">
                                            <input type="file" name="userImage" id="userImage" autocomplete="off"
                                                class="form-control" placeholder="Photo">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-file"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer clearfix">
                                        <div class="m-0 float-right">
                                            <input type="hidden" id="token" name="token"
                                                value="<?php echo Token::generate(); ?>" />
                                            <button type="button" class="btn btn-default"
                                                onClick="window.location.reload();">Reset</button>
                                            <button type="submit" class="btn btn-primary">Update</button>
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

        jQuery.validator.addMethod("alphanumeric", function(value, element) {
            return this.optional(element) || /^[\w. ]+$/i.test(value);
        }, "Letters, numbers, underscores and dot only please");

        $('#profileForm').validate({
            rules: {
                username: {
                    required: true,
                    alphanumeric: true,
                },
                userImage: {
                    required: false,
                    extension: "jpg|jpeg|png"
                },
                mobile: {
                    number: true,
                    rangelength: [10, 10]
                }
            },
            messages: {
                mobile: {
                    required: "Please enter a mobile number",
                    rangelength: "Please enter a vaild 10 Digit Mobile Number"
                },
                userImage: {
                    extension: "Please use jpg,jprg or png format"
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