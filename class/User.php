<?php
class User {	
   
	private $userTable = 'billing_user';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function login(){
		if($this->email && $this->password) {			
			$sqlQuery = "
				SELECT * FROM ".$this->userTable." 
				WHERE email = ? AND password = ?";			
			$stmt = $this->conn->prepare($sqlQuery);
			$password = md5($this->password);			
			$stmt->bind_param("ss", $this->email, $password);	
			$stmt->execute();
			$result = $stmt->get_result();			
			if($result->num_rows > 0){
				$user = $result->fetch_assoc();
				$_SESSION["userid"] = $user['id'];
				$_SESSION["role"] = $user['role'];
				$_SESSION["name"] = $user['first_name']." ".$user['last_name'];					
				return 1;		
			} else {
				return 0;		
			}			
		} else {
			return 0;
		}
	}
	
	public function loggedIn (){
		if(!empty($_SESSION["userid"])) {
			return 1;
		} else {
			return 0;
		}
	}
	
	public function listUsers(){			
		$sqlQuery = "
			SELECT id, first_name, last_name, gender, email, mobile, created, role
			FROM ".$this->userTable." 
			WHERE id != '".$_SESSION["userid"]."' ";
				
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id ASC ';
		}
		
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
		
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	
		
		$stmtTotal = $this->conn->prepare($sqlQuery);
		$stmtTotal->execute();
		$allResult = $stmtTotal->get_result();
		$allRecords = $allResult->num_rows;
		
		$displayRecords = $result->num_rows;
		$records = array();	
	
		while ($user = $result->fetch_assoc()) { 				
			$rows = array();			
			$rows[] = $user['id'];
			$rows[] = ucfirst($user['first_name']." ".$user['last_name']);
			$rows[] = $user['email'];
			$rows[] = $user['gender'];
			$rows[] = $user['mobile'];
			$rows[] = $user['created'];		
			$rows[] = $user['role'];				
			$rows[] = '<button  type="button" name="view" id="'.$user["id"].'" class="btn btn-info btn-xs view"><span title="View user ">View</span></button>';	
			$rows[] = '<button type="button" name="update" id="'.$user["id"].'" class="btn btn-warning btn-xs update"><span class="glyphicon glyphicon-edit" title="Edit"></span></button>';			
			$rows[] = '<button type="button" name="delete" id="'.$user["id"].'" class="btn btn-danger btn-xs delete" ><span class="glyphicon glyphicon-remove" title="Delete"></span></button>';
			$records[] = $rows;
		}
		
		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	$displayRecords,
			"iTotalDisplayRecords"	=>  $allRecords,
			"data"	=> 	$records
		);
		
		echo json_encode($output);
	}
	
	public function insert(){
		
		if($this->user_first_name && $this->user_email) {

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->userTable."(`first_name`, `last_name`, `gender`, `email`, `password` , `mobile`, `address`, `role`)
				VALUES(?,?,?,?,?,?,?,?)");
		
			$this->user_first_name = htmlspecialchars(strip_tags($this->user_first_name));
			$this->user_last_name  = htmlspecialchars(strip_tags($this->user_last_name));	
			$this->user_email  = htmlspecialchars(strip_tags($this->user_email));
			$this->user_gender  = htmlspecialchars(strip_tags($this->user_gender));
			$this->user_password  = md5(htmlspecialchars(strip_tags($this->user_password)));
			$this->user_mobile  = htmlspecialchars(strip_tags($this->user_mobile));
			$this->user_address  = htmlspecialchars(strip_tags($this->user_address));
			$this->user_role  = htmlspecialchars(strip_tags($this->user_role));

			
			$stmt->bind_param("ssssssss", $this->user_first_name, $this->user_last_name, $this->user_gender, $this->user_email, $this->user_password, $this->user_mobile, $this->user_address, $this->user_role);
			
			if($stmt->execute()){
				return true;
			}		
		}
	}
	
	public function update(){
		
		if($this->user_id && $this->user_first_name && $this->user_email) {
			$updatePassword = '';
			if($this->user_password) {
				$updatePassword = ", password = '".md5($this->user_password)."'";
			}			
			$stmt = $this->conn->prepare("
			UPDATE ".$this->userTable." 
			SET first_name = ?, last_name = ?, gender = ?, email = ?, mobile = ?, address = ?, role = ? $updatePassword
			WHERE id = ?");
	 
			$this->user_first_name = htmlspecialchars(strip_tags($this->user_first_name));
			$this->user_last_name  = htmlspecialchars(strip_tags($this->user_last_name));	
			$this->user_email  = htmlspecialchars(strip_tags($this->user_email));
			$this->user_gender  = htmlspecialchars(strip_tags($this->user_gender));
			$this->user_password  = md5(htmlspecialchars(strip_tags($this->user_password)));
			$this->user_mobile  = htmlspecialchars(strip_tags($this->user_mobile));
			$this->user_address  = htmlspecialchars(strip_tags($this->user_address));
			$this->user_role  = htmlspecialchars(strip_tags($this->user_role));	
			
			$stmt->bind_param("sssssssi", $this->user_first_name, $this->user_last_name, $this->user_gender, $this->user_email, $this->user_mobile, $this->user_address, $this->user_role, $this->user_id);
			
			if($stmt->execute()){				
				return true;
			}			
		}	
	}	
	
	public function getUserDetails(){
		if($this->user_id) {			
			$sqlQuery = "
			SELECT id, first_name, last_name, gender, email, mobile, address, role
			FROM ".$this->userTable." 
			WHERE id = ?";			
					
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->user_id);	
			$stmt->execute();
			$result = $stmt->get_result();				
			$records = array();		
			while ($user = $result->fetch_assoc()) { 				
				$rows = array();	
				$rows['user_id'] = $user['id'];				
				$rows['first_name'] = $user['first_name'];
				$rows['last_name'] = $user['last_name'];			
				$rows['gender'] = $user['gender'];
				$rows['email'] = $user['email'];
				$rows['mobile'] = $user['mobile'];	
				$rows['address'] = $user['address'];
				$rows['role'] = $user['role'];				
				$records[] = $rows;
			}		
			$output = array(			
				"data"	=> 	$records
			);
			echo json_encode($output);
		}
	}
	
	
	public function getUser(){
		if($this->id) {
			$sqlQuery = "
			SELECT id, first_name, last_name, gender, email, mobile, address, created
			FROM ".$this->userTable."			
			WHERE id = ?";		
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->id);	
			$stmt->execute();
			$result = $stmt->get_result();			
			$records = array();		
			while ($user = $result->fetch_assoc()) { 				
				$rows = array();			
				$rows[] = $user['id'];
				$rows[] = ucfirst($user['first_name']." ".$user['last_name']);					
				$rows[] = $user['gender'];
				$rows[] = $user['email'];	
				$rows[] = $user['mobile'];
				$rows[] = $user['address'];
				$rows[] = $user['created'];
				$records[] = $rows;
			}		
			$output = array(			
				"data"	=> 	$records
			);
			echo json_encode($output);
		}
	}	

	public function delete(){
		if($this->user_id && $_SESSION["userid"]) {			

			$stmt = $this->conn->prepare("
				DELETE FROM ".$this->userTable." 
				WHERE id = ?");

			$this->user_id = htmlspecialchars(strip_tags($this->user_id));

			$stmt->bind_param("i", $this->user_id);

			if($stmt->execute()){				
				return true;
			}
		}
	} 
}
?>