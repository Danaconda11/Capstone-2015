<?php
session_start();
require_once("_sql.php");
//to render error page
require_once('Mustache/Mustache/stache_engine.php');

date_default_timezone_set('America/Toronto');

/*

 █████╗      ██╗ █████╗ ██╗  ██╗    ██████╗ ███████╗ ██████╗ ██╗   ██╗███████╗███████╗████████╗███████╗
██╔══██╗     ██║██╔══██╗╚██╗██╔╝    ██╔══██╗██╔════╝██╔═══██╗██║   ██║██╔════╝██╔════╝╚══██╔══╝██╔════╝
███████║     ██║███████║ ╚███╔╝     ██████╔╝█████╗  ██║   ██║██║   ██║█████╗  ███████╗   ██║   ███████╗
██╔══██║██   ██║██╔══██║ ██╔██╗     ██╔══██╗██╔══╝  ██║▄▄ ██║██║   ██║██╔══╝  ╚════██║   ██║   ╚════██║
██║  ██║╚█████╔╝██║  ██║██╔╝ ██╗    ██║  ██║███████╗╚██████╔╝╚██████╔╝███████╗███████║   ██║   ███████║
╚═╝  ╚═╝ ╚════╝ ╚═╝  ╚═╝╚═╝  ╚═╝    ╚═╝  ╚═╝╚══════╝ ╚══▀▀═╝  ╚═════╝ ╚══════╝╚══════╝   ╚═╝   ╚══════╝


*/

if (isset($_REQUEST['oldPass'])) {
	$return_data = oldPass($_REQUEST['oldPass'], $_SESSION['EMP_ID']);
	echo json_encode($return_data);
}

if (isset($_REQUEST['getSwapForFinalize'])) {
	$return_data = getSwaps($_REQUEST['getSwapForFinalize']);
	echo json_encode($return_data);
}

if (isset($_REQUEST['acceptSwap'])) {
	# code...
	$isAdmin = $_SESSION['Supervisor'] == 1 ? true : null;
	$return_data = getSwaps($_REQUEST['acceptSwap']);
	echo json_encode($return_data);
}

if(isset($_REQUEST['getSwapRecords'])):
	$TPL = array();
	$counter = 0;
	$isAdmin = "";

	if ($_SESSION['Supervisor'] == 1) {
		$isAdmin = true;
		$return_data = getSwapRecords($isAdmin);
	} else {
		$return_data = getSwapRecords(null,$_SESSION['EMP_ID']);
	}

	foreach ($return_data as $row) {
		if ($_SESSION['EMP_ID'] == $row['Initiator_ID']) {
			# code...
			$TPL[$counter]['myShift'] = $row['Initiator_Shift_ID'];
			$TPL[$counter]['myDate'] = $row['Initiator_Date'];
			$TPL[$counter]['myName'] = $row['Initiator_Name'];
			$TPL[$counter]['myShiftType'] = $row['Initiator_Shift_Type'];

			$TPL[$counter]['theirShift'] = $row['Receiver_Shift_ID'];
			$TPL[$counter]['theirName'] = $row['Receiver_Name'];
			$TPL[$counter]['theirDate'] = $row['Receiver_Date'];
			$TPL[$counter]['theirShiftType'] = $row['Receiver_Shift_Type'];

			$TPL[$counter]['isInitiator'] = true;
		}
		if($_SESSION['EMP_ID'] == $row['Receiver_ID']) {
			# code...
			$TPL[$counter]['myShift'] = $row['Receiver_Shift_ID'];
			$TPL[$counter]['myDate'] = $row['Receiver_Date'];
			$TPL[$counter]['myName'] = $row['Receiver_Name'];
			$TPL[$counter]['myShiftType'] = $row['Receiver_Shift_Type'];

			$TPL[$counter]['theirShift'] = $row['Initiator_Shift_ID'];
			$TPL[$counter]['theirName'] = $row['Initiator_Name'];
			$TPL[$counter]['theirDate'] = $row['Initiator_Date'];
			$TPL[$counter]['theirShiftType'] = $row['Initiator_Shift_Type'];

			$TPL[$counter]['isReceiver'] = true;
		}
		 if($isAdmin){
			# code...
			$TPL[$counter]['myName'] = $row['Initiator_Name'];
			$TPL[$counter]['myShift'] = $row['Initiator_Shift_ID'];
			$TPL[$counter]['myDate'] = $row['Initiator_Date'];
			$TPL[$counter]['myShiftType'] = $row['Initiator_Shift_Type'];

			$TPL[$counter]['theirShift'] = $row['Receiver_Shift_ID'];
			$TPL[$counter]['theirName'] = $row['Receiver_Name'];
			$TPL[$counter]['theirDate'] = $row['Receiver_Date'];
			$TPL[$counter]['theirShiftType'] = $row['Receiver_Shift_Type'];

			$TPL[$counter]['isAdmin'] = $isAdmin;
		}

		$TPL[$counter]['Status'] = $row['Swap_Status'];
		$TPL[$counter]['Swap_PK'] = $row['SR_PK'];

		$counter++;
	}

	echo json_encode($TPL);
