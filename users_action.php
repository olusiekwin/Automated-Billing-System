<?php
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if(!empty($_POST['action']) && $_POST['action'] == 'listUsers') {
	$user->listUsers();
}

if(!empty($_POST['action']) && $_POST['action'] == 'getUser') {
	$user->id = $_POST["id"];
	$user->getUser();
}

if(!empty($_POST['action']) && $_POST['action'] == 'addUser') {
	$user->user_first_name = $_POST["first_name"];	
	$user->user_last_name = $_POST["last_name"];
	$user->user_email = $_POST["email"];
	$user->user_gender = $_POST["gender"];
	$user->user_password = $_POST["password"];
	$user->user_mobile = $_POST["mobile"];
	$user->user_address = $_POST["address"];
	$user->user_role = $_POST["role"];
	$user->insert();
}

if(!empty($_POST['action']) && $_POST['action'] == 'getUserDetails') {
	$user->user_id = $_POST["user_id"];
	$user->getUserDetails();
}

if(!empty($_POST['action']) && $_POST['action'] == 'updateUser') {
	$user->user_id = $_POST["id"];
	$user->user_first_name = $_POST["first_name"];	
	$user->user_last_name = $_POST["last_name"];
	$user->user_email = $_POST["email"];
	$user->user_gender = $_POST["gender"];
	$user->user_password = $_POST["password"];
	$user->user_mobile = $_POST["mobile"];
	$user->user_address = $_POST["address"];
	$user->user_role = $_POST["role"];
	$user->update();
}

if(!empty($_POST['action']) && $_POST['action'] == 'deleteUser') {
	$user->user_id = $_POST["id"];
	$user->delete();
}

?>