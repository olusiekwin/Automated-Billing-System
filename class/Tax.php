<?php
class Tax {	
   
    private $taxTable = 'billing_tax';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function listTaxes(){			
		
		$sqlQuery = "
			SELECT id, tax_name, percentage, status
			FROM ".$this->taxTable." ";
						
		if(!empty($_POST["order"])){
			$sqlQuery .= ' ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= ' ORDER BY id ASC ';
		}
		
		if($_POST["length"] != -1){
			$sqlQuery .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
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
	
		while ($tax = $result->fetch_assoc()) { 				
			$rows = array();			
			$rows[] = $tax['id'];			
			$rows[] = $tax['tax_name'];
			$rows[] = $tax['percentage'].'%';
			$rows[] = $tax['status'];
			$rows[] = '<button type="button" name="update" id="'.$tax["id"].'" class="btn btn-warning btn-xs update"><span class="glyphicon glyphicon-edit" title="Edit"></span></button>';			
			$rows[] = '<button type="button" name="delete" id="'.$tax["id"].'" class="btn btn-danger btn-xs delete" ><span class="glyphicon glyphicon-remove" title="Delete"></span></button>';
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
		
		if($this->taxName && $_SESSION["userid"]) {

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->taxTable."(`tax_name`, `percentage`, `status`)
				VALUES(?, ?, ?)");
		
			$this->taxName = htmlspecialchars(strip_tags($this->taxName));
			$this->percentage = htmlspecialchars(strip_tags($this->percentage));
			$this->status = htmlspecialchars(strip_tags($this->status));
			
			$stmt->bind_param("sss", $this->taxName, $this->percentage, $this->status);
			
			if($stmt->execute()){
				return true;
			}		
		}
	}
	
	public function update(){
		
		if($this->id && $this->taxName && $_SESSION["userid"]) {
			
			$stmt = $this->conn->prepare("
			UPDATE ".$this->taxTable." 
			SET tax_name = ?, percentage = ?, status = ?
			WHERE id = ?");
	 
			$this->taxName = htmlspecialchars(strip_tags($this->taxName));
			$this->percentage = htmlspecialchars(strip_tags($this->percentage));
			$this->status = htmlspecialchars(strip_tags($this->status));
								
			$stmt->bind_param("sssi", $this->taxName, $this->percentage, $this->status, $this->id);
			
			if($stmt->execute()){				
				return true;
			}			
		}	
	}	
	
	public function getTaxDetails(){
		if($this->id && $_SESSION["userid"]) {			
					
			$sqlQuery = "
			SELECT id, tax_name, percentage, status
			FROM ".$this->taxTable." WHERE id = ? ";	
					
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->id);	
			$stmt->execute();
			$result = $stmt->get_result();				
			$records = array();		
			while ($tax = $result->fetch_assoc()) { 				
				$rows = array();	
				$rows['id'] = $tax['id'];				
				$rows['tax_name'] = $tax['tax_name'];
				$rows['percentage'] = $tax['percentage'];						
				$rows['status'] = $tax['status'];					
				$records[] = $rows;
			}		
			$output = array(			
				"data"	=> 	$records
			);
			echo json_encode($output);
		}
	}
	

	public function delete(){
		if($this->id && $_SESSION["userid"]) {			

			$stmt = $this->conn->prepare("
				DELETE FROM ".$this->taxTable." 
				WHERE id = ?");

			$this->id = htmlspecialchars(strip_tags($this->id));

			$stmt->bind_param("i", $this->id);

			if($stmt->execute()){				
				return true;
			}
		}
	} 
}
?>