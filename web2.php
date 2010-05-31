<?php
/**
 * @author: Wang, Qing
 * @date: 2010-05-28
 * @version: 1.0
 */

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
?>