<?php
include_once 'config/Database.php';
include_once 'class/Tax.php';

$database = new Database();
$db = $database->getConnection();

$tax = new Tax($db);

if(!empty($_POST['action']) && $_POST['action'] == 'listTaxes') {
	$tax->listTaxes();
}

if(!empty($_POST['action']) && $_POST['action'] == 'getTaxDetails') {
	$tax->id = $_POST["id"];
	$tax->getTaxDetails();
}

if(!empty($_POST['action']) && $_POST['action'] == 'addTax') {
	$tax->taxName = $_POST["taxName"];
	$tax->percentage = $_POST["percentage"];
	$tax->status = $_POST["status"];
	$tax->insert();
}

if(!empty($_POST['action']) && $_POST['action'] == 'updateTax') {
	$tax->id = $_POST["id"];
	$tax->taxName = $_POST["taxName"];
	$tax->percentage = $_POST["percentage"];
	$tax->status = $_POST["status"];	
	$tax->update();
}

if(!empty($_POST['action']) && $_POST['action'] == 'deleteTax') {
	$tax->id = $_POST["id"];
	$tax->delete();
}
?>