<?php 
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=gbk" />
    <title>My Web 2.0 Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles/default/default.css" />
    <script type="text/javascript" src="lib/prototype/prototype.js"></script>
    <script type="text/javascript" src="lib/scriptaculous/scriptaculous.js?load=effects"></script>
    <script type="text/javascript" src="lib/fusioncharts/FusionCharts.js"></script>
		<script type="text/javascript" src="scripts/simple.js"></script>
</head>
<body>

<div id="header">
	<h1>Web 2.0 Programming</h1>
<?php 
if(isset($_SESSION["student_id"])){
?>
	<ul id="navigator">
		<li id="hw"><a href="#">Homeworks</a></li>
		<li id="proj"><a href="#">Project</a></li>
		<li id="extra"><a href="#">Extra</a></li>
	</ul>
	<p><a id="export_summary" href="#">Export genneral summary</a>
	<a id="logout" href="logout.php">Logout</a></p>
<?php 
}
?>
</div>

