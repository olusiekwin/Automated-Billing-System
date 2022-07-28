<h1>Welcome <?php echo ucfirst($_SESSION["role"]); ?> </h1><h3><?php if($_SESSION["userid"]) { echo $_SESSION["name"]; } ?> | <a href="logout.php">Logout</a> </h3><br>
<ul class="nav nav-tabs">	
	<?php if($_SESSION["role"] == 'admin') { ?>		
		<li id="invoices" class="active"><a href="invoices.php">Invoices</a></li>		
		<li id="items"><a href="items.php">Items</a></li>
		<li id="tax"><a href="tax.php">Tax</a></li>		
		<li id="users"><a href="users.php">Users</a></li>		
	<?php } else { ?>
		<li id="invoices"><a href="invoices.php">Invoices</a></li>	
	<?php } ?>
</ul>