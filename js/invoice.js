$(document).ready(function(){	
	
	var orderRecords = $('#orderListing').DataTable({
		"lengthChange": false,
		"processing":true,
		"serverSide":true,		
		"bFilter": true,
		'serverMethod': 'post',		
		"order":[],
		"ajax":{
			url:"invoice_action.php",
			type:"POST",
			data:{action:'listOrder'},
			dataType:"json"
		},
		"columnDefs":[
			{
				"targets":[0, 7, 8],
				"orderable":false,
			},
		],
		"pageLength": 10
	});		
	
	
	$('#addOrder').click(function(){
		$('#orderModal').modal({
			backdrop: 'static',
			keyboard: false
		});		
		$("#orderModal").on("shown.bs.modal", function () {
			$('#orderForm')[0].reset();				
			$('.modal-title').html("<i class='fa fa-plus'></i> Add Order");					
			$('#action').val('addOrder');
			$('#save').val('Save');
			getTaxRate();
		});
	});		
	
	$("#orderModal").on("hidden.bs.modal", function () {
		location.reload();
	});
	
	$(document).on('click', '#checkAll', function() {          	
		$(".itemRow").prop("checked", this.checked);
	});	
	$(document).on('click', '.itemRow', function() {  	
		if ($('.itemRow:checked').length == $('.itemRow').length) {
			$('#checkAll').prop('checked', true);
		} else {
			$('#checkAll').prop('checked', false);
		}
	});  
	
	var count = $(".itemRow").length;
	$(document).on('click', '#addRows', function() { 
		count++;
		var htmlRows = itemHTMLRows(count);		
		$('#orderItem').append(htmlRows);
		loadItems(count);		
	}); 
	$(document).on('click', '#removeRows', function(){
		$(".itemRow:checked").each(function() {
			$(this).closest('tr').remove();
		});
		$('#checkAll').prop('checked', false);
		calculateTotal();
	});		
	$(document).on('blur', "[id^=quantity_]", function(){
		calculateTotal();
	});	
	$(document).on('blur', "[id^=price_]", function(){
		calculateTotal();
	});	
	$(document).on('blur', "#taxRate", function(){		
		calculateTotal();
	});	
	
	$(document).on('change', "[id^=items_]", function(){	
		var id = $(this).attr('id');
		id = id.replace("items_",'');
        var itemId = $(this).val();
		$('#price_'+id).html('');
        if(itemId != '') {
            $.ajax({
                url:"invoice_action.php",
                method:"POST",
                data:{action:'loadItemPrice', itemId:itemId},
                success:function(data) {
                    $('#price_'+id).val(data);
					if($('#quantity_'+id).val() == '') {
						$('#quantity_'+id).val(1);						
					}
					calculateTotal();
                }
            });
        }
    });
	
	
	$("#orderListing").on('click', '.update', function(){
		var id = $(this).attr("id");
		var action = 'getOrderDetails';
		$.ajax({
			url:'invoice_action.php',
			method:"POST",
			data:{id:id, action:action},
			dataType:"json",
			success:function(respData){				
				$("#orderModal").on("shown.bs.modal", function () { 
					$('#orderForm')[0].reset();
					var itemCount = 1;										
					respData.data.forEach(function(item){						
						$('#id').val(item['id']);										
						$('#subTotal').val(item['gross_amount']);
						$('#taxAmount').val(item['tax_amount']);
						$('#totalAftertax').val(item['net_amount']);						
						$('#status').val(item['status']);							
							
						if(itemCount == 1) {
							setTimeout(function () {								
								$('#items_1').val(item['item_id']);																
							}, itemCount*150);
							$('#price_'+itemCount).val(item['rate']);
							$('#quantity_'+itemCount).val(item['quantity']);
							$('#total_'+itemCount).val(item['amount']);
							$('#itemIds'+count).val(item['item_id']);

						} else if(itemCount > 1) {
							count++;
							var htmlRows = itemHTMLRows(count);
							$('#orderItem').append(htmlRows);
							loadItems(count);							
							setTimeout(function () {
								$('#items_'+count).val(item['item_id']);															
							}, itemCount*150);													
							$('#price_'+count).val(item['rate']);
							$('#quantity_'+count).val(item['quantity']);
							$('#total_'+count).val(item['amount']); 
							$('#itemIds'+count).val(item['item_id']); 							
						}
						itemCount++;
						
					});
					getTaxRate();
					$('.modal-title').html("<i class='fa fa-plus'></i> Edit Order");
					$('#action').val('updateOrder');
					$('#save').val('Save');					
				}).modal({
					backdrop: 'static',
					keyboard: false
				});			
			}
		});
	});
	
	$("#orderModal").on('submit','#orderForm', function(event){
		event.preventDefault();
		$('#save').attr('disabled','disabled');
		var formData = $(this).serialize();
		$.ajax({
			url:"invoice_action.php",
			method:"POST",
			data:formData,
			success:function(data){				
				$('#orderForm')[0].reset();
				$('#orderModal').modal('hide');				
				$('#save').attr('disabled', false);
				orderRecords.ajax.reload();
			}
		})
	});		

	$("#orderListing").on('click', '.delete', function(){
		var id = $(this).attr("id");		
		var action = "deleteOrder";
		if(confirm("Are you sure you want to delete this record?")) {
			$.ajax({
				url:"invoice_action.php",
				method:"POST",
				data:{id:id, action:action},
				success:function(data) {					
					orderRecords.ajax.reload();
				}
			})
		} else {
			return false;
		}
	});	
	
});

