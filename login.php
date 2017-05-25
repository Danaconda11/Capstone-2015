<?PHP
ob_start();
session_start();
require_once("_sql.php");

//helps protect against sql injections
$legalCharactersID = "/^[0-9]+?/";
$legalCharactersPass = "/^[a-zA-Z0-9]+?/";
//echo "<pre>"; print_r($_POST); echo "</pre>";

if($_SERVER['REQUEST_METHOD'] == 'GET'):
	$_SESSION['access'] = 'nodata';
	header("location:Login_view.php");
	exit;
endif;

if(	$_SERVER['REQUEST_METHOD'] == 'POST' &&
	preg_match($legalCharactersID, $_POST['empID']) == 1 &&
    preg_match($legalCharactersPass, $_POST['password']) == 1 ):
	global $PDOobj;

	$the_id = $_POST['empID'];
	$the_pass = $_POST['password'];

	$holder = $PDOobj->prepare("
	SELECT *
	FROM Users
	WHERE EMP_ID = :id
	AND PASSWORD = :pass
	");

	$holder->bindvalue(":id", $the_id, PDO::PARAM_INT);
	$holder->bindvalue(":pass", $the_pass, PDO::PARAM_STR);
	$holder->execute();
	$result = $holder->fetch(PDO::FETCH_ASSOC);

	//if an admin record was found, then grant access
	if($holder->rowCount() > 0):
		$_SESSION['User'] = $result['EMP_NAME'];
		$_SESSION['EMP_ID'] = $result['EMP_ID'];
		//Admin or not..
		$_SESSION['Supervisor'] = $result['SUPERVISOR'] == 1 ? true : false;
		header("location:main_page.php");
	else:
		$_SESSION['access'] = 'denied';
		header("location:Login_view.php");
	endif;

	else:
		$_SESSION['access'] = 'denied';
		header("location:Login_view.php");
endif;



?>
