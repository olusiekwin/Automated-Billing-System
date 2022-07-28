<?php
class Item {	
   
    private $itemTable = 'billing_items';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function listItems(){			
		
		$sqlQuery = "
			SELECT item.id, item.name AS item_name, item.price, item.category_id, item.status
			FROM ".$this->itemTable." item ";
						
		if(!empty($_POST["order"])){
			$sqlQuery .= ' ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= ' ORDER BY item.id ASC ';
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
	
		while ($items = $result->fetch_assoc()) { 				
			$rows = array();			
			$rows[] = $items['id'];			
			$rows[] = $items['item_name'];
			$rows[] = "$".$items['price'];			
			$rows[] = $items['status'];
			$rows[] = '<button type="button" name="update" id="'.$items["id"].'" class="btn btn-warning btn-xs update"><span class="glyphicon glyphicon-edit" title="Edit"></span></button>';			
			$rows[] = '<button type="button" name="delete" id="'.$items["id"].'" class="btn btn-danger btn-xs delete" ><span class="glyphicon glyphicon-remove" title="Delete"></span></button>';
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
		
		if($this->itemName && $_SESSION["userid"]) {

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->itemTable."(`name`, `price`, `status`)
				VALUES(?, ?, ?)");
		
			$this->itemName = htmlspecialchars(strip_tags($this->itemName));
			$this->price = htmlspecialchars(strip_tags($this->price));			
			$this->status = htmlspecialchars(strip_tags($this->status));
			
			$stmt->bind_param("sss", $this->itemName, $this->price, $this->status);
			
			if($stmt->execute()){
				return true;
			}		
		}
	}
	
	public function update(){
		
		if($this->id && $this->itemName && $_SESSION["userid"]) {
			
			$stmt = $this->conn->prepare("
			UPDATE ".$this->itemTable." 
			SET name = ?, price = ?, status = ?
			WHERE id = ?");
	 
			$this->itemName = htmlspecialchars(strip_tags($this->itemName));
			$this->price = htmlspecialchars(strip_tags($this->price));			
			$this->status = htmlspecialchars(strip_tags($this->status));
								
			$stmt->bind_param("sssi", $this->itemName, $this->price, $this->status, $this->id);
			
			if($stmt->execute()){				
				return true;
			}			
		}	
	}	
	
	public function getItemDetails(){
		if($this->id && $_SESSION["userid"]) {			
					
			$sqlQuery = "
			SELECT item.id, item.name AS item_name, item.price, item.category_id, item.status
			FROM ".$this->itemTable." item 			
			WHERE item.id = ?";
					
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->id);	
			$stmt->execute();
			$result = $stmt->get_result();				
			$records = array();		
			while ($tax = $result->fetch_assoc()) { 				
				$rows = array();	
				$rows['id'] = $tax['id'];				
				$rows['item_name'] = $tax['item_name'];
				$rows['price'] = $tax['price'];							
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
				DELETE FROM ".$this->itemTable." 
				WHERE id = ?");

			$this->id = htmlspecialchars(strip_tags($this->id));

			$stmt->bind_param("i", $this->id);

			if($stmt->execute()){				
				return true;
			}
		}
	} 	
	
	function getItems(){		
		$stmt = $this->conn->prepare("
		SELECT id, name 
		FROM ".$this->itemTable);				
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	
}
?>