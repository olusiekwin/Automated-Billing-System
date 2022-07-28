$(document).ready(function(){	
	
	var itemRecords = $('#itemsListing').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,		
		"bFilter": true,
		'serverMethod': 'post',		
		"order":[],
		"ajax":{
			url:"items_action.php",
			type:"POST",
			data:{action:'listItems'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0, 4, 5],
				"orderable":false,
			},
		],
		"pageLength": 10
	});		
	
	
	$('#addItems').click(function(){
		$('#itemModal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#itemModal").on("shown.bs.modal", function () {
			$('#itemForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add Item");					
			$('#action').val('addItem');
			$('#save').val('Save');
		});
	});		
	
	$("#itemsListing").on('click', '.update', function(){
		var id = $(this).attr("id");
		var action = 'getItemDetails';
		$.ajax({
			url:'items_action.php',
			method:"POST",
			data:{id:id, action:action},
			dataType:"json",
			success:function(respData){				
				$("#itemModal").on("shown.bs.modal", function () { 
					$('#itemForm')[0].reset();
					respData.data.forEach(function(item){						
						$('#id').val(item['id']);						
						$('#itemName').val(item['item_name']);
						$('#price').val(item['price']);												
						$('#status').val(item['status']);
					});														
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit item");
					$('#action').val('updateItem');
					$('#save').val('Save');					
				}).modal({
					backdrop: 'static',
					keyboard: false
				});			
			}
		});
	});
	
	$("#itemModal").on('submit','#itemForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"items_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#itemForm')[0].reset();
				$('#itemModal').modal('hide');				
				$('#save').attr('disabled', false);
				itemRecords.ajax.reload();
			}
		})
	});		

	$("#itemsListing").on('click', '.delete', function(){
		var id = $(this).attr("id");		
		var action = "deleteItem";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"items_action.php",
				method:"POST",
				data:{id:id, action:action},
				success:function(data) {					
					itemRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	
	
});

