$(document).ready(function(){	
	
	var taxRecords = $('#taxListing').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,		
		"bFilter": true,
		'serverMethod': 'post',		
		"order":[],
		"ajax":{
			url:"tax_action.php",
			type:"POST",
			data:{action:'listTaxes'},
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
	
	
	$('#addTax').click(function(){
		$('#taxModal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#taxModal").on("shown.bs.modal", function () {
			$('#taxForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add Tax");					
			$('#action').val('addTax');
			$('#save').val('Save');
		});
	});		
	
	$("#taxListing").on('click', '.update', function(){
		var id = $(this).attr("id");
		var action = 'getTaxDetails';
		$.ajax({
			url:'tax_action.php',
			method:"POST",
			data:{id:id, action:action},
			dataType:"json",
			success:function(respData){				
				$("#taxModal").on("shown.bs.modal", function () { 
					$('#taxForm')[0].reset();
					respData.data.forEach(function(item){						
						$('#id').val(item['id']);						
						$('#taxName').val(item['tax_name']);
						$('#percentage').val(item['percentage']);						
						$('#status').val(item['status']);
					});														
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit tax");
					$('#action').val('updateTax');
					$('#save').val('Save');					
				}).modal({
					backdrop: 'static',
					keyboard: false
				});			
			}
		});
	});
	
	$("#taxModal").on('submit','#taxForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"tax_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#taxForm')[0].reset();
				$('#taxModal').modal('hide');				
				$('#save').attr('disabled', false);
				taxRecords.ajax.reload();
			}
		})
	});		

	$("#taxListing").on('click', '.delete', function(){
		var id = $(this).attr("id");		
		var action = "deleteTax";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"tax_action.php",
				method:"POST",
				data:{id:id, action:action},
				success:function(data) {					
					taxRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	
	
});

