<?php
function Redirect($url, $permanent = false)
{
    if (headers_sent() === false) {
    	header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
    }
    exit();
}

/**
 * Determine if a redirect is needed
 * This function defines when it is necessary to redirect
 * Compares url params with posted values
 */
function needRedirect() {
	/* Do any of the values NOT match? */
	return strcmp($_GET['name'], $_POST['name']) != 0 ||
		   strcmp($_GET['dept'], $_POST['dept']) != 0 ||
		   strcmp($_GET['supr'], $_POST['supr']) != 0;
}

/**
 * Build Redirect URL
 * This function builds the URL that will be redirected to
 */
function buildRedirectURL() {
	$query  = "?name=" . (( strcmp($_GET['name'], $_POST['name']) != 0 )? $_POST['name'] : $_GET['name']);
	$query .= "&dept=" . (( strcmp($_GET['dept'], $_POST['dept']) != 0 )? $_POST['dept'] : $_GET['dept']);
	$query .= "&supr=" . (( strcmp($_GET['supr'], $_POST['supr']) != 0 )? $_POST['supr'] : $_GET['supr']);

	return $_SERVER['PATH_INFO'] . $query;
}

$expire = time()+60*60*24*14;
$path =  '/';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	setcookie("timesheet-data", json_encode($_POST), $expire, $path);

	if ( needRedirect() ) { /* Redirect is needed */
		setcookie("timesheet-redirect", true, $expire, $path);
		Redirect( buildRedirectURL(), false );

	} else {
		// Redirect is NOT needed
	}
} else {
	// Delete cookie
	setcookie("timesheet-redirect", "", time()-3600, $path);
	$_POST = json_decode($_COOKIE["timesheet-data"], true);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-cell-Type" content-cell="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="../css/student.css" />
<title>Student Voucher</title>
</head>

<body>

	<div id="page-wrapper">

		<h1>STUDENT ASSISTANT ATTENDANCE VOUCHER</h1>

	    <?php include "student-voucher.php"; ?>

	    <div class="certify print">I CERTIFY THAT THE HOURS REPORTED ABOVE ARE TRUE AND CORRECT.</div>

		<div class="signature">
			<div>Student Employee Signature</div>
			<div>Date</div>
			<div>Supervisor Signature</div>
			<div>Date</div>
		</div>


		<div class="payroll">
			<div class="payroll-left">
			*When school is in session, student assistants may work up to but not in excess of 20 hours per week<br />
			*When school is NOT in session, student assistants may work up to a maximum of 40 hours per week<br />
			 - but SHALL NOT be scheduled to work overtime.
			</div>
			<div class="payroll-center">
			</div>
			<div class="payroll-right">
			    <div>FOR PAYROLL SERVICES USE ONLY</div>
			    <div>Position #:</div>
			    <div>Department:</div>
			</div>
		</div>

	</div> 
</body>
</html>