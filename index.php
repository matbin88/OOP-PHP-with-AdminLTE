<?php
require_once 'core/init.php';

$user = new User();

if($user->isLoggedIn()) {	
	Redirect::to('dashboard.php');
} else {

?>

<?php include "includes/layout/header.html"; ?>

<body>

	<?php include "includes/layout/guest_nav.php"; ?>

	<div class="alignCenterScreen">
		<h1 class="alignCenter">Simple User Management System</h1>
	</div>
</body>

</html>

<?php
	
}

?>