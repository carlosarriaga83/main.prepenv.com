<?php
	
setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');

	// Initialize the session.
	// If you are using session_name("something"), don't forget it now!
	session_start();
	
	//echo 'Logout';die;
	//if (isset($_SESSION['S_ID'])){$S_ID = $_SESSION['S_ID'];}

	// Unset all of the session variables.
	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}

	// Finally, destroy the session.
	session_destroy();
	
	
	//header("Location: ../index.php?s_id=" . $S_ID);
	header("Location: ../index.php");die;

	
?>