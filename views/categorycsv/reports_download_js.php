<?php defined('SYSPATH') or die('No direct script access.');
/**
 * categorycsv Java Script View - Creates the download reports page javascript
 *
 
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Category CSV Ushahidi Plugin - https://github.com/jetherton/categorycsv
  
 */
?>

		$(document).ready(function() {
			$("#from_date").datepicker({ 
				showOn: "both", 
				buttonImage: "<?php echo $calendar_img; ?>", 
				buttonImageOnly: true 
			});
			
			$("#to_date").datepicker({ 
				showOn: "both", 
				buttonImage: "<?php echo $calendar_img; ?>", 
				buttonImageOnly: true 
			});
			
			
			$("#reportForm").validate({
				rules: {
					"data_point[]": {
						required: true,
						range: [1,4]
					},
					"data_include[]": {
						range: [1,5]
					},
					from_date: {
						date: true
					},
					to_date: {
						date: true
					}
				},
				messages: {
					"data_point[]": {
						required: "Please select at least one type of report to download",
						range: "Please select a valid report type"
					},
					"data_include[]": {
						range: "Please select a valid item"
					},
					from_date: {
						date: "Please enter a valid FROM Date"
					},
					to_date: {
						date: "Please enter a valid TO Date"
					}
				},
				errorPlacement: function(error, element) {
					if (element.attr("name") == "data_point" || element.attr("name") == "data_include")
					{
						error.appendTo("#form_error1");
					}else if (element.attr("name") == "from_date" || element.attr("name") == "to_date"){
						error.appendTo("#form_error2");
					}else{
						error.insertAfter(element);
					}
				}
			});			
		});
		
		// Check All / Check None
		function CheckAll( id )
		{
			$("INPUT[name='data_point'][type='checkbox']").attr('checked', $('#' + id).is(':checked'));
			$("INPUT[name='data_include'][type='checkbox']").attr('checked', $('#' + id).is(':checked'));			
		}
