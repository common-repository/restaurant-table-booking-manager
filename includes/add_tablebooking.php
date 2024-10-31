<?php
	global $table_prefix,$wpdb;
	$sql_paymentmethod = "select * from ".$table_prefix."restbl_uststablebookings_paymentmethods";
	$payment_methods = $wpdb->get_results( $sql_paymentmethod );
	$current_user = wp_get_current_user();
  
  $sql_taxonomy = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."terms t on tt.term_id = t.term_id where tt.taxonomy = 'restbl_custom_category' ORDER BY tt.term_id ASC";
	$taxonomies = $wpdb->get_results( $sql_taxonomy );
  
	?>
	<style type="text/css">
		li .active{
			color: red;
		}
		li {
			margin-bottom: 0px;
		}
		#namediv input{
			width: 70%;
		}
    div.multiselect{
      width: 65%!important;      
    }
	</style>
   
	<script type="text/javascript">
	/*var venue_service_schedule = Array();
  <//?php if(isset($tvs_result[0])){ ?>
    venue_service_schedule = <//?php echo json_encode($tvs_result[0]);?>;
  <//?php } ?>  */
  <?php $dayson = get_option('_restbl_booking_days');?>  
	var dayscond = "";
  var days = "<?php echo $dayson ?>";
	if(days == "" || days == null ){
		dayscond = "1";
	}
	else{
		var days = "<?php echo $dayson ?>";
		var daysarr = days.split(',');
		
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
		jQuery("#dtpdate").datepicker({
      dateFormat: "yy-mm-dd", 
			beforeShowDay: function(date){ 
				if(eval(dayscond)){
					return [1];
				}
				else{
					return [0];
				}
			
			}
		
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
	function restbl_get_serviceprice(){
			//alert('called');
      var arr_schedules = new Array();
			var schedule = jQuery('#schedule_select').val();
      //alert(schedule);
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					data: {
              action: 'restbl_get_serviceprice_by_schedule',
              schedule: schedule
          },
					success: function (data) {
							var count = data.length;
              console.log(data);
              data = data.trim();
							jQuery('#txtCustomPrice').val(data);
					},
					complete: function (data){
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
	}
	function restbl_settablebooking_info(tablebooking_id){
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
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
								var roomids = tablebooking['room_id'].split(',');
					
								jQuery('#dtpfromdate').val(tablebooking['from_date']);
								jQuery('#dtptodate').val(tablebooking['to_date']);
								
								jQuery('#txtFirstName').val(tablebooking['first_name']);
								jQuery('#txtLastName').val(tablebooking['last_name']);
								jQuery('#txtEmail').val(tablebooking['email']);
								jQuery('#txtPhone').val(tablebooking['phone']);
								jQuery('#details').val(tablebooking['details']);
								jQuery('#txttablebookingby').val(tablebooking['tablebooking_by']);
								jQuery('#optguest_type').val(tablebooking['guest_type']);
								jQuery('#txtCustomPrice').val(tablebooking['custom_price']);
								jQuery('#txtPaid').val(tablebooking['paid']);
								jQuery('#txtDue').val(tablebooking['due']);
								jQuery('#optpaymentmethod').val(tablebooking['payment_method']);
								jQuery('#txtTrackingNo').val(tablebooking['tracking_no']);
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
	}
	function restbl_cleardata(){
			jQuery('#hdntablebookingid').val('');
			//jQuery('#rooms_multiselect').multiselect('refresh');
			
			jQuery('#dtpdate').val('');
			jQuery('#starttime').val('');
			jQuery('#endtime').val('');
			jQuery('#starttime').val('');
			
			jQuery('#txtFirstName').val('');
			jQuery('#txtLastName').val('');
			jQuery('#txtEmail').val('');
			jQuery('#txtPhone').val('');
			jQuery('#details').val('');
			jQuery('#txttablebookingby').val('<?php echo $current_user->display_name?>');
			jQuery('#txtCustomPrice').val('');
			jQuery('#optpaymentmethod').val('');
	}
	function restbl_load_moredeals_data_pagerefresh(page){
			jQuery.ajax
			({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					data: {
            action: 'restbl_load_managetablebooking_data_front',  
            page: page
          },
					success: function(msg)
					{
							jQuery("#inner_content").ajaxComplete(function(event, request, settings)
							{
									jQuery("#inner_content").html(msg);
							});
					}
					
			});
	
	}
	
	jQuery(document).ready(function(){
			//restbl_get_serviceprice();
			var calltype = restbl_getUrlVars()["calltype"];
			if(calltype){
				if(calltype = 'edittablebooking'){
					<?php
          if(isset($_REQUEST['id'])){
            $id = $_REQUEST['id'];
            global $table_prefix,$wpdb;
            $sql = "select * from ".$table_prefix."restbl_uststablebooking where tablebooking_id=".$id;
            $result = $wpdb->get_results($sql);
            ?>
            var tablebooking = <?php echo json_encode($result[0]);?>;
            jQuery('#hdntablebookingid').val(tablebooking['tablebooking_id']);
            jQuery('#dtpfromdate').val(tablebooking['from_date']);
            jQuery('#txtFirstName').val(tablebooking['first_name']);
            jQuery('#txtLastName').val(tablebooking['last_name']);
            jQuery('#txtEmail').val(tablebooking['email']);
            jQuery('#txtPhone').val(tablebooking['phone']);
            jQuery('#details').val(tablebooking['details']);
            jQuery('#txttablebookingby').val(tablebooking['tablebooking_by']);
            jQuery('#optguest_type').val(tablebooking['guest_type']);
            jQuery('#txtCustomPrice').val(tablebooking['custom_price']);
            jQuery('#txtPaid').val(tablebooking['paid']);
            jQuery('#txtDue').val(tablebooking['due']);
            jQuery('#optpaymentmethod').val(tablebooking['payment_method']);
            jQuery('#txtTrackingNo').val(tablebooking['tracking_no']);
          <?php } ?>  
				}	
			}
			
      //---------------------------------	
			jQuery('#table_select').on("change",function(){
				//restbl_get_serviceprice();
			});
			//----save tablebooking----
			jQuery('#frmtablebooking').on('submit',function(e){
	  		 e.preventDefault();
				 restbl_save_tablebooking();
			});
			//---------------------------
      jQuery('.multiselect').multipleSelect({
				placeholder: '<?php _e("Please select Table","restbl-uststablebooking"); ?>',
				selectAll: false,
				width:'39%',
				onClick: function(view){
					//evnt_get_eventprice();
				}
			});
      //--------------------------------
			<?php if(isset($_REQUEST['calendarcell'])){
			$calendarcell = $_REQUEST['calendarcell'];
			$calendarcell_data = explode("|",$calendarcell);
			$cell_month_cat = $calendarcell_data[0];
			$cell_month = $calendarcell_data[1];
			$cell_date =  $calendarcell_data[2];
			?>
					jQuery('#dtpfromdate').val('<?php// echo $cell_date;?>');	
					jQuery('#dtptodate').val('<?php echo $cell_date;?>');	
					
			<?php }?>
			//--------------------------------
	});
  function restbl_validateTime(strTime) {
    var regex = new RegExp("([0-1][0-9]|2[0-3]):([0-5][0-9])");
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
			//var timeshift = jQuery('#timeshift').val();
			
			var first_name = jQuery('#txtFirstName').val();
			var last_name = jQuery('#txtLastName').val();
			var email = jQuery('#txtEmail').val();
			var phone = jQuery('#txtPhone').val();
			var details = jQuery('#details').val();
			var tablebookingby = jQuery('#txttablebookingby').val();
			//var guest_type = jQuery('#optguest_type').val();
			var noofseat = jQuery('#txtNoofSeat').val();
			//var payment_method = jQuery('#optpaymentmethod').find('option:selected').val();
	
			//---validation----
			
			if(table == ""){
				alert('Please choose at Least a Table.');
				return false;
			}
			else if(date==""){
				alert('Please choose a date.');
				return;
			}
			/*/else if(starttime==""){
				alert('Please choose a StartTime.');
				return;
			}
      else if(!restbl_validateTime(starttime)){
        alert('Please Correct Start Time Format');
        return;
      }
			else if(endtime==""){
				alert('Please choose a EndTime.');
				return;
			}
      else if(!restbl_validateTime(endtime)){
        alert('Please Correct End Time Format');
        return;
      }*/
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
			//------------------
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					data: {
            action:'restbl_check_tablebooking',
            hdntablebookingid: hdntablebookingid,table: table,date:date,starttime:starttime,endtime: endtime},
					  success: function (data) {
              data = data.trim();
							if(data=='yes'){
								alert('Sorry! Already Booked!');
								return;
							}
							else if(data=='no'){
 								jQuery.ajax({
											type: "POST",
										  url: '<?php echo admin_url( 'admin-ajax.php' );?>',
											data: {
                        action: 'restbl_save_tablebooking',
                        hdntablebookingid: hdntablebookingid,tableid : tableid,table: table,date: date, start_time:starttime,end_time:endtime,first_name:first_name,last_name:last_name,email:email,phone:phone,details: details,tablebookingby: tablebookingby, noof_seat: noofseat  
                      },
											success: function (data) {
													if(data.length>0){
                            alert('added successfully');
                            window.location.href = "<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=tablebooking-calendar-menu";
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
			var atpos=email.indexOf("@");
			var dotpos=email.lastIndexOf(".");
			if (atpos < 1 || dotpos < atpos+2 || dotpos+2 >= email.length) {
					return false;
			}
			return true;
	}

	//===-----------------------add tablebooking dialog-------------------------------===
	//=--------------------------------------------------------------------------===
  </script>
  <style type="text/css">
	#frmtablebooking select, button, input, textarea {
		border:1px solid #E2E2E2;
		/*margin:5px;*/
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
	    /*-moz-box-shadow: 2px 2px 3px #666;
	    -webkit-box-shadow: 2px 2px 3px #666;
	    box-shadow: 2px 2px 3px #666;*/
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
	    /*-moz-box-shadow: 2px 2px 3px #666;
	    -webkit-box-shadow: 2px 2px 3px #666;
	    box-shadow: 2px 2px 3px #666;*/
	    font-size: 20px;
	    padding: 4px 7px;
	    outline: 0;
	    -webkit-appearance: none;
	}
	input.rounded:focus {
	    border-color: #4CB7FF;
	}
	.ui-dialog {
		z-index:3000!important;
	}
	/*-------theme css override------*/
	select, input[type="text"]{
		height: 35px;;
	}	
	
  </style>
  <?php $current_user = wp_get_current_user();
	?>
  
 <div id="addtablebooking_dialog" title="Add/Edit TableBooking" class="wrapper" style="display:none;z-index:5000">
  <div class="wrap" style="float:left; width:100%;">
    <div class="main_div">
     	<div class="metabox-holder" style="width:89%; float:left;">
        <form id="frmtablebooking" action="" method="post" style="width:100%">
          <table id="tbladdtablebookingbackpopup" style="margin:10px;width:500px;">
          	<tr>
            	<td class="tablebookinglavel"> <label for="table">Table </label></td>
              <td class="tablebookinginput" id="multi_tables_select" style="padding:0 0 5px 5px;">
              	<select id="opttables_multiselect" class="multiselect" multiple="multiple">
                  	<?php foreach($taxonomies as $taxo){?>
                    <option disabled="disabled" value="<?php echo $taxo->name;?>"><?php printf(__("%s","restbl-uststablebooking"), strtoupper($taxo->name));?></option>
                  <?php 
									$term_id = $taxo->term_id;
                    $sql_table = "select distinct * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id where (p.post_status = 'publish' or p.post_status = 'future') and p.post_type='custom_tablebooking' and tt.term_id=".$term_id." ";
									 
									$tables = $wpdb->get_results($sql_table);	
                    foreach($tables as $table){
                    ?>
                     <option value="<?php echo $table->ID;?>"><?php printf(__("%s","restbl-uststablebooking"), $table->post_title);?></option>
                     
               <?php 
                    }
               } ?>
                </select>
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	Date:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="dtpdate" name="dtpdate" value="" style="width:230px;" />
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	Start Time:
              </td>
              <td class="tablebookinginput">
              	<!--<input type="text" id="starttime" name="starttime" value="" style="width:230px;" /><br>-->
                <select name="opt_table_starthour" id="opt_table_starthour">
                  <option value="01" <?php if($table_starthour=='01') echo 'selected';?>>01</option>
                  <option value="02" <?php if($table_starthour=='02') echo 'selected';?>>02</option>
                  <option value="03" <?php if($table_starthour=='03') echo 'selected';?>>03</option>
                  <option value="04" <?php if($table_starthour=='04') echo 'selected';?>>04</option>
                  <option value="05" <?php if($table_starthour=='05') echo 'selected';?>>05</option>
                  <option value="06" <?php if($table_starthour=='06') echo 'selected';?>>06</option>
                  <option value="07" <?php if($table_starthour=='07') echo 'selected';?>>07</option>
                  <option value="08" <?php if($table_starthour=='08') echo 'selected';?>>08</option>
                  <option value="09" <?php if($table_starthour=='09') echo 'selected';?>>09</option>
                  <option value="10" <?php if($table_starthour=='10') echo 'selected';?>>10</option>
                  <option value="11" <?php if($table_starthour=='11') echo 'selected';?>>11</option>
                  <option value="12" <?php if($table_starthour=='12') echo 'selected';?>>12</option>
                  <option value="13" <?php if($table_starthour=='13') echo 'selected';?>>13</option>
                  <option value="14" <?php if($table_starthour=='14') echo 'selected';?>>14</option>
                  <option value="15" <?php if($table_starthour=='15') echo 'selected';?>>15</option>
                  <option value="16" <?php if($table_starthour=='16') echo 'selected';?>>16</option>
                  <option value="17" <?php if($table_starthour=='17') echo 'selected';?>>17</option>
                  <option value="18" <?php if($table_starthour=='18') echo 'selected';?>>18</option>
                  <option value="19" <?php if($table_starthour=='19') echo 'selected';?>>19</option>
                  <option value="20" <?php if($table_starthour=='20') echo 'selected';?>>20</option>
                  <option value="21" <?php if($table_starthour=='21') echo 'selected';?>>21</option>
                  <option value="22" <?php if($table_starthour=='22') echo 'selected';?>>22</option>
                  <option value="23" <?php if($table_starthour=='23') echo 'selected';?>>23</option>
                  <option value="24" <?php if($table_starthour=='24') echo 'selected';?>>24</option>
                </select>
                
                <select  name="opt_table_startminute" id="opt_table_startminute"> 
                  <option value="00" <?php if($table_startminute=='00') echo 'selected';?>>00</option>
                  <option value="05" <?php if($table_startminute=='05') echo 'selected';?>>05</option>
                  <option value="10" <?php if($table_startminute=='10') echo 'selected';?>>10</option>
                  <option value="15" <?php if($table_startminute=='15') echo 'selected';?>>15</option>
                  <option value="20" <?php if($table_startminute=='20') echo 'selected';?>>20</option>
                  <option value="25" <?php if($table_startminute=='25') echo 'selected';?>>25</option>
                  <option value="30" <?php if($table_startminute=='30') echo 'selected';?>>30</option>
                  <option value="35" <?php if($table_startminute=='35') echo 'selected';?>>35</option>
                  <option value="40" <?php if($table_startminute=='40') echo 'selected';?>>40</option>
                  <option value="45" <?php if($table_startminute=='45') echo 'selected';?>>45</option>
                  <option value="50" <?php if($table_startminute=='50') echo 'selected';?>>50</option>
                  <option value="55" <?php if($table_startminute=='55') echo 'selected';?>>55</option>
                  <option value="60" <?php if($table_startminute=='60') echo 'selected';?>>60</option>
                </select>
                <span style="font-style:italic;font-size:11px;">Example: 09:00</span>
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	End Time:
              </td>
              <td class="tablebookinginput">
              	<!--<input type="text" id="endtime" name="endtime" value="" style="width:230px;" /><br>-->
                <select name="opt_table_endhour" id="opt_table_endhour">
                  <option value="01" <?php if($table_endhour=='01') echo 'selected';?>>01</option>
                  <option value="02" <?php if($table_endhour=='02') echo 'selected';?>>02</option>
                  <option value="03" <?php if($table_endhour=='03') echo 'selected';?>>03</option>
                  <option value="04" <?php if($table_endhour=='04') echo 'selected';?>>04</option>
                  <option value="05" <?php if($table_endhour=='05') echo 'selected';?>>05</option>
                  <option value="06" <?php if($table_endhour=='06') echo 'selected';?>>06</option>
                  <option value="07" <?php if($table_endhour=='07') echo 'selected';?>>07</option>
                  <option value="08" <?php if($table_endhour=='08') echo 'selected';?>>08</option>
                  <option value="09" <?php if($table_endhour=='09') echo 'selected';?>>09</option>
                  <option value="10" <?php if($table_endhour=='10') echo 'selected'?>>10</option>
                  <option value="11" <?php if($table_endhour=='11') echo 'selected'?>>11</option>
                  <option value="12" <?php if($table_endhour=='12') echo 'selected'?>>12</option>
                  <option value="13" <?php if($table_endhour=='13') echo 'selected'?>>13</option>
                  <option value="14" <?php if($table_endhour=='14') echo 'selected'?>>14</option>
                  <option value="15" <?php if($table_endhour=='15') echo 'selected'?>>15</option>
                  <option value="16" <?php if($table_endhour=='16') echo 'selected'?>>16</option>
                  <option value="17" <?php if($table_endhour=='17') echo 'selected'?>>17</option>
                  <option value="18" <?php if($table_endhour=='18') echo 'selected'?>>18</option>
                  <option value="19" <?php if($table_endhour=='19') echo 'selected'?>>19</option>
                  <option value="20" <?php if($table_endhour=='20') echo 'selected'?>>20</option>
                  <option value="21" <?php if($table_endhour=='21') echo 'selected'?>>21</option>
                  <option value="22" <?php if($table_endhour=='22') echo 'selected'?>>22</option>
                  <option value="23" <?php if($table_endhour=='23') echo 'selected'?>>23</option>
                  <option value="24" <?php if($table_endhour=='24') echo 'selected'?>>24</option>
                </select>
                <select  name="opt_table_endminute" id="opt_table_endminute">
                  <option value="00" <?php if($table_endminute=='00') echo 'selected';?>>00</option>
                  <option value="05" <?php if($table_endminute=='05') echo 'selected';?>>05</option>
                  <option value="10" <?php if($table_endminute=='10') echo 'selected';?>>10</option>
                  <option value="15" <?php if($table_endminute=='15') echo 'selected';?>>15</option>
                  <option value="20" <?php if($table_endminute=='20') echo 'selected';?>>20</option>
                  <option value="25" <?php if($table_endminute=='25') echo 'selected';?>>25</option>
                  <option value="30" <?php if($table_endminute=='30') echo 'selected';?>>30</option>
                  <option value="35" <?php if($table_endminute=='35') echo 'selected';?>>35</option>
                  <option value="40" <?php if($table_endminute=='40') echo 'selected';?>>40</option>
                  <option value="45" <?php if($table_endminute=='45') echo 'selected';?>>45</option>
                  <option value="50" <?php if($table_endminute=='50') echo 'selected';?>>50</option>
                  <option value="55" <?php if($table_endminute=='55') echo 'selected';?>>55</option>
                  <option value="60" <?php if($table_endminute=='60') echo 'selected';?>>60</option>
                </select>
                <span style="font-style:italic;font-size:11px;">Example: 10:00</span>
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <!--<tr>
            	<td class="tablebookinglavel">
              	Time Shift:
              </td>
              <td class="tablebookinginput">
                <select id="timeshift" name="timeshift" style="height:21px;" >
                	<option value="am">AM</option>
                  <option value="pm">PM</option>
                </select>
              </td>
              <td class="tablebookingasterik"></td>
            </tr>-->
            <tr>
            	<td class="tablebookinglavel">
              	First Name:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtFirstName" name="txtFirstName" value="" />
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	Last Name:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtLastName" name="txtLastName" value="" />
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	Email:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtEmail" name="txtEmail" value="" /><!--<span style="color:red;">*</span>-->
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	Phone:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtPhone" name="txtPhone" value="" />
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	Details:
              </td>
              <td class="tablebookinginput">
              	<textarea cols="35" rows="10" id="details" name="details"></textarea>
              </td>
              <td class="tablebookingasterik"><span style="color:red;">*</span></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	TableBooking By:
              </td>
              <td class="tablebookinginput">
              	<input type="text" readonly="readonly" id="txttablebookingby" name="txttablebookingby" value="<?php echo $current_user->display_name; ?>" />
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	No of Seat:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtNoofSeat" name="txtNoofSeat" value="" />
              </td>
              <td class="tablebookingasterik"></td>
            </tr>
            <!--<tr>
            	<td class="tablebookinglavel">
              	Payment Method:
              </td>
              <td class="tablebookinginput">
              	<select id="optpaymentmethod" name="optpaymentmethod" >
                	</?php foreach($payment_methods as $pm){?>
                  	<option value="</?php echo $pm->payment_method;?>"></?php echo $pm->payment_method;?></option>
                  </?php }?>  
                </select>
              </td>
              <td class="tablebookingasterik"></td>
            </tr>-->
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