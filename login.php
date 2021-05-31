<?php 

require_once 'core/init.php';

$user = new User();

if($user->isLoggedIn()) {	
	Redirect::to('user/dashboard.php');
}

$message = "";

if(Input::exists()) {
	if(Token::check(Input::get('token'))) {

		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'email' => array('required' => true),
			'password' => array('required' => true)
		));

		if($validation->passed()) {
			// Login user
			$user = new User();

			$remember = (Input::get('remember') === 'on') ? true : false;
			$login = $user->login(Input::get('email'), Input::get('password'), $remember);

			if($login) {
				Redirect::to('user/dashboard.php');
			} else {
        $message .= 'Sorry, logging in failed';
			}

		} else {
			foreach($validation->errors() as $error) {
				$message .= $error.'<br>';
			}
		}

	}
}

$display = "none";
if(isset($message) && $message != "")
    $display = "block";

?>

<?php include "includes/layout/header.html"; ?>

<body class="hold-transition login-page">

    <div class=" login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="index.php" class="h1"><b>SUMS</b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <div class="alert alert-danger" style="text-align:center;padding:10px;display:<?=$display?>;">
                    <?php if(isset($message)) { echo $message; } ?></div>
                <?php  if(Session::exists('registration')) { ?>
                <div class="alert alert-success form-control" style="text-align:center;padding:10px;display:block">
                    <?php echo trim(Session::flash('registration')); ?>
                </div>
                <?php } ?>
                <form action="" method="post" id="logForm">
                    <div class="input-group mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email"
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
                    <div class="field">
                        <label for="remember">
                            <input type="checkbox" name="remember" id="remember" />
                            Remember me
                        </label>
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <input type="hidden" id="token" name="token" value="<?php echo Token::generate(); ?>" />
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <div class="bottom-link text-center mt-2">
                    <a href="register.php" class="text-center">Register a new membership</a>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

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
        return this.optional(element) || /^[\w.]+$/i.test(value);
    }, "Letters, numbers, underscores and dot only please");

    $('#logForm').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true
            }
        },
        messages: {
            email: {
                required: "Please enter your Email",
                email: "Please enter a vaild Email id"
            },
            password: {
                required: "Please provide a password"
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