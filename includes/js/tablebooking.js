	jQuery(document).ready(function(){
			//----save tablebooking----
			/*jQuery('#frmaddtimeslot').on('submit',function(e){
	  		 e.preventDefault();
				 //alert('main function..');
				 restbl_save_timeslot();
			});
			jQuery('#frmaddtimeslot_popup').on('submit',function(e){
	  		 e.preventDefault();
				 //alert('call ed');
				 restbl_save_timeslot_popup();
			});
			jQuery('#btnAddtimeslot_popup').on('click',function(e){
	  		 e.preventDefault();
				 //alert('call ed');
				 restbl_save_timeslot_popup();
			});
			
			jQuery('#frmaddservice').on('submit',function(e){
	  		 e.preventDefault();
				 //alert('main function..');
				 restbl_save_service();
			})
      jQuery('#btnAddservice_popup').on('click',function(e){
	  		 e.preventDefault();
				 restbl_save_service_popup();
			})
			
			jQuery('#frmaddvenue').on('submit',function(e){
	  		 e.preventDefault();
				 restbl_save_venue();
			});
      jQuery('#btnAddvenue_popup').on('click',function(e){
	  		 e.preventDefault();
				 restbl_save_venue_popup();
			});*/
			jQuery('#frmaddschedule').on('submit',function(e){
				 //alert('called...');
	  		 e.preventDefault();
				 restbl_save_schedule();
			});
			jQuery('#opttables').on("change",function(){
				//alert('called....');
				//restbl_set_table_session();	
        restbl_set_ajax_table_session();
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
	function restbl_save_timeslot(){
		//alert('save function..1.');
		var hdntimeslot_id = jQuery('#hdntimeslotid').val();
		//alert(hdntimeslot_id);
		var timeslot_name = jQuery('#timeslot_name').val();
		var starttime = jQuery('#starttime').val();
		var endtime = jQuery('#endtime').val();
		var timeinterval = jQuery('#timeinterval').val();
		//alert('save function..2');
		if(timeslot_name == ""){
			alert('Please input a TimeSlot Name');
			return;
		}
    else if(starttime == ""){
			alert('Please input a Start Time');
			return;
		}
		else if(endtime == ""){
			alert('Please input End Time');
			return;
		}
		else if(timeinterval ==""){
			alert('Please input Time Interval in Minute');
			return;
		}
		jQuery.ajax({
				type: "POST",
						url: ustsTableBookingAjax.ajaxurl,
						data: {
							action: 'usts_add_timeslot_ajax_request',	
							hdntimeslotid: hdntimeslot_id, 
							timeslot_name: timeslot_name, 
							start_time: starttime, 
							end_time:endtime, 
							time_interval: timeinterval
						},
					success: function (data) {
						if(data.length>0){
							alert('added successfully');
              jQuery('#timeslot_name').val('');
              jQuery('#starttime').val('');
              jQuery('#endtime').val('');
              jQuery('#timeinterval').val('');
						}
				},
				error : function(s , i , error){
						console.log(error);
				}
		});
		
	}
	function restbl_save_timeslot_popup(){
		var hdntimeslot_id = jQuery('#hdntimeslotid').val();
		var timeslot_name = jQuery('#timeslot_name').val();
		var starttime = jQuery('#starttime').val();
		var endtime = jQuery('#endtime').val();
		var timeinterval = jQuery('#timeinterval').val();
		if(timeslot_name == ""){
			alert('Please input a TimeSlot Name');
			return;
		}
    else if(starttime == ""){
			alert('Please input a Start Time');
			return;
		}
		else if(endtime == ""){
			alert('Please input End Time');
			return;
		}
		else if(timeinterval ==""){
			alert('Please input Time Interval in Minute');
			return;
		}
		jQuery.ajax({
				type: "POST",
						url: ustsTableBookingAjax.ajaxurl,
						data: {
							action: 'usts_add_timeslot_ajax_request',	
							hdntimeslotid: hdntimeslot_id, 
							timeslot_name: timeslot_name, 
							start_time: starttime, 
							end_time:endtime, 
							time_interval: timeinterval
						},
					success: function (data) {
						if(data.length>0){
							alert('added successfullys');
							jQuery('.es_popup_overlay').hide();
							jQuery('.es_popup_content').hide();
							//jQuery('#frmaddschedule').submit();
						}
				},
				error : function(s , i , error){
						console.log(error);
				},
				complete: function(data){
					window.location.reload();
				}
		});
	} 
	function restbl_save_service(){
		//alert('save function..1.');
		var hdnservice_id = jQuery('#hdnserviceid').val();
		//alert(hdntimeslot_id);
		var provider_name = jQuery('#provider_name').val();
		var service_name = jQuery('#service_name').val();
		
		var service_details = jQuery('#service_details').val();
		var price = jQuery('#price').val();
		var days = "";
		var count=0;
		jQuery('select[name=days] option:selected').each(function(){
			if(count==0){
				days = jQuery(this).val();
			}
			else{
				days = days +','+jQuery(this).val();	
			}
			count++;
		});
    
    if(provider_name == ""){
			alert('Please input a Provider Name');
			return;
		}
		else if(service_name == ""){
			alert('Please input a Service Name');
			return;
		}
		else if(price == ""){
			alert('Please input Price');
			return;
		}
		else if(days ==""){
			alert('Please input Service Days.');
			return;	
		}
		
		jQuery.ajax({
				type: "POST",
						url: ustsTableBookingAjax.ajaxurl,
						data: {
							action: 'usts_add_service_ajax_request',	
							hdnserviceid: hdnservice_id, 
							provider_name: provider_name, 
							service_name: service_name, 
							service_details:service_details, 
							price: price,
							days: days
						},
					success: function (data) {
						if(data.length>0){
							alert('added successfully');
              jQuery('#provider_name').val('');
              jQuery('#service_name').val('');
              jQuery('#service_details').val('');
              jQuery('#price').val('');
						}
				},
				error : function(s , i , error){
						console.log(error);
				}
		});

	}
  function restbl_save_service_popup(){
		var hdnservice_id = jQuery('#hdnserviceid').val();
		var provider_name = jQuery('#provider_name').val();
		var service_name = jQuery('#service_name').val();
		
		var service_details = jQuery('#service_details').val();
		var price = jQuery('#price').val();
		var days = "";
		var count=0;
		jQuery('select[name=days] option:selected').each(function(){
			if(count==0){
				days = jQuery(this).val();
			}
			else{
				days = days +','+jQuery(this).val();	
			}
			
			count++;
		});
		if(provider_name == ""){
			alert('Please input a Provider Name');
			return;
		}
		else if(service_name == ""){
			alert('Please input a Service Name');
			return;
		}
		else if(price == ""){
			alert('Please input Price');
			return;
		}
		else if(days ==""){
			alert('Please input Service Days.');
			return;	
		}
		
		jQuery.ajax({
				type: "POST",
						url: ustsTableBookingAjax.ajaxurl,
						data: {
							action: 'usts_add_service_ajax_request',	
							hdnserviceid: hdnservice_id, 
							provider_name: provider_name, 
							service_name: service_name, 
							service_details:service_details, 
							price: price,
							days: days
						},
					success: function (data) {
						if(data.length>0){
							alert('added successfully');
              jQuery('.es_popup_overlay').hide();
							jQuery('.es_popup_content').hide();
						}
				},
				error : function(s , i , error){
						console.log(error);
				},
				complete: function(data){
					window.location.reload();
				}
		});
	}
	function restbl_save_venue(){
		var hdnvenue_id = jQuery('#hdnvenueid').val();
		var venue_name = jQuery('#venue_name').val();
		var venue_address = jQuery('#venue_address').val();
		var description = jQuery('#description').val();
		if(venue_name == ""){
			alert('Please input a Venue Name');
			return;
		}
		else if(venue_address == ""){
			alert('Please input an Address');
			return;
		}
		jQuery.ajax({
				type: "POST",
						url: ustsTableBookingAjax.ajaxurl,
						data: {
							action: 'usts_add_venue_ajax_request',	
							hdnvenueid: hdnvenue_id, 
							venue_name: venue_name, 
							venue_address: venue_address, 
							description: description 
						},
					success: function (data) {
						if(data.length>0){
							alert('added successfully');
              jQuery('#venue_name').val('');
              jQuery('#venue_address').val('');
              jQuery('#description').val('');
						}
				},
				error : function(s , i , error){
						console.log(error);
				}
		});
		
	}
  function restbl_save_venue_popup(){
		var hdnvenue_id = jQuery('#hdnvenueid').val();
		var venue_name = jQuery('#venue_name').val();
		var venue_address = jQuery('#venue_address').val();
		var description = jQuery('#description').val();
		if(venue_name == ""){
			alert('Please input a Venue Name');
			return;
		}
		else if(venue_address == ""){
			alert('Please input Venue Address');
			return;
		}
		jQuery.ajax({
				type: "POST",
						url: ustsTableBookingAjax.ajaxurl,
						data: {
							action: 'usts_add_venue_ajax_request',	
							hdnvenueid: hdnvenue_id, 
							venue_name: venue_name, 
							venue_address: venue_address, 
							description: description 
						},
					success: function (data) {
						if(data.length>0){
							alert('added successfully');
              jQuery('.es_popup_overlay').hide();
							jQuery('.es_popup_content').hide();
						}
				},
				error : function(s , i , error){
						console.log(error);
				},
				complete: function(data){
					window.location.reload();
				}
		});
		
	}
	function restbl_save_schedule(){
    //alert('in save schedule');
		var hdnschedule_id = jQuery('#hdnscheduleid').val();
		var schedule_name = jQuery('#schedule_name').val();
		var opttimeslot = jQuery('#timeslot').val();
		var optservice = jQuery('#optservice').val();
		var optvenue = jQuery('#optvenue').val();
    if(schedule_name == ""){
      alert('Please Input a Schedule Name');
      return;
    }
    jQuery.ajax({
				type: "POST",
					url: ustsTableBookingAjax.ajaxurl,
					data: {
						action: 'restbl_add_schedule_ajax_request',	
						hdnscheduleid: hdnschedule_id, 
						schedule_name: schedule_name, 
						optservice: optservice, 
						optvenue: optvenue,
						opttimeslot: opttimeslot
					},
					success: function (data) {
            //alert(data);
						if(data.length>0){
							alert('added successfully');
              jQuery('#schedule_name').val('');
						}
				},
				error : function(s , i , error){
						console.log(error);
				}
		});
	}
	
	function restbl_set_ajax_table_session(){
			var tableid = jQuery("select[name=opttables] option:selected").val();
			jQuery.ajax({
					type: "POST",
					url: ustsTableBookingAjax.ajaxurl,
					data: {
						action: 'usts_set_ajax_table_session',
						tableid : tableid
						},
					success: function (data) {
						console.log(data);
					},
					error : function(s , i , error){
						console.log(error);
					},
					complete: function(data){
						jQuery('#frmtables').submit();
					}
			});
	}