endif;

//get possible swaps
if(isset($_REQUEST['possibleSwaps'])):
	$return_data = getPossibleSwaps($_REQUEST['initiator_shift_id'], $_REQUEST['date'], $_REQUEST['Shift_Type'], $_REQUEST['Emp_ID']);
	echo json_encode($return_data);
endif;

//get user for swap
if(isset($_REQUEST['startSwap'])):
	$date = date('Y/m/d');
	$return_data = getUserStartSwap($_SESSION['EMP_ID'], $date);
	echo json_encode($return_data);
endif;

//get admin status
if(isset($_REQUEST['getAdmin'])):
	$return_data = $_SESSION['Supervisor'];
	echo json_encode($return_data);
endif;

//ajax call to get the list of positions which will populate the drop down
if(isset($_REQUEST['listof_positions'])):
	$return_data = listOfPositions();
	echo json_encode($return_data);
endif;

if(isset($_REQUEST['load_contextual_emps'])):
    $return_data = getListOfEmployees();
	echo json_encode($return_data);
endif;

//request user whos shift is being updated through modal form
if(isset($_REQUEST['getUserForUpdate'])):
	$return_data = getUserForUpdate($_REQUEST['getUserForUpdate']);
	echo json_encode($return_data);
endif;

//get the number of employees registered
if(isset($_REQUEST['empCount'])):
	$return_data = getListOfEmployees();
	echo json_encode($return_data);
endif;

//get all of the pending swaps
if(isset($_REQUEST['swaps'])):
	if($_SESSION['Supervisor'] == 1):
		$return = getSwapRecords(true);
	else:
		$return = getSwapRecords();
	endif;
	echo json_encode($return);
endif;

//loading time specific shift data
if(isset($_REQUEST['context'])):
	//this is for determining which days are in week X so I can query
	date_default_timezone_set('America/Toronto');
	$date = date('Y/m/d');
	$week = date('W', strtotime($date));
	$year = date('Y', strtotime($date));
	$weekBeginning = date('Y/m/d', strtotime($year."W".$week."1"));
	$weekEnding =  date('Y/m/d', strtotime($year."W".$week."5"));

	$return_data;

	switch($_REQUEST['context']):
		case "this_week":
			if($_SESSION['Supervisor'] == 1):
				$return_data = getOnShiftEmployees(null, "and Date BETWEEN '{$weekBeginning}' and '{$weekEnding}'");
			else:
				$return_data = getOnShiftEmployees($_SESSION['EMP_ID'], "and Date BETWEEN '{$weekBeginning}' and '{$weekEnding}'");
			endif;
		break;

		case "past_shifts":
			if($_SESSION['Supervisor'] == 1):
				$return_data = getOnShiftEmployees(null, "and Date < '{$weekBeginning}'");
			else:
				$return_data = getOnShiftEmployees($_SESSION['EMP_ID'], "and Date < '{$weekBeginning}'");
			endif;
		break;

		case "future_shifts":
			if($_SESSION['Supervisor'] == 1):
				$return_data = getOnShiftEmployees(null, "and Date > '{$weekEnding}'");
			else:
				$return_data = getOnShiftEmployees($_SESSION['EMP_ID'], "and Date > '{$weekEnding}'");
			endif;
		break;
	endswitch;

	echo json_encode($return_data);

endif;

/*
██████╗  ██████╗ ███████╗████████╗
██╔══██╗██╔═══██╗██╔════╝╚══██╔══╝
██████╔╝██║   ██║███████╗   ██║
██╔═══╝ ██║   ██║╚════██║   ██║
██║     ╚██████╔╝███████║   ██║
╚═╝      ╚═════╝ ╚══════╝   ╚═╝
*/


