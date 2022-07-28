<?php
include_once 'config/Database.php';
include_once 'class/User.php';
include_once 'class/Item.php';
include_once 'class/Invoice.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$item = new Item($db);
$invoice = new Invoice($db);

if(!$user->loggedIn()) {
	header("Location: index.php");
}

include('inc/header.php');

?>
<title>G-Billing</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/invoice.js"></script>	
<script src="js/general.js"></script>
<?php include('inc/container.php');?>
<div class="container"> 	
	<?php include('top_menus.php'); ?>	
	<div> 	
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-10">
					<h3 class="panel-title"></h3>
				</div>
				<div class="col-md-2" align="right">
					<button type="button" id="addOrder" class="btn btn-info" title="Add Order"><span class="glyphicon glyphicon-plus"></span></button>
				</div>
			</div>
		</div>
		<table id="orderListing" class="table table-bordered table-striped">
			<thead>
				<tr>						
					<th>Invoice No</th>					
					<th>Gross Amount</th>
					<th>Tax Amount</th>	
					<th>Net Amount</th>	
					<th>Date Time</th>	
					<th>Created By</th>
					<th>Status</th>					
					<th></th>
					<th></th>					
				</tr>
			</thead>
		</table>
	</div>
	
	<div id="orderModal" class="modal fade">
		<div class="modal-dialog" :style="width">
			<form method="post" id="orderForm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Edit Order</h4>
					</div>
					<div class="modal-body">						
						<div class="form-group">
							<div class="row">
																
								<table class="table table-bordered table-hover" id="orderItem">										
										
									<tr>
										<th width="2%"><input id="checkAll" class="formcontrol" type="checkbox"></th>									
										<th width="25%">Item</th>										
										<th width="15%">Price</th>
										<th width="10%">Quantity</th>										
										<th width="15%">Total</th>
									</tr>
									
									<tr>
										<td><input class="itemRow" type="checkbox"></td>										
										<td>
											<select name="items[]" id="items_1" class="form-control">
											
											<option value="">--Select--</option>
												<?php 
												$itemsResult = $item->getItems();
												while ($items = $itemsResult->fetch_assoc()) { 	
												?>
													<option value="<?php echo $items['id']; ?>"><?php echo $items['name']; ?></option>							
												<?php } ?>	
											
											</select>	
										</td>
										<td><input type="number" name="price[]" id="price_1" class="form-control price" autocomplete="off"></td>				
										<td><input type="number" name="quantity[]" id="quantity_1" class="form-control quantity" autocomplete="off"></td>
										<td><input type="number" name="total[]" id="total_1" class="form-control total" autocomplete="off"></td>
										<input type="hidden" name="itemIds[]" id="itemIds_1" class="form-control" >
									</tr>						
								</table>
							</div>
							<div class="row">								
								&nbsp;<button class="btn btn-danger delete" id="removeRows" type="button">- Delete</button>
								<button class="btn btn-success" id="addRows" type="button">+ Add More</button>								
							</div>
						</div>
						
						<div class="row">							
							<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">								
								<div class="form-group">
								    <br><br><br><br><br><br><br><br><br><br>
									<label class="col-md-2">Status <span class="text-danger">*</span></label>
									<div class="col-md-9">
										<select name="status" id="status" class="form-control" required>											
											<option value="Paid">Paid</option>
											<option value="In Process">In Process</option>
										</select>
									</div>	
									<br><br>
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />									
								</div>								
							</div>
							<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
								<span class="form-inline">
									<div class="form-group">
										<label>Subtotal: &nbsp;</label>
										<div class="input-group">
											<div class="input-group-addon currency">Ksh</div>
											<input value="" type="text" class="form-control" name="subTotal" id="subTotal" placeholder="Subtotal">
										</div>
									</div>
									<div class="form-group">
										<label>CGST Rate: &nbsp;</label>
										<div class="input-group">
											<input value="" type="text" class="form-control" name="taxRate1" id="taxRate1" placeholder="CGST Rate">
											<div class="input-group-addon">%</div>
										</div>
									</div>
									<div class="form-group">
										<label>SGST Rate: &nbsp;</label>
										<div class="input-group">
											<input value="" type="text" class="form-control" name="taxRate2" id="taxRate2" placeholder="SGST Rate">
											<div class="input-group-addon">%</div>
										</div>
									</div>
									<div class="form-group">
										<label>Tax Amount: &nbsp;</label>
										<div class="input-group">
											<div class="input-group-addon currency">Ksh</div>
											<input value="" type="text" class="form-control" name="taxAmount" id="taxAmount" placeholder="Tax Amount">
										</div>
									</div>							
									<div class="form-group">
										<label>Net Amount: &nbsp;</label>
										<div class="input-group">
											<div class="input-group-addon currency">Ksh</div>
											<input value="" type="text" class="form-control" name="totalAftertax" id="totalAftertax" placeholder="Total">
										</div>
									</div>									
								</span>
							</div>
						</div>

												
					</div>
					<div class="modal-footer">
						<input type="hidden" name="id" id="id" />						
						<input type="hidden" name="action" id="action" value="" />						
					</div>
				</div>
			</form>
		</div>
	</div>
			
</div>
 <?php include('inc/footer.php');?>