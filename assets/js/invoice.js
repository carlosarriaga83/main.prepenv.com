'use strict';

(function ($) {
  $('#addRow').click(function() {
      const rowCount = $('#invoice-table tbody tr').length + 1;
      const newRow = `
          <tr>
              <td><span SUB_TBS="item" >${String(rowCount).padStart(2, '0')}</span></td>
              <td><input type="text" class="invoive-form-control product-autofill" SUB_TBS="item_name" value="" required></td>
              <td class="d-none"><input type="text" class="invoive-form-control stripe_product_id" SUB_TBS="stripe_product_id" value=""></td>
              <td><input type="number" 	class="invoive-form-control item_qty" 	SUB_TBS="item_qty" 		value="1" required></td>
              <td><input type="text" 	class="invoive-form-control" 			SUB_TBS="item_units" 	value="Pza"></td>
              <td><input type="number" 	class="invoive-form-control item_price" SUB_TBS="item_price" 	value="0.00" step="0.01" required></td>  
              <td><input type="number" 	class="invoive-form-control item_total" SUB_TBS="item_total" 	value="0.00" step="0.01"></td> 
              <td class="text-center">
                  <button type="button" class="remove-row"><iconify-icon icon="ic:twotone-close" class="text-danger-main text-xl"></iconify-icon></button>
              </td>
          </tr>
      `;
      $('#invoice-table tbody').append(newRow);
  });

  $(document).on('click', '.remove-row', function() {
      $(this).closest('tr').remove();
      updateRowNumbers();
	  calculateInvoiceTotal();
  });

  function updateRowNumbers() {
    $('#invoice-table tbody tr').each(function(index) {
      $(this).find('td:first').text(String(index + 1).padStart(2, '0'));
    });
  }

	function makeEditable(selector, inputClass) {
	  $(selector).click(function() {
		const cell = $(this);
		const originalText = cell.text();
		const input = $('<input type="text" />').addClass(inputClass).val(originalText);
		
		if (originalText != ''){	
			cell.empty().append(input);
		}
		input.focus().select();

		input.on('blur keypress', function(e) {
		  if(e.type === 'keypress' && e.which !== 13) return; // Skip if it's not Enter key
		  var newText = input.val();
		  debugger;
			newText = newText.trim();
			if (newText === '') {
			  //cell.text('__________');
			} else if (newText !== '') {
			  if (e.type === 'keypress') e.preventDefault(); // Prevent form submission
			  cell.text(newText);
			} else {
			  //cell.text(' ' + newText);
			}
			
			
		});
	  });
	}

	makeEditable('.editable', 'invoive-form-control');
	makeEditable('.client-editable', 'invoive-form-control client-autofill');
	makeEditable('.product-editable', 'invoive-form-control product-autofill');
	makeEditable('.invoice_tax_rate', 'invoive-form-control invoice_tax_rate');   

	  
  
})(jQuery);

function calculateRowTotal($row) {
	
    var qty = $row.find('.item_qty').val();
    var price = $row.find('.item_price').val().replace(/[^0-9.-]+/g,""); // remove non-numeric characters
    var total = qty * price;
    $row.find('.item_total').val(total);
	
	calculateInvoiceTotal();
	
};


function calculateInvoiceTotal() {

    if ($('input[name=TAX_IN_PRICES]').is(':checked')) {     $('input[name=TAX_ENABLED]').prop('checked', true); }// Force TAX_ENABLED to be checked 
	
	
	
    var tax_rate = parseFloat($('.invoice_tax_rate').text().replace(/[^0-9.-]+/g,"")); // remove non-numeric characters
    
    // if tax_rate is NaN, get the value from the input with class invoice_tax_rate if it exist
	if ($('input.invoice_tax_rate').length) {
		tax_rate = parseFloat($('input.invoice_tax_rate').val().replace(/[^0-9.-]+/g, ""));
		} 
	// Check if it's span tag
	else if ($('span.invoice_tax_rate').length) {
		tax_rate = parseFloat($('span.invoice_tax_rate').text().replace(/[^0-9.-]+/g, ""));
	}
	
	
    if (isNaN(tax_rate)) {
        tax_rate = 0;
    }
	
    var tax_rate_label = tax_rate; // remove non-numeric characters
	$('.invoice_tax_rate_label').text(tax_rate_label);
	
	
    var subtotal_0 = 0;
    var subtotal_1 = 0;
	
    $('.item_total').each(function(){
        subtotal_0 += parseFloat($(this).val().replace(/[^0-9.-]+/g,"")); // remove non-numeric characters
    });
	

    if (!$('input[name=TAX_IN_PRICES]').is(':checked')) {
		subtotal_1 = subtotal_0;
    }else{ 
        subtotal_1 = subtotal_0 * ((100-tax_rate)/100) ;
		
	}
	
    if ($('input[name=TAX_ENABLED]').is(':checked')) {

    }else{ 
        $('.invoice_tax_rate_label').text('');
		
	}
	
    $('.invoice_subtotal_1').text(subtotal_1.toFixed(2));
	

    var discount = parseFloat($('.invoice_discount').text().replace(/[^0-9.-]+/g,"")); // remove non-numeric characters
    if (isNaN(discount)) {	discount = 0;    }
	
    $('.invoice_subtotal_2').text((subtotal_1 + discount).toFixed(2));

    var subtotal_2 = parseFloat($('.invoice_subtotal_2').text().replace(/[^0-9.-]+/g,"")); // remove non-numeric characters
    

    
    $('.invoice_tax').text((subtotal_0 * (tax_rate/100)).toFixed(2));
    
    // check if TAX_ENABLED checkbox is checked
    if (!$('input[name=TAX_ENABLED]').is(':checked')) {
        $('.invoice_tax').text(0);
    }

    var tax = parseFloat($('.invoice_tax').text().replace(/[^0-9.-]+/g,"")); // remove non-numeric characters

    var invoiceTotal = subtotal_2 + discount + tax;
    
    $('.invoice_total').text(invoiceTotal.toFixed(2));
    
}