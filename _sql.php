<?php
// ini_set('display_errors', 'On');
// error_reporting(E_ALL)
/*

███████╗ ██████╗ ██╗
██╔════╝██╔═══██╗██║
███████╗██║   ██║██║
╚════██║██║▄▄ ██║██║
███████║╚██████╔╝███████╗
╚══════╝ ╚══▀▀═╝ ╚══════╝

*/
$host = 'xxx';
$user = '*Mid*';
$pass = '00000000';
$dbname = '*Mid*';

$PDOobj = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);
$PDOobj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$PDOobj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


function newPass($pass, $id)
{

  global $PDOobj;
  try{

    if (preg_match('/^[a-zA-Z0-9]+$/', $pass) == false) {
      # code...
      return false;
    }

    $holder = $PDOobj->prepare("UPDATE `Users` SET `PASSWORD` = '{$pass}' WHERE `EMP_ID` = {$id}");
    $holder->execute();
    return true;

  }catch(PDOException $e){
    $e->getMessage();
    die();
    return false;
  }
}

function oldPass($pass, $id)
{
  global $PDOobj;
  try{
    $holder = $PDOobj->prepare("SELECT * FROM `Users` WHERE `PASSWORD` = '{$pass}' AND `EMP_ID` = {$id}");
    $holder->execute();
    $return = $holder->rowCount();

    if ($return > 0) {
      # code...
      return true;
    } else {
      # code...
      return false;
    }
  }catch(PDOException $e){
    return false;
  }
}

function getShift($id)
{
    global $PDOobj;
    $holder = $PDOobj->prepare("SELECT * FROM `Shifts` WHERE `Shift_ID` = {$id}");
    $holder->execute();
    $return_data = $holder->fetchAll();
    return $return_data;
}

function acceptShift($id)
{
  global $PDOobj;
  try{
      $holder = $PDOobj->prepare("UPDATE Swap_Requests SET Status = 'Accepted' WHERE SR_PK = {$id}");
      $holder->execute();
      return true;
  }catch(PDOException $e){
    $mes = $e->getMessage();
    return $mes;
  }
}

function declineShift($id)
{
  global $PDOobj;
  try{
      $holder = $PDOobj->prepare("UPDATE Swap_Requests SET Status = 'Declined' WHERE SR_PK = {$id}");
      $holder->execute();
      return true;
  }catch(PDOException $e){
    $mes = $e->getMessage();
    return $mes;
  }
}

function isExpired($id) {
  global $PDOobj;
  try{
      $holder = $PDOobj->prepare("SELECT * FROM `Shifts` WHERE `Shift_ID` = {$id} AND `Date` < CURDATE()");
      $holder->execute();

      if ($holder->rowCount() > 0) {
        return true;
      } else {
        return false;
      }
  }catch(PDOException $e){
    $mes = $e->getMessage();
    return $mes;
  }
}

function adminShiftApproval($approved, $id)
{
  global $PDOobj;
  try{
      $holder = $PDOobj->prepare("UPDATE `Swap_Requests` SET `Status` = '{$approved}' WHERE `SR_PK` = {$id}");
      $holder->execute();
      return true;
  }catch(PDOException $e){
    $mes = $e->getMessage();
    return $mes;
  }
}

