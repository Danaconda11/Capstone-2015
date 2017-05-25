<?php
session_start();
if(isset($_SESSION['User']) == false) header("location:Login_view.php");
require_once('Mustache/Mustache/stache_engine.php');
require_once('_sql.php');

//this is for determining which days are in week X so we can query
date_default_timezone_set('America/Toronto');
$date = date('Y/m/d');
$week = date('W', strtotime($date));
$year = date('Y', strtotime($date));
$weekBeginning = date('Y/m/d', strtotime($year."W".$week."1"));
$weekEnding =  date('Y/m/d', strtotime($year."W".$week."5"));

//supervisor or user
if($_SESSION['Supervisor'] == 1):
	$mdata['admin'] = 1;
	$mdata['listofemployees'] = getOnShiftEmployees(null, "and Date BETWEEN '{$weekBeginning}' and '{$weekEnding}'");
else:
	$mdata['admin'] = 0;
	$mdata['listofemployees'] = getOnShiftEmployees($_SESSION['EMP_ID'], "and Date BETWEEN '{$weekBeginning}' and '{$weekEnding}'");
endif;

$date2 = new DateTime(date('y:m:d'));
$week = $date2->format('W');

if($_SESSION['Supervisor'] != 1):
	$mdata['numberOfSwaps'] = numberOfSwaps($_SESSION['EMP_ID']);
endif;

if(isset($mdata['numberOfSwaps']) && $mdata['numberOfSwaps'] > 0) $mdata['hasValidSwaps'] = true;

$mdata['howManyOnShift'] = howManyOnShift($date);
$mdata['requiredOnShift'] = getRequiredShifts($date);
$mdata['current_week'] = $week;
$mdata['user'] = $_SESSION['User'];
$mdata['js'] = //javascript files to be loaded
	array('https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js',
	      'https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js',
		  	'js_template_loader.js', //this must preceed jstuff.js
	      'jstuff.js',
	      'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js',
		  	'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js',
		  	'https://cdnjs.cloudflare.com/ajax/libs/mustache.js/2.1.3/mustache.min.js' //even though mustache is loaded in PHP
	     );
$mdata['css'] = //css files to be loaded
	array('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css',
	      'stylz.css',
	      'https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css',
	      'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css'
	     );

//echo "<pre>"; print_r($_SESSION); echo "</pre>";
echo $stache->render('main_page', $mdata);
?>