function itemHTMLRows(count) {
	var htmlRows = '';
	htmlRows += '<tr>';
	htmlRows += '<td><input class="itemRow" type="checkbox"></td>'; 		       
	htmlRows += '<td><select name="items[]" id="items_'+count+'" class="form-control"></select></td>';	
	htmlRows += '<td><input type="number" name="price[]" id="price_'+count+'" class="form-control price" autocomplete="off"></td>';		
	htmlRows += '<td><input type="number" name="quantity[]" id="quantity_'+count+'" class="form-control quantity" autocomplete="off"></td>'; 	
	htmlRows += '<td><input type="number" name="total[]" id="total_'+count+'" class="form-control total" autocomplete="off"></td>'; 
	htmlRows += '<input type="hidden" name="itemIds[]" id="itemIds_'+count+'" class="form-control">'; 	
	htmlRows += '</tr>';
	return htmlRows;
}


function getTaxRate () {	
	$.ajax({
		url:"invoice_action.php",
		method:"POST",
		dataType:"json",
		data:{action:'getTaxRate'},
		success:function(respData) {
			respData.data.forEach(function(item){						
				if(item['tax_name'] == 'SGST') {
					$("#taxRate2").val(item['percentage'])
				}	
				if(item['tax_name'] == 'CGST') {
					$("#taxRate1").val(item['percentage'])
				}				
			});		
		}
	});
}


function loadItems(id) {	
	$.ajax({
		url:"invoice_action.php",
		method:"POST",
		data:{action:'loadItemsList'},
		success:function(data) {
			$('#items_'+id).html(data);
		}
	});
}

function calculateTotal(){
	var totalAmount = 0; 
	$("[id^='price_']").each(function() {
		var id = $(this).attr('id');
		id = id.replace("price_",'');
		var price = $('#price_'+id).val();
		var quantity  = $('#quantity_'+id).val();
		if(!quantity) {
			quantity = 1;
		}
		var total = price*quantity;
		$('#total_'+id).val(parseFloat(total));
		totalAmount += total;			
	});
	$('#subTotal').val(parseFloat(totalAmount));	
	var taxRate = parseFloat($("#taxRate1").val());
	taxRate = taxRate + parseFloat($("#taxRate2").val());
	var subTotal = $('#subTotal').val();	
	if(subTotal) {
		var taxAmount = subTotal*taxRate/100;
		$('#taxAmount').val(taxAmount);
		subTotal = parseFloat(subTotal)+parseFloat(taxAmount);
		$('#totalAftertax').val(subTotal);					
	}
}