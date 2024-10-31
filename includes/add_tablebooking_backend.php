<?php
	global $table_prefix,$wpdb;
	$table = 0;
	if($_POST){
		//$schedule = $_REQUEST['tables_select'];
    $table = $_REQUEST['id'];
	}
	$sql_taxonomy = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."terms t on tt.term_id = t.term_id where tt.taxonomy = 'restbl_custom_category'";
  //die($sql_taxonomy);
	$taxonomies = $wpdb->get_results( $sql_taxonomy );
  //echo "<pre>";
  //die(print_r($taxonomies));
	$sql_paymentmethod = "select * from ".$table_prefix."restbl_uststablebookings_paymentmethods";
	$payment_methods = $wpdb->get_results( $sql_paymentmethod );
	
	/*$sql_schedule = "select scd.id as scheduleid,srv.id as serviceid,tmsl.id as timeslotid,vn.id as venueid, scd.* ,srv.*,tmsl.*,vn.* from ".$table_prefix."restbl_schedules scd inner join ".$table_prefix."restbl_services srv on scd.service = srv.id 
inner join ".$table_prefix."restbl_timeslot tmsl on tmsl.id = scd.timeslot
inner join ".$table_prefix."restbl_venues vn on vn.id = scd.venue 
where scd.id=".$schedule;*/
	//$tvs_result = $wpdb->get_results($sql_schedule);
	
  $table_starthour = 0;
  $table_startminute = 0;
  $table_endhour = 0;
  $table_endminute = 0;
  
  if(isset($_REQUEST['id'])){
      $id = $_REQUEST['id'];
      global $table_prefix,$wpdb;
      $sql = "select * from ".$table_prefix."restbl_uststablebookings where tablebooking_id=".$id;
      $result = $wpdb->get_results($sql);
      //die(print_r($result[0]->start_time));
      $start_time = $result[0]->start_time;
      $starttime_arr = explode(":",$start_time);
      $table_starthour = $starttime_arr[0];
      $table_startminute = $starttime_arr[1];
      //die(print_r($table_startminute));
      $end_time = $result[0]->end_time;
      $endtime_arr = explode(":",$end_time);
      $table_endhour = $endtime_arr[0];
      $table_endminute = $endtime_arr[1];
      //die(print_r($table_endminute));
  }             
              
	?>
  <style type="text/css">
		.multiselect {
			text-align: left;
		}
		.multiselect-container li.active .checkbox{
			background-color:#3A83C2;
		}
		li .active{
			color: red;
		}
		li {
			margin-bottom: 0px;
		}
		#namediv input{
			width: 70%;
		}
    #namediv ul li input{
			width: 10%;
		}
	</style>
	<script>
	//--------------form validation------------------
	//-----------------date picker------------------------------
  /*var venue_service_schedule = Array();
  <//?php if(isset($tvs_result[0])){?>
      venue_service_schedule = <//?php echo json_encode($tvs_result[0]);?>;
  <//?php } ?>*/
  <?php $dayson = get_option('_restbl_booking_days');?>  
	var dayscond = "";
  var days = "<?php echo $dayson ?>";
	if(days == "" || days == null ){
		dayscond = "1";
	}
	else{
    
		var days = "<?php echo $dayson ?>";
		var daysarr = days.split(',');
		
		for(var i=0; i<daysarr.length; i++){
			if(i==(daysarr.length-1)){
				dayscond = dayscond + 'date.getDay() == '+daysarr[i]+'';
			}
			else{
				dayscond = dayscond + 'date.getDay() == '+daysarr[i]+' || ';
			}
		}
	}
	
	jQuery(function() {
    //jQuery( "#dtpdate" ).datepicker({ dateFormat: "yy-mm-dd" });
		jQuery("#dtpdate").datepicker({
      dateFormat: "yy-mm-dd",
			beforeShowDay: function(date){ 
				//if(date.getDay() == 1 || date.getDay() == 2 || date.getDay() == 3 || date.getDay() == 4){
				if(eval(dayscond)){
					return [1];
				}
				else{
					return [0];
				}
			
			}
		
		});
		//jQuery( "#dtptodate" ).datepicker({ dateFormat: "yy-mm-dd" });
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
	function restbl_get_rooms_for_tablebookingcell(roomid){
		  var term_id = jQuery('#roomtype').val();
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					dataType:'json', 
					data: {
            action: 'restbl_get_room_bycat',  
            term_id:term_id
          },
					success: function (data) {
							var count = data.length;
							jQuery('#optroom').empty();
							if(data.length > 0 ){
								for(var i=0;i<data.length;i++){
										if(i==0){
											jQuery('#optroom').append('<option value="'+data[i]['ID']+'" selected="selected">'+data[i]['post_title']+'</option>');
										}
										else{
											jQuery('#optroom').append('<option value="'+data[i]['ID']+'">'+data[i]['post_title']+'</option>');
										}
								}
								restbl_get_roomprice();
							}
							else{
								jQuery('#optroom').empty();
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			}).done(function(msg){
					jQuery('#optroom').val(roomid);
			});
	}
	function restbl_get_rooms_for_edittablebooking(roomid){
		  //alert(roomid);
		  var term_id = jQuery('#roomtype').val();
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					dataType:'json', 
					data: {
            action: 'restbl_get_room_bycat',
            term_id:term_id
          },
					success: function (data) {
							var count = data.length;
							jQuery('#optroom').empty();
							if(data.length > 0 ){
								for(var i=0;i<data.length;i++){
										if(i==0){
											jQuery('#optroom').append('<option value="'+data[i]['ID']+'" selected="selected">'+data[i]['post_title']+'</option>');
										}
										else{
											jQuery('#optroom').append('<option value="'+data[i]['ID']+'">'+data[i]['post_title']+'</option>');
										}
								}
								restbl_get_roomprice();
							}
							else{
								jQuery('#optroom').empty();
							}
					},
					error : function(s , i , error){
							console.log(error);
					}
			}).done(function(msg){
					jQuery('#optroom').val(roomid);
			});
	}
	function restbl_get_serviceprice(){
			var arr_schedules = new Array();
			var schedule = jQuery('#tables_select').val();
			
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					data: {
            action: 'restbl_get_serviceprice_by_schedule',
            schedule: schedule
          },
					success: function (data) {
							var count = data.length;
							jQuery('#txtCustomPrice').val(data);
					},
					complete: function (data){
						//restbl_calculate_due();
					},
					error : function(s , i , error){
							console.log(error);
					}
			});
	}
	jQuery(document).ready(function(){
      jQuery('.multiselect').multipleSelect({
				placeholder: '<?php _e("Please select Table","restbl-uststablebooking"); ?>',
				selectAll: false,
				width:'39%',
				onClick: function(view){
					//evnt_get_eventprice();
				}
			});
			//----------------multiselect combo---------------
			  ///restbl_get_serviceprice();
			//-------------------------------------------------
			var calltype = restbl_getUrlVars()["calltype"];
			//alert(calltype);
			if(calltype){
				if(calltype = 'edittablebooking'){
					<?php
          if(isset($_REQUEST['id'])){
              $id = $_REQUEST['id'];
              global $table_prefix,$wpdb;
              $sql = "select * from ".$table_prefix."restbl_uststablebookings where tablebooking_id=".$id;
              $result = $wpdb->get_results($sql);
              //$start_time = $result[0]['start_time'];
              //die(print_r($start_time));
              ?>
              var tablebooking = <?php echo json_encode($result[0]);?>;
              jQuery('#hdntablebookingid').val(tablebooking['tablebooking_id']);
              jQuery("#tables_select option[value="+tablebooking['schedule_id'] +"]").attr("selected","selected");

              jQuery('#dtpdate').val(tablebooking['date']);
              jQuery('#starttime').val(tablebooking['start_time']);
              jQuery('#endtime').val(tablebooking['end_time']);

              jQuery('#timeshift').val(tablebooking['timeshift']);
              jQuery('#txtFirstName').val(tablebooking['first_name']);
              jQuery('#txtLastName').val(tablebooking['last_name']);
              jQuery('#txtEmail').val(tablebooking['email']);

              jQuery('#txtPhone').val(tablebooking['phone']);
              jQuery('#details').val(tablebooking['details']);
              jQuery('#txttablebookingby').val(tablebooking['tablebooking_by']);
              jQuery('#txtCustomPrice').val(tablebooking['custom_price']);
              jQuery('#txtNoofSeat').val(tablebooking['noof_seat']);
              jQuery("#optpaymentmethod option:selected").text(tablebooking['payment_method']);
          <?php } ?>
				}	
			}
			
      //---------------------------------	
			jQuery('#tables_select').on("change",function(){
				restbl_get_serviceprice();
			});
			//----save tablebooking----
			jQuery('#frmtablebooking').on('submit',function(e){
        //alert('here....');
	  		 e.preventDefault();
				 restbl_save_tablebooking();
			});
			//---------------------------
			<?php if(isset($_REQUEST['calendarcell'])){
			$calendarcell = $_REQUEST['calendarcell'];
			$calendarcell_data = explode("|",$calendarcell);
			$cell_month_cat = $calendarcell_data[0];
			$cell_month = $calendarcell_data[1];
			$cell_date =  $calendarcell_data[2];
			//die('-'.$cell_month);
			?>
				//alert(<?php// echo $cell_month;?>);
					jQuery("#tables_select").multiselect("select",<?php echo $cell_month;?>);
					restbl_get_roomprice();
					jQuery('#roomtype').val(<?php echo $cell_month_cat;?>);
					restbl_get_rooms_for_tablebookingcell(<?php echo $cell_month;?>);
					//jQuery('#optroom').val(<?php //echo $cell_month;?>);
					jQuery('#dtpfromdate').val('<?php echo $cell_date;?>');
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
			var hdntablebookingid = jQuery('#hdntablebookingid').val();
			var table = jQuery('#tables_select :selected').text();
      var tableid = jQuery('#tables_select :selected').val();
			//alert(schedule); return;
			var date = jQuery('#dtpdate').val();
      //alert(date);
			//var to_date = jQuery('#dtptodate').val();
      var starthr = jQuery('#opt_table_starthour').val();
      var startmin = jQuery('#opt_table_startminute').val();
			var starttime = starthr+':'+startmin;
      var endhr = jQuery('#opt_table_endhour').val();
      var endmin = jQuery('#opt_table_endminute').val();
			var endtime = endhr +':'+endmin;
      
			//var timeshift = jQuery('#timeshift').val();
      //alert('TimeShift: '+ timeshift);
			var first_name = jQuery('#txtFirstName').val();
			var last_name = jQuery('#txtLastName').val();
			var email = jQuery('#txtEmail').val();
			var phone = jQuery('#txtPhone').val();
			var details = jQuery('#details').val();
			var tablebookingby = jQuery('#txttablebookingby').val();
			//var guest_type = jQuery('#optguest_type').val();
			//var price = jQuery('#txtCustomPrice').val();
			//var payment_method = jQuery('#optpaymentmethod').find('option:selected').val();
      var noofseat = jQuery('#txtNoofSeat').val();
			
      //var regex='/^(2[0-3])|[01][0-9]:[0-5][0-9]$/';
      //regex.test([contents of input element]);
      
      if(table == ""){
				alert('Please choose at Least a Table .');
				return;
			}
			else if(date==""){
				alert('Please choose a date.');
				return;
			}
			
      /*else if(starttime==""){
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
				return;
			}
			jQuery.ajax({
					type: "POST",
          url: '<?php echo admin_url( 'admin-ajax.php' );?>',
					data: {
            action: 'restbl_check_tablebooking',
            hdntablebookingid: hdntablebookingid,table: table,date:date,starttime:starttime,endtime: endtime
          },
					success: function (data) {
							//console.log(data);
              data = data.trim();
							if(data=='yes'){
								alert('Sorry! Already Booked!');
								return;
							}
							else if(data=='no'){
								//alert('available!'); 
                //alert(timeshift);return;
 								jQuery.ajax({
											type: "POST",
                      url: '<?php echo admin_url( 'admin-ajax.php' );?>',
											data: {
                        action: 'restbl_save_tablebooking',
                        hdntablebookingid: hdntablebookingid,tableid:tableid,table: table,date: date, start_time:starttime,end_time:endtime, first_name:first_name,last_name:last_name,email:email,phone:phone,details: details,tablebookingby: tablebookingby, noof_seat: noofseat 
                      },
											success: function (data) {
													//console.log(data);
                          //alert('2nd functi: '+data); return;
                          if(data.length>0){
														alert('added successfully');
                            jQuery('#dtpdate').val('');
                            jQuery('#starttime').val('');
                            jQuery('#endtime').val('');
                            jQuery('#txtFirstName').val('');
                            jQuery('#txtLastName').val('');
                            jQuery('#txtEmail').val('');
                            jQuery('#txtPhone').val('');
                            jQuery('#details').val('');
                            jQuery('#txtNoofSeat').val('');
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
			//return;
			//----
			
	}
	function restbl_validateEmail(email) {
			var atpos=email.indexOf("@");
			var dotpos=email.lastIndexOf(".");
			if (atpos < 1 || dotpos < atpos+2 || dotpos+2 >= email.length) {
					//alert("Not a valid e-mail address");
					return false;
			}
			return true;
	}
	function restbl_calculate_due(){
		var price = jQuery('#txtCustomPrice').val();
		var paid = jQuery('#txtPaid').val();
		var due = (price - paid);
		//alert(due);
		jQuery('#txtDue').val(due); 
	}
  </script>
  
  <style type="text/css">
		.tablebookinglavel{
			width:16%;
		}
		.tablebookinginput{
			width:75%;
		}
	</style>
  <?php $current_user = wp_get_current_user();
	//die(print_r($current_user ));
	?>	  
  <div class="wrapper">
  <div class="wrap" style="float:left; width:100%;">
    <div id="icon-options-general" class="icon32"><br />
    </div>
    <h2><?php _e("TableBooking","restbl-uststablebooking"); ?></h2>
    <div class="main_div">
     	<div class="metabox-holder" style="width:69%; float:left;">
        <div id="namediv" class="stuffbox" style="width:99%;">
        <h3 class="top_bar"><?php _e("Add TableBooking","restbl-uststablebooking"); ?></h3>
        <form id="frmtablebooking" action="" method="post" novalidate="novalidate">
          <table style="margin:10px;width:100%;">
          	<tr>
            	<td class="tablebookinglavel"><?php _e("Tables","restbl-uststablebooking"); ?></td>
              <td class="tablebookinginput" id="multi_tables_select">
              	<!--<select id="tables_select" class="multiselect" multiple="multiple" >-->
                <select id="tables_select" class="multiselect" multiple="multiple" >
                  <?php foreach($taxonomies as $taxo){?>
                      <option disabled="disabled" value="<?php echo $taxo->name;?>"><?php printf(__("%s","restbl-uststablebooking"), strtoupper($taxo->name));?></option>
                      <?php 
						$term_id = $taxo->term_id;
						//$sql_table = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and p.post_type='custom_tablebooking' and tt.term_id=".$term_id." ";
            $sql_table = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id where p.post_status = 'publish' and p.post_type='custom_tablebooking' and tt.term_id=".$term_id." ";
            //die('='+$sql_table);
            //echo $sql_table;
            //die();
						 $tables = $wpdb->get_results($sql_table);	
						 foreach($tables as $table){
						?>
                        	<option value="<?php echo $table->ID;?>"><?php printf(__("%s","restbl-uststablebooking"), $table->post_title);?></option>
                        <?php } ?>
                      <?php  } ?>
                          
                          
                  <?php 
									 //$sql_schedules = "select * from ".$table_prefix."restbl_schedules";
									 
									 //$schedules = $wpdb->get_results($sql_schedules);	
									 //foreach($schedules as $schedule){
									?>
                  	<!--<option value="<?php //echo $schedule->id;?>"><?php //echo $schedule->schedule_name;?></option>-->
                  <?php //} ?>
                </select><span style="color:red;">*</span>
              </td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("Date","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="dtpdate" name="dtpdate" value="" style="width:230px;" /><span style="color:red;">*</span>
              </td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("Start Time","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<!--<input type="text" id="starttime" name="starttime" value="" style="width:230px;" /><span style="color:red;">*</span><br>-->
                <?php //die(print_r($table_starthour)); ?>
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
                <span style="font-style:italic;clear:both;font-size:11px;">Example: 09:00</span>
              </td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("End Time","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<!--<input type="text" id="endtime" name="endtime" value="" style="width:230px;" /><span style="color:red;">*</span><br>-->
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
                <span style="font-style:italic;clear:both;font-size:11px;">Example: 10:30</span>
              </td>
            </tr>
            <!--<tr>
            	<td class="tablebookinglavel">
              	Time Shift:
              </td>
              <td class="tablebookinginput">
                <!--<input type="text" id="txtampm" name="txtampm" value="" /> -- >
                <select id="timeshift" name="timeshift" style="height:21px;" >
                	<option value="am">AM</option>
                  <option value="pm">PM</option>
                </select>
              </td>
            </tr>-->
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("First Name","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtFirstName" name="txtFirstName" value="" />
              </td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("Last Name","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtLastName" name="txtLastName" value="" />
              </td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("Email","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtEmail" name="txtEmail" value="" /><!--<span style="color:red;">*</span>-->
              </td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("Phone","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtPhone" name="txtPhone" value="" /><span style="color:red;">*</span>
              </td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("Details","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<textarea cols="57" rows="15" id="details" name="details"></textarea>
              </td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("TableBooking By","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<input type="text" readonly="readonly" id="txttablebookingby" name="txttablebookingby" value="<?php echo $current_user->display_name; ?>" />
              </td>
            </tr>
            <tr>
            	<td class="tablebookinglavel">
              	<?php _e("No of Seat","restbl-uststablebooking"); ?>:
              </td>
              <td class="tablebookinginput">
              	<input type="text" id="txtNoofSeat" name="txtNoofSeat" value="" />
              </td>
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
            </tr>-->
            <tr>
            	<td class="tablebookinglavel" colspan="2" style="height:15px;">
              </td>
            </tr>
            <tr>
            	<td></td>
              <td>
              <input type="submit" id="btnaddtablebooking" name="btnaddtablebooking" value="Request TableBooking" style="width:170px;background-color:#0074A2;"/>
              <input type="hidden" id="hdntablebookingid" name="hdntablebookingid" value="" style="width:150px;"/>
              </td>
            </tr>
          </table>
          </form>
          
    		</div>
      </div>
    </div>
   </div>
  </div>