function getProduct(prod_type) {
	$('#prod_type1').val(prod_type);
	$('#prod_type2').val(prod_type);
	
	$.ajax({
		url: 'ajax/product.php',
		type: 'post',
		data: {
			product_type: prod_type
		},
		success: function(data) {
			$('#products').find('tr:gt(0)').remove();
			$('#products').append(data);
			$('#new_product').css('display', 'none');
		}
	});
}

$(document).ready(function() {
	$('#new_item').click(function() {
		$('#items tbody>tr:last').clone(true).insertAfter('#items tbody>tr:last');
		$('#items tbody>tr:last input').val('');
		return false;
	});
	
	$('#remove_item').click(function() {
		if($('#items tr').length > 3)
			$('#items tbody>tr:last').remove();
	});
	
	$('#search').click(function() {
		doSearch();
	});
	
	$('.product').click(function() {
		var prod_type = $(this).attr('id');
		getProduct(prod_type);
	});
	
	$('#products').click(function() {
		var source = $(event.target);
		var tr = source.parent().parent();
		
		if(source[0].tagName == 'A') {
			if(source.attr('class') == 'delete_product') {
				if(confirm("Are you sure you want to delete this product?")) {
					$.ajax({
						url: 'ajax/delete_product.php',
						type: 'post',
						data: {
							id: source.attr('id')
						},
						success: function(data) {
							getProduct($('#prod_type1').val());
						}
					});
				}
			}
		}
	});
	
	$('.cancel').click(function() {console.log('clicked');
		$(this).parent().parent().remove();
	});
	
	$('#new_row').click(function() {
		$('#new_product tbody>tr:last').clone(true).insertAfter('#new_product tbody>tr:last');
		$('#new_product tbody>tr:last input').val('');
		return false;
	});
	
	$('#del_row').click(function() {
		if($('#new_product tr').length > 6)
			$('#new_product tbody>tr:last').remove();
	});
	
	$('.quantity').blur(function() {
		calcTotal($(this).parent().parent());
	});
	
	$('.unit_price').blur(function() {
		var val = parseFloat($(this).val(), 10).toFixed(3);
		$(this).val(val);
		calcTotal($(this).parent().parent());
	});
	
	$('#newProduct').submit(function(e) {
		var selectEmpty = false;
		$('#product_type option:selected').each(function() {
			if($(this).val() == '') {console.log('select is empty');
				selectEmpty = true;
			}
		});
		
		var quantityEmpty = false;
		$('input[name="quantity[]"]').each(function() {
			if($(this).val() == '') {console.log('quantity is empty');
				quantityEmpty = true;
			}
		});
		
		var upriceEmpty = false;
		$('input[name="unit_price[]"]').each(function() {
			if($(this).val() == '') {console.log('unit_price is empty');
				upriceEmpty = true;
			}
		});
		
		var epriceEmpty = false;
		$('input[name="extended_price[]"]').each(function() {
			if($(this).val() == '') {console.log('extended_price is empty');
				epriceEmpty = true;
			}
		});
		
		if($('#product_code').val() == '' ||
			$('#timestamp').val() == '' ||
			$('#product_code').val() == '' ||
			$('#product_desc').val() == '' ||
			selectEmpty || quantityEmpty || upriceEmpty || epriceEmpty) {
				$('#msg').text('Oops, did you forget to fill out something?');
				e.preventDefault();
		}
	});
	
	$('#newClient').submit(function (e) {
		$('.required').each(function() {
			if($(this)[0].tagName == 'SELECT') {
				$('#rep option:selected').each(function() {
					if($(this).val() == '') {
						e.preventDefault();
						$('#msg').text('Oops, did you forget to fill something out?');
					}
				});
			}
			else {
				if($(this).val() == '') {
					e.preventDefault();
					$('#msg').text('Oops, did you forget to fill something out?');
				}
			}
		});
	});
	
	$('#zipcode').blur(function() {
		$.ajax({
			url: 'ajax/search_zipcode.php',
			type: 'post',
			data: {
				zipcode: $(this).val()
			},
			success: function(data) {console.log(data);
				var tmp = data.split(',');
				$('#city').val(tmp[0]);
				$('#state').val(tmp[1]);
			}
		});
	});
	
	$('#newRep').submit(function(e) {
		$('.required').each(function() {
			if($(this).val() == '') {
				e.preventDefault();
				$('#msg').text('Oops, did you forget to fill something out?');
			}
		});
	});
	
	$('.search_input').keyup(function(e) {
		if(e.keyCode == '13') {
			doSearch();
		}
	});
	
	$('#modalForm').click(function() {
		$('#clientForm').modal();
	});
	
	$('#closeModal').click(function() {
		$.modal.close();
	});
	
	$('#newClientModal').click(function() {
		var submitForm = true;
		var repID = 0;
		
		$('.required').each(function() {
			if($(this)[0].tagName == 'SELECT') {
				$('#rep option:selected').each(function() {
					repID = $(this).val();
					if($(this).val() == '') {
						submitForm = false;
						$('#msg').text('Oops, did you forget to fill something out?');
					}
				});
			}
			else {
				if($(this).val() == '') {
					submitForm = false;
					$('#msg').text('Oops, did you forget to fill something out?');
				}
			}
		});
		
		if(submitForm) {
			$.ajax({
				url: 'ajax/new_client.php',
				type: 'post',
				data: {
					date: $('#date').val(),
					contact: $('#contact').val(),
					business: $('#business').val(),
					address: $('#address').val(),
					city: $('#city').val(),
					state: $('#state').val(),
					zipcode: $('#zipcode').val(),
					phone1: $('#phone1').val(),
					phone2: $('#phone2').val(),
					phone3: $('#phone3').val(),
					rep: repID,
					comment: $('#comment').val(),
					email: $('#email').val()
				},
				success: function(data) {
					console.log(data);
					var resp = jQuery.parseJSON(data);
					$('#msg').html(resp.msg);
					
					if(resp.success) {
						$('#client').val($('#business').val());
						$('#lead_id').val(resp.lead_id);
						$('#desc').val(resp.ad_copy);
						setTimeout('$.modal.close()', 1000);
					}
				}
			});
		}
	});
	
	$('#searchInvoice').click(function() {
		searchForInvoice();
	});
	
	// Hit enter while inside text box
	$('#invoiceNum').keyup(function(e) {
		if(e.keyCode == '13') {
			searchForInvoice();
		}
	});
	
	$('#invoiceBtn').click(function() {
		$('#modalInvoices').modal();
	});
	
	$('#gg').click(function() {
		var invoices = $("input[name='invoices\\[\\]']").map(function(){
			return $(this).val();
		}).get();
		
		var invoice_ids = "";
		for(var i = 0; i < invoices.length; i++)
		{
			invoice_ids += invoices[i] + ',';
		}
		
		$.ajax({
			url: 'ajax/update_invoices.php',
			type: 'get',
			data: {
				which_date: $('#which_date').val(),
				new_date: $('#new_date').val(),
				invoice_ids: invoice_ids
			},
			success: function(data) {
				if(data == 'success')
					$('#msg').text("Invoices have been updated");
			}
		});
	});
	
	$('a.remove_invoice').live('click', function() {
		$(this).parent().parent().remove();
	});
	
	$('a.delete_invoice').click(function() {console.log($(this).attr('id'));
		if(confirm("Are you sure you want to delete this invoice?")) {
			$.ajax({
				url: 'ajax/delete_invoice.php',
				type: 'get',
				data: {
					id: $(this).attr('id')
				},
				success: function() {
					location.reload();
				}
			});
		}
	});
	
	$('#newInvoiceForm').submit(function(e) {
		var error = $('#error').val();
		if(error == "good")
			return true;
		else {
			var msg = "";
			if(error == "error-paid")
				msg = "This client has an outstanding UNPAID invoice. Would you like to continue?";
			else if(error == "error-ship")
				msg = "This client still has an UNSHIPPED invoice pending. Would you like to continue?";
			
			if(confirm(msg))
				return true;
			else
				return false;
		}
		
		/*$.ajax({
			url: 'ajax/verify_lead.php',
			type: 'get',
			data: {
				lead_id: $('#lead_id').val()
			},
			success: function(data) {console.log(data);
				if(data == 'success') {
					$('#newInvoiceForm').unbind('submit', preventDefault);
					$('#newInvoiceForm').submit();
				}
				else {
					var msg = "";
					if(data == 'error-paid') {
						msg = "This client has an outstanding UNPAID invoice. Would you like to continue?";
					}
					else if(data == 'error-ship') {
						msg = "This client still has an UNSHIPPED invoice pending. Would you like to continue?";
					}
					
					if(confirm(msg)) {
						$('#newInvoiceForm').unbind('submit', preventDefault);
						$('#newInvoiceForm').submit();
					}
				}
			}
		});*/
	});
	
	$('a.misc').click(function() {
		$('#phpmsg').html("");
		if($(this).attr('id') == "something") {
			$('#switchDiv').show();
			$('#invoice_update').hide();
		}
		else {
			$('#invoice_update').show();
			$('#switchDiv').hide();
			
			if($(this).attr('id') == "paid_invoices") {
				$('#title').html("Update Paid Invoices");
				$('#which_date').val('paid');
				$('#msg').html('You are updating the PAID date for these invoices');
			}
			else {
				$('#title').html('Update Shipped Invoices');
				$('#which_date').val('ship');
				$('#msg').html('You are updating the SHIP date for these invoices');
			}
		}
	});
	
	function calcTotal(tr) {
		var inputs = tr.find('input');
		var ext_price = parseInt(inputs[0].value, 10) * parseFloat(inputs[1].value, 10);
		
		if(isNaN(ext_price) || ext_price == 0)
			inputs[2].value = '0.00';
		else
			inputs[2].value = ext_price.toFixed(2);
	}
	
	function doSearch() {
		$.ajax({
			url: 'ajax/search.php',
			type: 'post',
			data: {
				business: $('#business').val(),
				phone: $('#phone').val()
			},
			success: function(data) {console.log(data);
				$("#leads").find("tr:gt(1)").remove();
				if(data == "")
					$("<tr><td colspan=\"10\" style=\"text-align:center;\">No results</td></tr>").insertAfter('#leads tbody > tr:last');
				else
					$(data).insertAfter('#leads tbody > tr:last');
			}
		});
	}
	
	function searchForInvoice() {
		$.ajax({
			url: 'ajax/search_invoices.php',
			type: 'get',
			data: {
				invoice_id: $('#invoiceNum').val()
			},
			success: function(data) {
				if(data != 'error')
					$('#invoiceSearch').append(data);
			}
		});
	}
	
	function preventDefault(e) {
		e.preventDefault();
	}
	
	$(".numeric").numeric();
	$(".integer").numeric(false);
	
	var options, a;
	jQuery(function() {
		options = {
			serviceUrl: 'ajax/search_client.php',
			maxHeight: 200,
			width: 400,
			zIndex: 9999,
			onSelect: function(value, data) {console.log(data);
				$('#lead_id').val(data.id);
				$('#desc').val(data.venue);
				$('#addr').text(data.addr);
				$('#owner').text(data.owner);
				
				// Check for any outstanding invoices for this lead
				$.ajax({
					url: 'ajax/verify_lead.php',
					type: 'get',
					data: {
						lead_id: data.id
					},
					success: function(data2) {console.log(data2);
						$('#error').val(data2);
					}
				});
			}
		};
		a = $('#client').autocomplete(options);
	});
});