<?php
include_once 'config/Database.php';
include_once 'class/Item.php';

$database = new Database();
$db = $database->getConnection();

$item = new Item($db);

if(!empty($_POST['action']) && $_POST['action'] == 'listItems') {
	$item->listItems();
}

if(!empty($_POST['action']) && $_POST['action'] == 'getItemDetails') {
	$item->id = $_POST["id"];
	$item->getItemDetails();
}

if(!empty($_POST['action']) && $_POST['action'] == 'addItem') {
	$item->itemName = $_POST["itemName"];
	$item->price = $_POST["price"];	
	$item->status = $_POST["status"];
	$item->insert();
}

if(!empty($_POST['action']) && $_POST['action'] == 'updateItem') {
	$item->id = $_POST["id"];
	$item->itemName = $_POST["itemName"];
	$item->price = $_POST["price"];	
	$item->status = $_POST["status"];
	$item->update();
}

if(!empty($_POST['action']) && $_POST['action'] == 'deleteItem') {
	$item->id = $_POST["id"];
	$item->delete();
}
?>