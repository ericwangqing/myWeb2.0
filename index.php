<?php
/**
 * @author: Wang, Qing
 * @date: 2010-05-28
 * @version: 1.0
 */
require "snapshot.php";
require "web2.php";
require "lib/fusioncharts/FusionCharts.php";


// start program
session_start();
if(!isset($_SESSION["student_id"])){
	session_destroy();
	header("Location: login.php");
	exit(0);
//	$_SESSION["student_id"] = "07302941";
}

loadData();
$student = getStudent($_SESSION["student_id"]);
if($student == null) {
	session_unset();
	die("Couldn't retrieve student with id ".$_SESSION['student_id']);
}

require "templates/header.php";

?>

<div id="hw_charts">
	<h2>homework chart</h2>
	<div class="content">
		<?php 
			echo renderChart("lib/fusioncharts/charts/Line.swf", "", getScoreXML($student), "myScore", 0, 0, false, false);
			echo renderChart("lib/fusioncharts/charts/Line.swf", "", getRankXML($student), "myRank", 0, 0, false, false);
		?>
		</div>
		<!--<div class="bar">sample</div>
		<h3>Rank</h3>
		<p>
		bla bla
		</p>
		
		<div class="bar">sample</div>
		<h3>Score</h3>
		<p>
		bla bla
		</p>
	-->
	<p class="expdf"><a id="expdf" href="#">Export as pdf</a></p>
</div>
<div id="overview">
<?php 
foreach($student->hw_results as $result){
	$hw = getHomework($result->hid);
	// for filling in the ftp and http url provide by hw->ftp, hw->http
	$ftp = getHWFtp($student->id, $hw);
	$http = getHWHttp($student->id, $hw);
	loadThumbnail($student->id, $hw);
	$img = getThumbnailFile($student->id, $hw->id);
?>
	<div class="entry">
		<h3>HW<?= $hw->id?>: <?= $hw->name?></h3>
		<p class="thumb"><a href="<?= $http ?>"><img src="<?= $img ?>" alt="hw<?= $hw->id?>"/></a></p>
		<dl>
			<dt>Score: </dt><dd><?= $result->score?></dd>
			<dt>Group ranking: </dt><dd><?= $result->grank?>/5</dd>
			<dt>Class ranking: </dt><dd><?= $result->crank?>/62</dd>
		</dl>
		<ul>
			<li><a href="<?= $hw->wiki ?>">HW Spec.</a></li>
			<li><a href="<?= $http ?>">View online</a></li>
			<li><a href="<?= $ftp ?>">Download Source File</a></li>
		</ul>
		<p class="intro"><?= $hw->intro ?></p>
	</div>
<?php 
}
?>	
	
<?php 
require "templates/footer.php";
?>
