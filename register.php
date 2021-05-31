<?php 
require_once 'core/init.php';

$user = new User();

if($user->isLoggedIn()) {	
	Redirect::to('user/dashboard.php');
}

$message = "";

if (Input::exists() && (Input::get('captcha') != $_SESSION["captcha"] OR $_SESSION["captcha"]==''))  {
    $message .= "Invalid Captcha!";
}

if($message == "")
{
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
                    'unique' => 'users'
                ),
                'email' => array(
                    'required' => true,
                    'email' => true,
                    'unique' => 'users'
                ),
                'password' => array(
                    'required' => true,    
                    'min' => 6
                ),
                'confirm_password' => array(
                    'required' => true,    
                    'min' => 6,
                    'matches' => 'password'
                )
            ));
            
            if($validation->passed()) {
                $user = new User();
                $salt = Hash::salt(32);
                $joinedDate = date('Y-m-d H:i:s');
                try {                
                    //upload image
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
                            move_uploaded_file($file_tmp,"uploads/".strtotime($joinedDate).".".$file_ext);                            
                        }
                    }
                    
                    $user->create(array(
                        'username'	=> Input::get('username'),
                        'mobile'	=> Input::get('mobile'),
                        'email'	=> Input::get('email'),
                        'photo'	=> $file_name,
                        'password'	=> Hash::make(Input::get('password'), $salt),
                        'salt'		=> $salt,
                        'joined'	=> $joinedDate,
                        'group'		=> 1,
                    ));

                    if($message != "")
                        $message = "Registration Successfull. Log in now!<br>Photo is not Uploaded.<br>".$message;
                    else
                        $message = "Registration Successfull. Log in now!";

                    Session::flash('registration', $message);                
                    Redirect::to('login.php');

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
}

$display = "none";
if(isset($message) && $message != "")
    $display = "block";

?>

<?php include "includes/layout/header.html"; ?>

<body class="hold-transition register-page">
    <div class="register-box">
        <div class="card card-outline card-primary mt-3 mb-3">
            <div class="card-header text-center">
                <a href="index.php" class="h1"><b>SUMS</b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Register a new membership</p>
                <div class="alert alert-danger" style="text-align:center;padding:10px;display:<?=$display?>;">
                    <?php if(isset($message)) { echo $message; } ?></div>
                <form action="" method="post" id="regForm" enctype="multipart/form-data">
                    <div class="input-group mb-3">
                        <input type="text" name="username" id="username" class="form-control" placeholder="Full name*"
                            value="<?php echo escape(Input::get('username')); ?>" autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile"
                            value="<?php echo escape(Input::get('mobile')); ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email*"
                            value="<?php echo escape(Input::get('email')); ?>">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" id="password" autocomplete="off" class="form-control"
                            placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="confirm_password" id="confirm_password" autocomplete="off"
                            class="form-control" placeholder="Confirm password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="file" name="userImage" id="userImage" autocomplete="off" class="form-control-file"
                            placeholder="Photo">
                    </div>
                    <div class="input-group mb-3">
                        <img src="captcha.php">
                        <input type="text" name="captcha" id="captcha" autocomplete="off" class="form-control"
                            placeholder="Enter Captcha">
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="token" name="token" value="<?php echo Token::generate(); ?>" />
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <div class="bottom-link text-center mt-2">
                    <a href="login.php">I already have a membership</a>
                </div>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <!-- /.register-box -->

</body>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- jquery-validation -->
<script src="plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="plugins/jquery-validation/additional-methods.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
$(function() {
    /* $.validator.setDefaults({
        submitHandler: function() {
            alert("Form successful submitted!");
        }
    }); */

    jQuery.validator.addMethod("alphanumeric", function(value, element) {
        return this.optional(element) || /^[\w. ]+$/i.test(value);
    }, "Letters, numbers, underscores and dot only please");

    $('#regForm').validate({
        rules: {
            username: {
                required: true,
                alphanumeric: true,
            },
            mobile: {
                number: true,
                rangelength: [10, 10]
            },
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
                minlength: 6
            },
            confirm_password: {
                required: true,
                minlength: 6,
                equalTo: "#password"
            },
            userImage: {
                required: false,
                extension: "jpg|jpeg|png"
            },
        },
        messages: {
            mobile: {
                required: "Please enter a mobile number",
                rangelength: "Please enter a vaild 10 Digit Mobile Number"
            },
            email: {
                email: "Please enter a vaild email address"
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long"
            },
            confirm_password: {
                required: "Please confirm your password",
                minlength: "Your password must be at least 5 characters long",
                equalTo: "password and confirm password must match"
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

</html>