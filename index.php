<?php
/**
 * @author: Wang, Qing
 * @date: 2010-05-28
 * @version: 1.0
 */
require "snapshot.php";
require "lib/fusioncharts/FusionCharts.php";
require "templates/header.php";

?>

<?php 
define('STUDENTS_FILE', 'data/students.csv');
define('HOMEWORKS_FILE', 'data/homeworks.csv');
define('RESULTS_FILE', 'data/results.csv');

class Student {
	public $id, $name, $class_name, $hw_results;
	public function __construct($id, $name, $class_name){
		$this->id = $id;
		$this->name = $name;
		$this->class_name = $class_name;
		$this->hw_results = array();
	}
	
	public function add_hw_result($hid, $hw_result){
		$this->hw_results[$hid] = $hw_result;
	}
}

class Homework {
	public $id, $name, $intro, $ftp, $http, $wiki;
	public function __construct($id, $name, $intro, $ftp, $http, $wiki){
		$this->id = $id;
		$this->name = $name;
		$this->intro = $intro;
		$this->ftp = $ftp;
		$this->http = $http;
		$this->wiki = $wiki;
	}
}

class HomeworkResult {
	public $sid, $hid, $score, $grank, $crank; // group rank, class rank
	public function __construct($sid, $hid, $score, $grank, $crank) {
		$this->sid = $sid;
		$this->hid = $hid;
		$this->score = $score;
		$this->grank = $grank;
		$this->crank = $crank;
	}
}

function loadData(){ 
	//TODO: refactor all load functions into one
	if(!isset($_SESSION["homeworks"])) 
		$_SESSION["homeworks"] = loadHomeworks(HOMEWORKS_FILE);
	if(!isset($_SESSION["students"])) 
		$_SESSION["students"] = loadStudents(STUDENTS_FILE);
	if(!isset($_SESSION["results"])) 
		$_SESSION["results"] = loadResults(RESULTS_FILE);
}

function loadHomeworks($file){
	$homeworks = array();
	$ss = file($file);
	foreach(file($file) as $homework){
		list($id, $name, $intro, $ftp, $http, $wiki) = explode(",", $homework);
		$homeworks[] = new Homework($id, $name, $intro, $ftp, $http, $wiki);
	}
	return $homeworks;
}

function loadStudents($file){
	$students = array();
	$ss = file($file);
	foreach(file($file) as $student){
		list($id, $name, $class_name) = explode(",", $student);
		$students[] = new Student($id, $name, $class_name);
	}
	return $students;
}

function loadResults($file){
	$results = array();
	foreach(file($file) as $result){
		list($sid, $hid, $score, $grank, $crank) = explode(",", $result);
		$results[] = new HomeworkResult($sid, $hid, $score, $grank, $crank);
	}
	return $results;
}

function getStudent($sid){
	$_stu = null;
	foreach($_SESSION["students"] as $student){
		if($student->id == $sid){
			$_stu = $student;
			break;
		}
	}
	// add _stu's results
	if($_stu != null){
		foreach($_SESSION["results"] as $result){
			if($result->sid == $_stu->id) $_stu->add_hw_result($result->hid, $result);
		}
	}
	return $_stu;
}

function getHomework($hid){
	$_hw = null;
	foreach($_SESSION["homeworks"] as $hw){
		if($hw->id == $hid){
			$_hw = $hw;
			break;
		}
	}
	return $_hw;
}

function loadThumbnail($sid, $hw){
	$file = getThumbnailFile($sid, $hw->id);
	if(!file_exists($file)){
		snapshot(getHWHttp($sid, $hw), $file);
	}
}

function getThumbnailFile($sid, $hid){
	return "images/".$hid.".jpg";
	// uncomment the following code when all hws are placed properly
	// , and can be viewed online
//	return "thumbnails/".$sid."_".$hid.".png"
}

function getHWHttp($sid, $hw){
	$hid = $hw->id;
	eval("\$http = \"$hw->http\";");
	return $http;
}

function getHWFtp($sid, $hw){
	$hid = $hw->id;
	eval("\$ftp = \"$hw->ftp\";");
	return $ftp;
}

// start program
//session_start();
if(!isset($_SESSION["student_id"])){
//	header("Location: login.php");
//	exit;
	$_SESSION["student_id"] = "07302950";
}
loadData();
$student = getStudent($_SESSION["student_id"]);
if($student == null) {
	session_unset();
	die("Couldn't retrieve student with id ".$_SESSION['student_id']);
}
?>

<div id="hw_charts">
	<h2>homework chart</h2>
	<div class="content">
		<?php 
			//TODO: fade chart with dynamic data, maybe by data.php
			echo renderChart("lib/fusioncharts/charts/Line.swf", "data/score.xml", "", "myScore", 0, 0, false, false);
			echo renderChart("lib/fusioncharts/charts/Line.swf", "data/crank.xml", "", "myRank", 0, 0, false, false);
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
session_unset();
?>
