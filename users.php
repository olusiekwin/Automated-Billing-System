<?php
include_once 'config/Database.php';
include_once 'class/User.php';


$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if(!$user->loggedIn()) {
	header("Location: index.php");
}
include('inc/header.php');
?>
<title>G-billing.com</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="js/general.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/user.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" >  
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
					<button type="button" id="addUser" class="btn btn-info" title="Add user"><span class="glyphicon glyphicon-plus"></span></button>
				</div>
			</div>
		</div>
		<table id="userListing" class="table table-bordered table-striped">
			<thead>
				<tr>						
					<th>Id</th>					
					<th>Name</th>					
					<th>Email</th>
					<th>Gender</th>						
					<th>Mobile</th>	
					<th>Created</th>
					<th>Role</th>					
					<th></th>
					<th></th>
					<th></th>					
				</tr>
			</thead>
		</table>
	</div>
	
	<div id="userDetails" class="modal fade">
		<div class="modal-dialog">    		
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fa fa-plus"></i> User Details</h4>
				</div>
				<div class="modal-body">
					<table id="" class="table table-bordered table-striped">
						<thead>
							<tr>						
								<th>Id</th>					
								<th>Name</th>					
								<th>Email</th>
								<th>Gender</th>						
								<th>Mobile</th>	
								<th>Address</th>	
								<th>Created</th>														
							</tr>
						</thead>
						<tbody id="userList">							
						</tbody>
					</table>								
				</div>    				
			</div>    		
		</div>
	</div>	
	
	<div id="userModal" class="modal fade">
		<div class="modal-dialog">
			<form method="post" id="userForm">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Edit user</h4>
					</div>
					<div class="modal-body">						
						<div class="form-group">
							<div class="row">
								<label class="col-md-4 text-right">First Name <span class="text-danger">*</span></label>
								<div class="col-md-8">
									<input type="text" name="first_name" id="first_name" autocomplete="off" class="form-control" required />
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row">
								<label class="col-md-4 text-right">Last Name <span class="text-danger"></span></label>
								<div class="col-md-8">
									<input type="text" name="last_name" id="last_name" autocomplete="off" class="form-control" />
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row">
								<label class="col-md-4 text-right">Email <span class="text-danger">*</span></label>
								<div class="col-md-8">
									<input type="email" name="email" id="email" autocomplete="off" class="form-control" required />
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row">
								<label class="col-md-4 text-right">Gender <span class="text-danger">*</span></label>
								<div class="col-md-8">
									<select name="gender" id="gender" class="form-control">
										<option value="">Select</option>
										<option value="Male">Male</option>
										<option value="Female">Female</option>										
									</select>
								</div>
							</div>
						</div>	
						
						<div class="form-group">
							<div class="row">
								<label class="col-md-4 text-right">Password <span class="text-danger">*</span></label>
								<div class="col-md-8">
									<input type="password" name="password" id="password" autocomplete="off" class="form-control" />
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row">
								<label class="col-md-4 text-right">Mobile<span class="text-danger">*</span></label>
								<div class="col-md-8">
									<input type="number" name="mobile" id="mobile" autocomplete="off" class="form-control" />
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row">
								<label class="col-md-4 text-right">Address<span class="text-danger">*</span></label>
								<div class="col-md-8">
									<input type="text" name="address" id="address" autocomplete="off" class="form-control" />
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row">
								<label class="col-md-4 text-right">Role <span class="text-danger">*</span></label>
								<div class="col-md-8">
									<select name="role" id="role" class="form-control" required>
										<option value="">Select</option>
										<option value="admin">Admin</option>
										<option value="user">User</option>										
									</select>
								</div>
							</div>
						</div>		
								
					</div>
					<div class="modal-footer">
						<input type="hidden" name="id" id="id" />						
						<input type="hidden" name="action" id="action" value="" />
						<input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
 <?php include('inc/footer.php');?>
