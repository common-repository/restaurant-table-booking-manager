<?php
	global $table_prefix,$wpdb;
	$sql_paymentmethod = "select * from ".$table_prefix."restbl_uststablebookings_paymentmethods";
	$payment_methods = $wpdb->get_results( $sql_paymentmethod );
	$current_user = wp_get_current_user();
  
   $sql_taxonomy = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."terms t on tt.term_id = t.term_id where tt.taxonomy = 'restbl_custom_category' ORDER BY tt.term_id ASC";
	$taxonomies = $wpdb->get_results( $sql_taxonomy );
  
  $output .="
	<style type='text/css'>
		li .active{
			color: red;
		}
		li {
			margin-bottom: 0px;
		}
		#namediv input{
			width: 70%;
		}
	</style>
	<script type='text/javascript'>";
  $dayson = get_option('_restbl_booking_days');
  $output .='var dayscond = "";
  var days = "'.$dayson.'";  
  ';
  
	$output .="if(days == '' || days == null ){
		dayscond = '1';
	}
	else{";
  
    $output .="var days = '".$dayson."'; ";
		$output .= "var daysarr = days.split(',');
		for(var i=0;i<daysarr.length;i++){
			if(i==(daysarr.length-1)){
				dayscond = dayscond + 'date.getDay() == '+daysarr[i]+'';
			}
			else{
				dayscond = dayscond + 'date.getDay() == '+daysarr[i]+' || ';
			}
		}
	}
	
  jQuery(function() {
		jQuery('#dtpdate').datepicker({
      dateFormat: 'yy-mm-dd',
			beforeShowDay: function(date){ 
				if(eval(dayscond)){
					return [1];
				}
				else{
					return [0];
				}
			}
		});
   //alert('mmmm');
   jQuery('#dtptest').datepicker({
      dateFormat: 'yy-mm-dd'
   });

	});
  
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
	function restbl_settablebooking_info(tablebooking_id){
			jQuery.ajax({
					type: 'POST',
          url: '".admin_url( 'admin-ajax.php' )."',  
					dataType:'json', 
					data: {
            action: 'restbl_get_tablebookings',
            tablebooking_id:tablebooking_id
          },
					success: function (data) {
							var count = data.length;
							if(data.length > 0 ){
								var tablebooking = data[0];
								jQuery('.hdntablebookingidcls').val(tablebooking['tablebooking_id']);

								jQuery('#dtpdate').val(tablebooking['date']);
								
								jQuery('#txtFirstName').val(tablebooking['first_name']);
								jQuery('#txtLastName').val(tablebooking['last_name']);
								jQuery('#txtEmail').val(tablebooking['email']);
								jQuery('#txtPhone').val(tablebooking['phone']);
								jQuery('#details').val(tablebooking['details']);
								jQuery('#txttablebookingby').val(tablebooking['tablebooking_by']);
								jQuery('#txtCustomPrice').val(tablebooking['custom_price']);
								jQuery('#optpaymentmethod').val(tablebooking['payment_method']);
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
			
			
	}
	function restbl_cleardata(){
			jQuery('#hdntablebookingid').val('');
			
			jQuery('#dtpdate').val('');
			jQuery('#starttime').val('');
			jQuery('#endtime').val('');
			jQuery('#starttime').val('');
			
			jQuery('#txtFirstName').val('');
			jQuery('#txtLastName').val('');
			jQuery('#txtEmail').val('');
			jQuery('#txtPhone').val('');
			jQuery('#details').val('');
			jQuery('#txttablebookingby').val('".$current_user->display_name."');
			jQuery('#txtCustomPrice').val('');
			jQuery('#optpaymentmethod').val('');
	}
	function restbl_load_moredeals_data_pagerefresh(page){
			jQuery.ajax
			({
					type: 'POST',
          url: '".admin_url( 'admin-ajax.php' )."',
					data: {
          action:'restbl_load_managetablebooking_data_front',
          page: page
          },
					success: function(msg)
					{
							jQuery('#inner_content').ajaxComplete(function(event, request, settings)
							{
									jQuery('#inner_content').html(msg);
							});
					}
					
			});
	}
	function restbl_get_serviceprice(){
      
			var arr_schedules = new Array();
			var schedule = jQuery('#schedule_select').val();
			jQuery.ajax({
					type: 'POST',
          url: '".admin_url( 'admin-ajax.php' )."',   
					data: {
            action: 'restbl_get_serviceprice_by_schedule',
            schedule: schedule
          },
					success: function (data) {
              //console.log(data);
							var count = data.length;
              data = data.trim();
              if(jQuery.isNumeric( data )){
                jQuery('#txtCustomPrice').val(data);
              }
					},
					complete: function (data){
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
	}
	jQuery(document).ready(function(){
      jQuery('.multiselect').multipleSelect({
          placeholder: 'Please select Table',
          selectAll: false,
          width:'69%',
          onClick: function(view){
            //evnt_get_eventprice();
          }
        });
        
			restbl_get_serviceprice();
			jQuery('#schedule_select').on('change',function(){
				restbl_get_serviceprice();
			});
			
			var calltype = restbl_getUrlVars()['calltype'];
			if(calltype){
				if(calltype = 'edittablebooking'){";
					if(isset($_REQUEST['id'])){
          $id = $_REQUEST['id'];
					global $table_prefix,$wpdb;
					$sql = "select * from ".$table_prefix."restbl_uststablebookings where tablebooking_id=".$id;
					$result = $wpdb->get_results($sql);
					$output .="var tablebooking = ".json_encode($result[0]).";
					jQuery('#hdntablebookingid').val(tablebooking['tablebooking_id']);
					jQuery('#dtpdate').val(tablebooking['date']);
					
					jQuery('#txtFirstName').val(tablebooking['first_name']);
					jQuery('#txtLastName').val(tablebooking['last_name']);
					jQuery('#txtEmail').val(tablebooking['email']);
					jQuery('#txtPhone').val(tablebooking['phone']);
					jQuery('#details').val(tablebooking['details']);
					jQuery('#txttablebookingby').val(tablebooking['tablebooking_by']);
					jQuery('#txtCustomPrice').val(tablebooking['custom_price']);
					jQuery('#optpaymentmethod').val(tablebooking['payment_method']);";
          }
				$output .="}	
			}
			
			/*jQuery('#dtptodate').on('change',function(){
				restbl_get_serviceprice();
			});*/
			jQuery('#frmtablebooking').on('submit',function(e){
	  		 e.preventDefault();
				 restbl_save_tablebooking();
			});";
			if(isset($_REQUEST['calendarcell'])){
				$calendarcell = $_REQUEST['calendarcell'];
				$calendarcell_data = explode("|",$calendarcell);
				$cell_month_cat = $calendarcell_data[0];
				$cell_month = $calendarcell_data[1];
				$cell_date =  $calendarcell_data[2];
				$output .="jQuery('#dtptodate').val('".$cell_date."');";
			}
	$output .="});
  function restbl_validateTime(strTime) {
    var regex = new RegExp('([0-1][0-9]|2[0-3]):([0-5][0-9])');
    if (regex.test(strTime)) {
      return true;
    } else {
      return false;
    }
  }  
	function restbl_save_tablebooking(){
			var hdntablebookingid = jQuery('.hdntablebookingidcls').val();
			var table = jQuery('#opttables_multiselect :selected').text();
			var tableid = jQuery('#opttables_multiselect :selected').val();
      
			var date = jQuery('#dtpdate').val();
      
      
        
			var starthr = jQuery('#opt_table_starthour').val();
      var startmin = jQuery('#opt_table_startminute').val();
			var starttime = starthr+':'+startmin;
      var endhr = jQuery('#opt_table_endhour').val();
      var endmin = jQuery('#opt_table_endminute').val();
			var endtime = endhr +':'+endmin;
			
			var first_name = jQuery('#txtFirstName').val();
			var last_name = jQuery('#txtLastName').val();
			var email = jQuery('#txtEmail').val();
			var phone = jQuery('#txtPhone').val();
			var details = jQuery('#details').val();
			var tablebookingby = jQuery('#txttablebookingby').val();
			
			var noofseat = jQuery('#txtNoofSeat').val();
			
	
			if(table == ''){
				alert('Please choose at Least a Table.');
				return false;
			}
			else if(date==''){
				alert('Please choose a date.');
				return false;
			}
			
			else if(email!=''){
				if(!restbl_validateEmail(email)){
					alert('Please input a valid email Address.');
					return false;
				}
			}
			else if(phone==''){
				alert('please input your phone number.');
				return false;
			}
			jQuery.ajax({
					type: 'POST',
          url: '".admin_url( 'admin-ajax.php' )."',  
					data: {
            action: 'restbl_check_tablebooking',
            hdntablebookingid: hdntablebookingid,table: table,date:date,starttime:starttime,endtime: endtime
          },
					success: function (data) {
              data = data.trim();
							if(data=='yes'){
								alert('Sorry! Already Booked!');
								return;
							}
							else if(data=='no'){
 								jQuery.ajax({
											type: 'POST',
                      url: '".admin_url( 'admin-ajax.php' )."',
											data: {
                        action: 'restbl_save_tablebooking',
                        hdntablebookingid: hdntablebookingid,tableid:tableid,table: table,date: date, start_time:starttime,end_time:endtime,first_name:first_name,last_name:last_name,email:email,phone:phone,details: details,tablebookingby: tablebookingby,noof_seat: noofseat 
                      },
											success: function (data) {
													if(data.length>0){
                          window.location.href = '".get_option('siteurl')."/gen-usts-tablebooking-calendar/?tblid='+tableid+'&tblshape=round';
														//window.location.href = '".get_option('siteurl')."/?page_id=".SHOPPINGCART_PAGEID."';
                              
													}
											},
											error : function(s , i , error){
													console.log(error);
											}
									});
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
			
	}
	function restbl_validateEmail(email) {
			var atpos=email.indexOf('@');
			var dotpos=email.lastIndexOf('.');
			if (atpos < 1 || dotpos < atpos+2 || dotpos+2 >= email.length) {
					return false;
			}
			return true;
	}

	
  </script>
  <style type='text/css'>
	#frmtablebooking select, button, input, textarea {
		border:1px solid #E2E2E2;
		margin:5px;
	}
	#frmtablebooking label {
		margin:3px;
		width:135px;
	}
	span{font-size:12px;}
	#frmtablebooking table{
		width: 50%;
	}
	input.rounded {
			border: 1px solid #ccc;
	    -moz-border-radius: 5px;
	    -webkit-border-radius: 5px;
	    border-radius: 5px;
	    font-size: 20px;
	    padding: 4px 7px;
	    outline: 0;
	    -webkit-appearance: none;
	}
	select.rounded {
			border: 1px solid #ccc;
	    -moz-border-radius: 5px;
	    -webkit-border-radius: 5px;
	    border-radius: 5px;
	    font-size: 20px;
	    padding: 4px 7px;
	    outline: 0;
	    -webkit-appearance: none;
	}
	input.rounded:focus {
	    border-color: #4CB7FF;
	}
	.ui-dialog {
		z-index:10000!important;
	}
	select, input[type='text']{
		height: 35px;;
	}	
	#addtablebooking_dialog #frmtablebooking table#tbladdtablebookingfrontpopup .tablebookinglavel{
    width:24%;
  }
  #addtablebooking_dialog #frmtablebooking table#tbladdtablebookingfrontpopup .tablebookinginput{
    width:63%;
  }
  #addtablebooking_dialog #frmtablebooking table#tbladdtablebookingfrontpopup .tablebookingasterik{
    width:10%;
  }
  </style>";
  $current_user = wp_get_current_user();
	$output .="
 <div id='addtablebooking_dialog' title='Add/Edit TableBooking' class='wrapper' style='display:none;z-index:5000'>
  <div class='wrap' style='float:left; width:100%;'>
    <div class='main_div'>
     	<div class='metabox-holder' style='width:49%; float:left;'>
        <form id='frmtablebooking' action='' method='post' style='width:100%'>
          <table id='tbladdtablebookingfrontpopup' style='margin:10px;width:500px;'>
          	<tr>
            	<td class='tablebookinglavel'> <label for='table'>Table </label></td>
              <td class='tablebookinginput' id='multi_tables_select' style='padding:0 0 5px 5px;'>
              	<select id='opttables_multiselect' class='multiselect' multiple='multiple'>";
								foreach($taxonomies as $taxo){
                  $output .='<option disabled="disabled" value="'.$taxo->name.'">'.strtoupper($taxo->name).'</option>';
                  $term_id = $taxo->term_id;
                  $sql_table = "select distinct * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id where (p.post_status = 'publish' or p.post_status = 'future') and p.post_type='custom_tablebooking' and tt.term_id=".$term_id." ";
									 
									$tables = $wpdb->get_results($sql_table);	
                  foreach($tables as $table){
                    $output .= '<option value="'.$table->ID.'">'.$table->post_title.'</option>';
                  }
                }
                $output .='</select>
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<label for="from date">Date:</label>
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="dtpdate" name="dtpdate" class="rounded" value="" style="width:230px;" />
                <!--<input type="text" id="dtptest" name="dtptest" class="rounded" value="" style="width:230px;" />-->
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
						<tr>
            	<td class="tablebookinglavel">
              	Start Time:
              </td>
              <td class="tablebookinginput">
              	<select name="opt_table_starthour" id="opt_table_starthour" style="float:left;">';
                  for($i=1;$i<=24;$i++){
                   if($table_starthour==sprintf("%02d", $i))
                           $selectedstarthr = 'selected'; 
                   $output .= '<option value="'.sprintf("%02d", $i) .'"'.$selectedstarthr.'>'. sprintf("%02d", $i) .'</option>';
                  
                  }
                if($table_startminute=='00')
                  $initialstartminselected = 'selected';  
                $output .='</select>
                <select  name="opt_table_startminute" id="opt_table_startminute" style="float:left;"> 
                  <option value="00"'.$initialstartminselected.'>00</option>';
                    
                  for($i=5;$i<=60;$i=$i+5){
                   if($table_startminute==sprintf("%02d", $i))
                           $startminuteselected = 'selected';
                   $output .='<option value="'.sprintf("%02d", $i) .'" '.$startminuteselected.'>'.sprintf("%02d", $i) .'</option>';
                  }
                $output .='</select>
                <span style="font-style:italic;font-size:11px;">Example: 09:00</span>
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	End Time:
              </td>
              <td class="tablebookinginput">
              	<select name="opt_table_endhour" id="opt_table_endhour" style="float:left;">';
                  
                  for($i=1;$i<=24;$i++){
                    if($table_endhour==sprintf("%02d", $i)) 
                         $selectedendhour = 'selected';
                  $output .='<option value="'.sprintf("%02d", $i).'" '.$selectedendhour.'>'.sprintf("%02d", $i) .'</option>
                  ';
                  }
                  
                if($table_endminute=='00')
                  $selectedendminuteinitial = 'selected';  
                $output .='</select>
                <select  name="opt_table_endminute" id="opt_table_endminute"  style="float:left;"> 
                  <option value="00" '.$selectedendminuteinitial.'>00</option>';
                  for($i=5;$i<=60;$i=$i+5){
                    if($table_endminute==sprintf("%02d", $i)) 
                            $selectedendmin = 'selected';
                  $output .= '<option value="'.sprintf("%02d", $i) .'" '.$selectedendmin.'>'.sprintf("%02d", $i) .'</option>';
                  }
                $output .='</select>
                <span style="font-style:italic;font-size:11px;">Example: 10:00</span>
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<label for="first name">First Name:</label>
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtFirstName" name="txtFirstName" class="rounded" value="" />
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<label for="last name">Last Name:</label>
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtLastName" name="txtLastName" class="rounded" value="" />
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<label for="email">Email:</label>
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtEmail" name="txtEmail"  class="rounded" value="" /><!--<span style="color:red;">*</span>-->
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<label for="phone">Phone:</label>
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtPhone" name="txtPhone" class="rounded" value="" />
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<label for="details">Details:</label>
              </td>
              <td class="tablebookinginput">
              	<textarea cols="30" rows="10" id="details" class="rounded" name="details"></textarea>
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<label for="tablebooking By">TableBooking By:</label>
              </td>
              <td class="tablebookinginput">
              	<input type="text" readonly="readonly" id="txttablebookingby" name="txttablebookingby" class="rounded" value="'.$current_user->display_name.'" />
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<label for="price">No of Seat:</label>
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtNoofSeat" name="txtNoofSeat" class="rounded" value="" />
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel" colspan="2" style="height:15px;">
                <input type="hidden" class="hdntablebookingidcls" id="hdntablebookingid" name="hdntablebookingid" value="" style="width:150px;"/>	
              </td>
              <td class="tablebookingasterik"></td>
              </tr>
          </table>
          </form>
          
    		</div>
      </div>
    </div>
   </div>
  ';