if($_SERVER['REQUEST_METHOD'] == 'POST'):

	//password change validation
	if (isset($_POST['new_pass_'])) {
		if (oldPass($_POST['old_pass'], $_SESSION['EMP_ID']) == false) {
			$TPL['success'] = false;
			$TPL['message'] = "Old password was incorrect.";
			echo $stache->render('output_page', $TPL);

		}else{

			if(newPass($_POST['new_pass'], $_SESSION['EMP_ID']) ==  false){
				$TPL['success'] = false;
				$TPL['message'] = "No special characters allowed in password.";
				echo $stache->render('output_page', $TPL);
			}else{
				$TPL['success'] = true;
				$TPL['message'] = "Password has been updated.";
				echo $stache->render('output_page', $TPL);
			};
		}
	}

	if (isset($_POST['swap_finalize_input'])) {
		# code...
		if ($_POST['select_finalize'] == 1) {
			if (isExpired($_POST['Initiator_Shift_ID']) || isExpired($_POST['Receiver_Shift_ID'])) {

				if(adminShiftApproval('Expired', $_POST['Swap_ID'])){

				$TPL['success'] = false;
				$TPL['message'] = "This offer has expired.";
				echo $stache->render('output_page', $TPL);

				}
			}else{
				if(adminShiftApproval('Approved', $_POST['Swap_ID'])){
				$TPL['success'] = true;
				$TPL['message'] = "You have APPROVED the swap request.";
				echo $stache->render('output_page', $TPL);
				}
			}
		} else {
			adminShiftApproval('Denied', $_POST['Swap_ID']);
			$TPL['success'] = true;
			$TPL['message'] = "You have DENIED the swap request.";
			echo $stache->render('output_page', $TPL);
		}

		updateSwappedShiftsStatus(0,$_POST['Initiator_Shift_ID']);
		updateSwappedShiftsStatus(0,$_POST['Receiver_Shift_ID']);

	}

	//accepting a swap request
	if (isset($_POST['swap_accept_input'])) {

		$swapping = getSwaps($_POST['Swap_ID']);

		if ($_POST['select_acceptance'] == 1) {
			if (acceptShift($_POST['Swap_ID'])) {
				$TPL['success'] = true;
				$TPL['message'] = "You have accepted the swap request. Now pending supervisor approval..";
				echo $stache->render('output_page', $TPL);
			}
		}else{
			if (declineShift($_POST['Swap_ID'])) {

				updateSwappedShiftsStatus(0,$_POST['Initiator_Shift_ID']);
				updateSwappedShiftsStatus(0,$_POST['Receiver_Shift_ID']);

				$TPL['success'] = true;
				$TPL['message'] = "You have declined the swap request.";
				echo $stache->render('output_page', $TPL);
			}
		}
	}

	//sending a swap request
	if(isset($_POST['shift_swap_request'])):
		$TPL = array();
		$last_inserted_id = "";
		if($last_inserted_id = initiateSwap()):

			updateSwappedShiftsStatus($last_inserted_id, $_POST['initiator_select']);
			updateSwappedShiftsStatus($last_inserted_id, $_POST['receiver_select']);

			$TPL['success'] = true;
			$TPL['message'] = "Your swap request has been sent!";
			echo $stache->render('output_page', $TPL);
		else:
			echo "There was a problem with our servers, sorry! Please try again.";
		endif;
	endif;

	//ADDING A DEPARTMENT
	if(isset($_POST['form_add_department']) && $_POST['form_add_department'] == 1):
		$TPL = array();
		if(addDepartment($_POST['department_name'])):
			$TPL['success'] = true;
			$TPL['message'] = "You have successfully created the department: " . $_POST['department_name'];
			echo $stache->render('output_page', $TPL);
		else:
			$TPL['success'] = false;
			$TPL['message'] = "The department '{$_POST['department_name']}' already exists";
			echo $stache->render('output_page', $TPL);
		endif;
	endif;

	//REQUIRED ON SHIFT
	if(isset($_POST['required_on_shift'])):

		$theDate = date('Y/m/d');

		foreach($_POST as $key => $value):
			switch($key):
				case "number_required_monday":
					$theDate = date('Y/m/d', strtotime($_POST['number_required_year']."W".$_POST['number_required_week']."1"));
					requiredOnShift($theDate, $value);
				break;

				case "number_required_tuesday":
					$theDate = date('Y/m/d', strtotime($_POST['number_required_year']."W".$_POST['number_required_week']."2"));
					requiredOnShift($theDate, $value);
				break;

				case "number_required_wednesday":
					$theDate = date('Y/m/d', strtotime($_POST['number_required_year']."W".$_POST['number_required_week']."3"));
					requiredOnShift($theDate, $value);
				break;

				case "number_required_thursday":
					$theDate = date('Y/m/d', strtotime($_POST['number_required_year']."W".$_POST['number_required_week']."4"));
					requiredOnShift($theDate, $value);
				break;

				case "number_required_friday":
					$theDate = date('Y/m/d', strtotime($_POST['number_required_year']."W".$_POST['number_required_week']."5"));
					requiredOnShift($theDate, $value);
				break;
			endswitch;
		endforeach;

		$TPL['success'] = true;
		$TPL['message'] = "The labor required has been updated.";
		echo $stache->render('output_page', $TPL);
	endif;

	//UPDATING A SHIFT STATUS
	if(isset($_POST['form_update_status']) && $_POST['form_update_status'] == 1):
		$TPL = array();
		try
		{
			if($_POST['status'] == ""):
				throw new Exception("");
			endif;

			if(updateShiftStatus($_POST['status'], $_SESSION['EMP_ID'], $_POST['shiftid'])):
				$TPL['success'] = true;
				$TPL['message'] = "Shift number {$_POST['shiftid']} has been updated to a status of {$_POST['status']}";
				echo $stache->render('output_page', $TPL);
			endif;

		}
		catch(Exception $e)
		{
			$TPL['success'] = false;
			$TPL['message'] = "Please select an option from the drop down menu";
			echo $stache->render('output_page', $TPL);
		}
	endif;


	//CREATING EMPLOYEE
	if(isset($_POST['form_create_emp']) && $_POST['form_create_emp'] == 1):

		/*VALIDATION*/

		if(createEmployee()){
			$TPL['success'] = true;
			$TPL['message'] = "Emloyee created: {$_POST['create_name']}";
			echo $stache->render('output_page', $TPL);
		}else{
			$TPL['success'] = false;
			$TPL['message'] = "Emloyee {$_POST['create_name']} could not be created. Be sure to user numers and/or letters only when filling in the fields.";
			echo $stache->render('output_page', $TPL);
		};

	endif;

	//assign employee shift form
	if(isset($_POST['form_shift']) && $_POST['form_shift'] == 1):
		global $PDOobj;

		date_default_timezone_set('America/Toronto');

		try
		{
			$currentDate = date('Y/m/d');
			$theDate = date('Y/m/d');
			$TPL = array();

			if(!isset($_POST['weekday'])):
				throw new Exception('select');
			endif;

			foreach($_POST['weekday'] as $key => $day):
				$theDate = date('Y/m/d', strtotime($_POST['year']."W".$_POST['week'].(string)$day['value']))."\n";

				//if user tried to book a date that is in the past..
				if($currentDate > $theDate):
					throw new Exception('date');
				endif;

				if(isset($_POST['vacation'])){
					if (bookVacation($theDate)) {
						$TPL['success'] = true;
						$TPL['message'] = "You have successfully booked vacation time for emloyee: {$_POST['name']} on {$theDate}";
						echo $stache->render('output_page', $TPL);
					} else {
						$TPL['success'] = false;
						$TPL['message'] = "Sorry, there was an error. Please try again.";
						echo $stache->render('output_page', $TPL);
					}

				}else{

					if(isset($day['shift_type']) == false){
						throw new Exception("shift");
					}

					if(createShift($theDate, $day['shift_type'])):
						$TPL['success'] = true;
						$TPL['message'] = "You have successfully added emloyee: {$_POST['name']} on {$theDate}";
						echo $stache->render('output_page', $TPL);
					else:
						$TPL['success'] = false;
						$TPL['message'] = "The Employee ID:{$_POST['name']} is already scheduled for $theDate.";
						echo $stache->render('output_page', $TPL);
					endif;

				}
			endforeach;

		}
		catch(Exception $e)
		{
			if($e->getMessage() == "select"){
				$TPL['success'] = false;
				$TPL['message'] = "Please select a shift and a shift type.";
				echo $stache->render('output_page', $TPL);
			}
			if($e->getMessage() == "shift"){
				$TPL['success'] = false;
				$TPL['message'] = "All shifts must have a shift type.";
				echo $stache->render('output_page', $TPL);
			}
			if($e->getMessage() == "date"){
				$TPL['success'] = false;
				$TPL['message'] = "The shift you are trying to schedule is for a past calendar date.";
				echo $stache->render('output_page', $TPL);
			}
		}

	endif;
	//echo "<pre>"; print_r($_POST); echo "</pre>";
endif;
?>
