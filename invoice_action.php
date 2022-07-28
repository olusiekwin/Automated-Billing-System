<?php
include_once 'config/Database.php';
include_once 'class/Invoice.php';

$database = new Database();
$db = $database->getConnection();

$invoice = new Invoice($db);

if(!empty($_POST['action']) && $_POST['action'] == 'listOrder') {
	$invoice->listOrder();
}

if(!empty($_POST['action']) && $_POST['action'] == 'loadItemsList') {	
	$invoice->loadItemsList();
}

if(!empty($_POST['action']) && $_POST['action'] == 'loadItems') {	
	$invoice->loadItems();
}

if(!empty($_POST['action']) && $_POST['action'] == 'loadItemPrice') {
	$invoice->itemId = $_POST["itemId"];
	$invoice->loadItemPrice();
}

if(!empty($_POST['action']) && $_POST['action'] == 'getTaxRate') {	
	$invoice->getTaxRate();
}

if(!empty($_POST['action']) && $_POST['action'] == 'getOrderDetails') {
	$invoice->id = $_POST["id"];
	$invoice->getOrderDetails();
}

if(!empty($_POST['action']) && $_POST['action'] == 'addOrder') {		
	$invoice->items = $_POST["items"];
	$invoice->price = $_POST["price"];
	$invoice->quantity = $_POST["quantity"];
	$invoice->total = $_POST["total"];
	$invoice->status = $_POST["status"];
	$invoice->subTotal = $_POST["subTotal"];
	$invoice->taxAmount = $_POST["taxAmount"];
	$invoice->totalAftertax = $_POST["totalAftertax"];	
	$invoice->insert();
}

if(!empty($_POST['action']) && $_POST['action'] == 'updateOrder') {
	$invoice->id = $_POST["id"];	
	$invoice->items = $_POST["items"];
	$invoice->price = $_POST["price"];
	$invoice->quantity = $_POST["quantity"];
	$invoice->total = $_POST["total"];
	$invoice->status = $_POST["status"];
	$invoice->subTotal = $_POST["subTotal"];
	$invoice->taxAmount = $_POST["taxAmount"];
	$invoice->totalAftertax = $_POST["totalAftertax"];
	$invoice->itemIds = $_POST["itemIds"];
	$invoice->update();
}

if(!empty($_POST['action']) && $_POST['action'] == 'deleteOrder') {
	$invoice->id = $_POST["id"];
	$invoice->delete();
}

?>