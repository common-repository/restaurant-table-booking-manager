	jQuery(document).ready(function(){
			//----save tablebooking----
			//alert('on load....');
			jQuery('#opttables').on("change",function(){
				//alert('called....');
				restbl_set_table_session_front();	
			});
			
	});
	// Read a page's GET URL variables and return them as an associative array.
	function restbl_getUrlVars()
	{
			var vars = [], hash;
			var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++)
			{
					hash = hashes[i].split('=');
					vars.push(hash[0]);
					vars[hash[0]] = hash[1];
			}
			return vars;
	}
	//
 function restbl_set_table_session_front(){
			var tableid = jQuery("select[name=opttables] option:selected").val();
			//alert(' set cookie scheduleid: '+scheduleid);
      //alert(tableid);
			jQuery.ajax({
					type: "POST",
					url: ustsTableBookingAjax_front.ajaxurl,
					data: {
						action: 'usts_set_ajax_table_session_front',
						tableid : tableid
						},
					success: function (data) {
						//alert('set data: '+data)
						console.log(data);
					},
					error : function(s , i , error){
						//alert('set cookie error: '+error);
						console.log(error);
					},
					complete: function(data){
						jQuery('#frmtables').submit();
					}
			});
	}