function exchangeShifts($id1, $id2)
{
//http://www.microshell.com/database/sql/swap-values-in-2-rows-sql/
  global $PDOobj;
  try{
      $holder = $PDOobj->prepare("UPDATE Shifts as ShiftsA
        JOIN Shifts as ShiftsB
        ON (ShiftsA.Shift_ID = {$id1} AND ShiftsB.Shift_ID = {$id2})
        OR (ShiftsB.Shift_ID = {$id2} AND ShiftsB.Shift_ID = {$id1})
        SET
          ShiftsA.Shift_ID = ShiftsB.Shift_ID,
          ShiftsB.Shift_ID = ShiftsA.Shift_ID
        ");
      $holder->execute();
      return true;
  }catch(PDOException $e){
    $mes = $e->getMessage();
    return $mes;
  }
}

function getSwapRecords($isAdmin = null, $id_ = null)
{
  global $PDOobj;
  $holder = null;

    try{
        if ($isAdmin) {
          $holder = $PDOobj->prepare("SELECT
            `SR_PK`,

            `Initiator_FK` as `Initiator_ID`,
            UA.EMP_NAME as `Initiator_Name`,
            ShiftsA.Date as `Initiator_Date`,
            ShiftsA.Shift_Type as `Initiator_Shift_Type`,
            ShiftsA.Shift_ID as `Initiator_Shift_ID`,

            `Receiver_FK` as 'Receiver_ID',
            UB.EMP_NAME as `Receiver_Name`,
            ShiftsB.Date as `Receiver_Date`,
            ShiftsB.Shift_Type as `Receiver_Shift_Type`,
            ShiftsB.Shift_ID as `Receiver_Shift_ID`,

            Swap_Requests.Status as `Swap_Status`

            FROM `Swap_Requests`
            JOIN `Shifts` as `ShiftsA`
            ON ShiftsA.Shift_ID = Swap_Requests.Initiator_Shift_PK
            JOIN `Shifts` as `ShiftsB`
            ON ShiftsB.Shift_ID = Swap_Requests.Receiver_Shift_PK
            JOIN `Users` as `UA`
            ON Swap_Requests.Initiator_FK = UA.EMP_ID
            JOIN `Users` as `UB`
            ON Swap_Requests.Receiver_FK = UB.EMP_ID
            WHERE UA.EMP_ID IS NOT NULL OR UB.EMP_ID IS NOT NULL
            "
          );
        }else{
          $holder = $PDOobj->prepare("SELECT
            `SR_PK`,

            `Initiator_FK` as `Initiator_ID`,
            UA.EMP_NAME as `Initiator_Name`,
            ShiftsA.Date as `Initiator_Date`,
            ShiftsA.Shift_Type as `Initiator_Shift_Type`,
            ShiftsA.Shift_ID as `Initiator_Shift_ID`,

            `Receiver_FK` as 'Receiver_ID',
            UB.EMP_NAME as `Receiver_Name`,
            ShiftsB.Date as `Receiver_Date`,
            ShiftsB.Shift_Type as `Receiver_Shift_Type`,
            ShiftsB.Shift_ID as `Receiver_Shift_ID`,

            Swap_Requests.Status as `Swap_Status`

            FROM `Swap_Requests`
            JOIN `Shifts` as `ShiftsA`
            ON ShiftsA.Shift_ID = Swap_Requests.Initiator_Shift_PK
            JOIN `Shifts` as `ShiftsB`
            ON ShiftsB.Shift_ID = Swap_Requests.Receiver_Shift_PK
            JOIN `Users` as `UA`
            ON Swap_Requests.Initiator_FK = UA.EMP_ID
            JOIN `Users` as `UB`
            ON Swap_Requests.Receiver_FK = UB.EMP_ID
            WHERE UA.EMP_ID = {$id_}
            OR UB.EMP_ID = {$id_}"
          );
        }


      $holder->execute();
      $return_data = $holder->fetchAll();

      return $return_data;

    }catch(PDOException $e){
      $e->getMessage();
      return false;
    }
}

function numberOfSwaps($id_)
{
  global $PDOobj;
  try{
      $holder = $PDOobj->prepare("SELECT
        `SR_PK`,

        `Initiator_FK` as `Initiator_ID`,
        UA.EMP_NAME as `Initiator_Name`,
        ShiftsA.Date as `Initiator_Date`,
        ShiftsA.Shift_Type as `Initiator_Shift_Type`,
        ShiftsA.Shift_ID as `Initiator_Shift_ID`,

        `Receiver_FK` as 'Receiver_ID',
        UB.EMP_NAME as `Receiver_Name`,
        ShiftsB.Date as `Receiver_Date`,
        ShiftsB.Shift_Type as `Receiver_Shift_Type`,
        ShiftsB.Shift_ID as `Receiver_Shift_ID`,

        Swap_Requests.Status as `Swap_Status`

        FROM `Swap_Requests`
        JOIN `Shifts` as `ShiftsA`
        ON ShiftsA.Shift_ID = Swap_Requests.Initiator_Shift_PK
        JOIN `Shifts` as `ShiftsB`
        ON ShiftsB.Shift_ID = Swap_Requests.Receiver_Shift_PK
        JOIN `Users` as `UA`
        ON Swap_Requests.Initiator_FK = UA.EMP_ID
        JOIN `Users` as `UB`
        ON Swap_Requests.Receiver_FK = UB.EMP_ID
        WHERE (UA.EMP_ID = {$id_} OR UB.EMP_ID = {$id_})
        AND (Swap_Requests.Status = 'Sent'
          OR Swap_Requests.Status = 'Accepted'
          OR Swap_Requests.Status = 'Approved'
        )
        "
      );
      $holder->execute();
      $return = $holder->rowCount();
      return $return;
  }catch(PDOException $e){
      //echo $e->getMessage();
      return false;
  }

}

function getEmployee($id)
{
	global $PDOobj;
	$holder = $PDOobj->prepare("SELECT * FROM `Users` WHERE `EMP_ID` = {$id}");
	$holder->execute();
	$return_data = $holder->fetchAll();
	return $return_data;
}

function updateSwappedShiftsStatus($swapVal, $shiftid)
{
  global $PDOobj;
  try{
      $holder = $PDOobj->prepare("UPDATE Shifts SET Swap_FK = {$swapVal} WHERE Shift_ID = {$shiftid}");
      $holder->execute();
      return true;
  }catch(PDOException $e){
      //echo $e->getMessage();
      return false;
  }
}

function initiateSwap()
{
  global $PDOobj;
  try{
      $holder = $PDOobj->prepare("INSERT INTO Swap_Requests (`Initiator_FK`, `Receiver_FK`, `Initiator_Shift_PK`, `Receiver_Shift_PK`, `Status`)
      VALUES({$_POST['initiator_PK']}, {$_POST['receiver_PK']}, {$_POST['initiator_select']}, {$_POST['receiver_select']}, 'Sent'); ");
      $holder->execute();
      $return_data = $PDOobj->lastInsertId();
      return $return_data;
  }catch(PDOException $e){
    $mes = $e->getMessage();
    return $mes;
  }

}

function getPossibleSwaps($Shift_ID, $date_, $shift_type, $Emp_ID)
{

	global $PDOobj;

	try{

    $Shift_ID = (int)$Shift_ID;
		//This query was incredibly problematic when attempting to bind parameters with PDO. This is only solution I could find..
		$holder = $PDOobj->prepare("SELECT
			   `EMP_ID`,
			   `EMP_NAME`,
			   `EMP_POSITION`,
			   `REQUEST_SWAP`,
			   `Shift_ID`,
			   `Status`,
			   `Date`,
			   `Supervisor_FK`,
			   `Shift_Type` FROM `Shifts`
				JOIN `Users`
				ON `User_FK` = `EMP_ID`
				WHERE `Shift_ID` <> {$Shift_ID}
				AND `Date` > '{$date_}'
				AND `Shift_Type` <> '{$shift_type}'
				AND `EMP_ID` <> {$Emp_ID}
        AND `Swap_FK` = 0
        AND `Status` = 'P';"
				);
		$holder->execute();
		//print_r($holder);
		$return = $holder->fetchAll(PDO::FETCH_ASSOC);
		return $return;
	}catch(PDOException $e){
		$e->getMessage();
	}
}

function getUserStartSwap($emp, $date)
{
	global $PDOobj;
	try{
		$holder = $PDOobj->prepare("SELECT
			EMP_ID,
			EMP_NAME,
			EMP_POSITION,
			REQUEST_SWAP,
			Shift_ID,
			Status,
			Date,
			Supervisor_FK,
			Position_Name,
			Shift_Type
		FROM Shifts
		JOIN Users
		ON Shifts.User_FK = Users.EMP_ID
		JOIN Positions
		ON Positions.Position_ID = EMP_POSITION
		WHERE EMP_ID = :emp
		AND Date > :date
    AND Swap_FK = 0
    AND `Status` = 'P';
		");

		$holder->bindvalue(":emp", $emp, PDO::PARAM_INT);
		$holder->bindvalue(":date", $date, PDO::PARAM_INT);
		$holder->execute();
		$return = $holder->fetchAll(PDO::FETCH_ASSOC);
		return $return;

	}catch(PDOException $e){
		$e->getMessage();
	}
}

function getSwaps($swapid = null)
{
	global $PDOobj;
	try{
    if($swapid){
      $holder = $PDOobj->prepare("SELECT * from `Swap_Requests` WHERE SR_PK = {$swapid}");
    }else{
      $holder = $PDOobj->prepare("SELECT * from `Swap_Requests`");
    }

		$holder->execute();
		$return = $holder->fetchAll(PDO::FETCH_ASSOC);
		return $return;

	}catch(PDOException $e){
		$e->getMessage();
	}
}

function getRequiredShifts($date)
{
	global $PDOobj;
	try{
		$holder = $PDOobj->prepare("SELECT * from `Required_On_Shift` WHERE `Date` = :date");
		$holder->bindvalue(":date", $date);
		$holder->execute();
		$return = $holder->fetchAll(PDO::FETCH_ASSOC);
		return $return;

	}catch(PDOException $e){
		$e->getMessage();
	}
}

function requiredOnShift($date, $amount)
{
	global $PDOobj;
	try{
		$holder = $PDOobj->prepare("INSERT INTO Required_On_Shift (`Date`,`Amount`)
          VALUES(:date, :amount)
          ON DUPLICATE KEY UPDATE `Amount` = :amount"
        );
		$holder->bindvalue(":date", $date);
		$holder->bindvalue(":amount", $amount, PDO::PARAM_INT);
		$holder->execute();

	}catch(PDOException $e){
		$e->getMessage();
	}
}

function howManyOnShift($date)
{
	global $PDOobj;
	try{
		$holder = $PDOobj->prepare("SELECT COUNT(DISTINCT `User_Fk`) as Total FROM `Shifts` WHERE Date = :date ");
		$holder->bindvalue(":date", $date);
		$holder->execute();
		$return = $holder->fetchAll(PDO::FETCH_ASSOC);
		return $return;

	}catch(PDOException $e){
		$e->getMessage();
	}
}

function updateShiftStatus($status_, $super_, $shift_)
{
	global $PDOobj;
	try
	{
		$holder = $PDOobj->prepare("UPDATE Shifts SET `Status` = :status_, `Supervisor_FK` = :super_
									WHERE `Shift_ID` = :shift_");
		$holder->bindvalue(":status_", $status_, PDO::PARAM_STR);
		$holder->bindvalue(":super_", $super_, PDO::PARAM_INT);
		$holder->bindvalue(":shift_", $shift_);
		$holder->execute();
		return true;
	}
	catch(PDOException $e)
	{
		return false;
	}
}

function getUserForUpdate($SID)
{
	global $PDOobj;
	$holder = $PDOobj->prepare("Select * from Shifts join Users ON Shifts.User_FK = Users.EMP_ID where Shift_ID = :SID");
	$holder->bindvalue(":SID", $SID, PDO::PARAM_INT);
	$holder->execute();
	$return_data = $holder->fetchAll(PDO::FETCH_ASSOC);
	return $return_data;
}

function getListOfEmployees()
{
	global $PDOobj;
	$holder = $PDOobj->prepare("SELECT EMP_NAME, EMP_ID, EMP_PHONE, NOTES FROM Users where Supervisor <> true");
	$holder->execute();
	$return_data = $holder->fetchAll();
	return $return_data;
}

function bookVacation($theDate)
{
  try{
	global $PDOobj;
		$holder = $PDOobj->prepare("
						INSERT INTO Shifts(`User_FK`, `Supervisor_FK`, `Date` ,`Status`, `Shift_type`)
						VALUES(:User_FK, :Supervisor_FK, :Date ,'V', 'None')");
		$holder->bindvalue(":User_FK", $_POST['name'], PDO::PARAM_INT);
		$holder->bindvalue(":Supervisor_FK", $_SESSION['EMP_ID'], PDO::PARAM_INT);
		$holder->bindvalue(":Date", $theDate);
		$holder->execute();
		return true;
	}
	catch(PDOException $Exception)
	{
		//constraint integrity violation aka trying to insert a duplicate record
		if($holder->errorCode() == '23000' ):
			return false;
		endif;
		exit();
	}
}

function createShift($theDate, $shift_type)
{

	try{
	global $PDOobj;
		$holder = $PDOobj->prepare("
						INSERT INTO Shifts(`User_FK`, `Supervisor_FK`, `Date` ,`Status`, `Shift_type`)
						VALUES(:User_FK, :Supervisor_FK, :Date ,'P', :shift_type)");
		$holder->bindvalue(":User_FK", $_POST['name'], PDO::PARAM_INT);
		$holder->bindvalue(":Supervisor_FK", $_SESSION['EMP_ID'], PDO::PARAM_INT);
		$holder->bindvalue(":shift_type", $shift_type, PDO::PARAM_STR);
		$holder->bindvalue(":Date", $theDate);
		$holder->execute();
		return true;
	}
	catch(PDOException $Exception)
	{
		//constraint integrity violation aka trying to insert a duplicate record
		if($holder->errorCode() == '23000' ):
			return false;
		endif;
		exit();
	}
}

function listOfPositions()
{
	global $PDOobj;
	$holder = $PDOobj->prepare("SELECT * FROM Positions");
	$holder->execute();
	$return_data = $holder->fetchAll();
	return $return_data;
}

function addDepartment($dept)
{
	global $PDOobj;

	try
	{
		$holder = $PDOobj->prepare("INSERT INTO `Positions` (`Position_ID`, `Position_Name`) VALUES(NULL, :deptName)");
		$holder->bindvalue(':deptName', $dept, PDO::PARAM_STR);
		$holder->execute();
		return true;
	}
	catch(PDOException $Exception)
	{
		//integrity constraint violation
		if($holder->errorCode() == '23000' ):
			return false;
		endif;
	}
}

function createEmployee()
{
	global $PDOobj;
	try{

    if(preg_match('/^[0-9]+$/', $_POST['phone']) ==  false) throw new Exception();
    if(preg_match('/^[a-zA-Z0-9]+$/', $_POST['create_name']) ==  false) throw new Exception();

		$holder = $PDOobj->prepare("INSERT INTO `Users`(`EMP_NAME`, `EMP_POSITION`, `EMP_PHONE`, `SUPERVISOR`, `PASSWORD`, `REQUEST_SWAP`, NOTES) VALUES (:name,:position,:phone,:supervisor,:password,0, :notes)");
		$holder->bindvalue(":name", $_POST['create_name'], PDO::PARAM_STR);
		$holder->bindvalue(":position", $_POST['position']);
    $holder->bindvalue(":notes", $_POST['notes']);
		$holder->bindvalue(":phone", $_POST['phone'], PDO::PARAM_STR);
		$holder->bindvalue(":supervisor", $_POST['supervisor']);
		$holder->bindvalue(":password", $_POST['create_password'], PDO::PARAM_STR);
		$holder->execute();
    return true;
		}catch(PDOException $Exception){
      return false;
		}catch(Exception $e){
      return false;
    }
}

function getOnShiftEmployees($isEmp = null, $where = null)
{
global $PDOobj;
//default action is to get all records
if($isEmp != true):

	try{
		$holder = $PDOobj->prepare("
		SELECT
			EMP_ID,
			EMP_NAME,
			EMP_POSITION,
			REQUEST_SWAP,
			Shift_ID,
			Status,
			Date,
			Supervisor_FK,
			Position_Name,
			Shift_Type
		FROM Shifts
		JOIN Users
		ON Shifts.User_FK = Users.EMP_ID
		JOIN Positions
		ON Positions.Position_ID = EMP_POSITION
		WHERE EMP_ID IS NOT NULL
		{$where}");
		$holder->execute();
		$return_data = $holder->fetchAll(PDO::FETCH_ASSOC);
		return $return_data;

	}catch(PDOException $e){
		$e->getMessage();
	}

endif;

//if argument specified, then its an non-admin employee
if($isEmp):

	try{
	$holder = $PDOobj->prepare("
		SELECT
			EMP_ID,
			EMP_NAME,
			EMP_POSITION,
			REQUEST_SWAP,
			Shift_ID,
			Status,
			Date,
			Supervisor_FK,
			Position_Name,
			Shift_Type
		FROM Shifts
		JOIN Users
		ON Shifts.User_FK = Users.EMP_ID
		JOIN Positions
		ON Positions.Position_ID = EMP_POSITION
		WHERE EMP_ID = '{$isEmp}'
		{$where}");
		$holder->execute();
		$return_data = $holder->fetchAll(PDO::FETCH_ASSOC);
		return $return_data;
		}
		catch(PDOException $e){
			$e->getMessage();
		}
endif;

}


?>
