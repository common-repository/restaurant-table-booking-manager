<?php 
  global $table_prefix,$wpdb;
  $table = "";
  if(isset($_SESSION['tableid'])){
    $table = $_SESSION['tableid'];
  }
	
	if($_POST){
		$table = $_REQUEST['opttables'];
	}
	$sql = "";
	$sql_table = "";
	$tvs_result = "";
	$mintime = "";
	$maxtime = "";
	$interval = "";
	if($table==0){
		$sql = "select * from ".$table_prefix."restbl_uststablebookings where confirmed=1";
		//echo '<br >its all';
		$mintime = 0;
	  $maxtime = 24;
	  $interval = 30;
	}
	else{
		$sql = "select * from ".$table_prefix."restbl_uststablebookings where table_id like '%".$table."%' and confirmed=1";
		//echo '<br> other options';
		$sql_table = "select scd.id as tableid,srv.id as serviceid,tmsl.id as timeslotid,vn.id as venueid, scd.* ,srv.*,tmsl.*,vn.* from ".$table_prefix."restbl_tables scd inner join ".$table_prefix."restbl_services srv on scd.service = srv.id 
inner join ".$table_prefix."restbl_timeslot tmsl on tmsl.id = scd.timeslot
inner join ".$table_prefix."restbl_venues vn on vn.id = scd.venue 
where scd.id=".$table;
		$tvs_result = $wpdb->get_results($sql_table);
		$mintime = $tvs_result[0]->mintime;
		$maxtime = $tvs_result[0]->maxtime;
		$interval = $tvs_result[0]->time_interval;	
	}
	
	$uststablebookings = $wpdb->get_results($sql);
	//==========day calculation=============
  $booked_bg_color = restbl_uststablebooking_get_opt_val('_booked_bg_color',GEN_CCB_BOOKED_BG_COLOR);
  
  $sql_taxonomy = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."terms t on tt.term_id = t.term_id where tt.taxonomy = 'restbl_custom_category' ORDER BY tt.term_id ASC";
	$taxonomies = $wpdb->get_results( $sql_taxonomy );
	?>
  <script type="text/javascript">
    jQuery(document).ready(function(){
    });  
		function restbl_submit_form(){
			var table = jQuery('#opttables').val();
			var sel = jQuery("option[value=" + table + "]", jQuery("select[name=opttables]") );
			if (sel.length > 0){
				sel.attr('selected', 'selected');
			}
			var table = jQuery("select[name=opttables] option:selected").text();
		}
	</script>
  <style type='text/css'>
	 #calendar {
			max-width: 800px;
			/*margin: 0 auto;*/
			margin-top: 10px;
		}
		.event {
      /*shared event css*/
		}
		.greenEvent {
				background-color:#00FF00;
		}
		.redEvent {
				background-color:#FF0000;
		}
		#wpfooter {
			position:relative;
		}	
    /*--------------------------------*/
    #addbooking_backend .multiselect {
      text-align: left;
    }
    #addbooking_backend .multiselect-container li.active .checkbox{
      background-color:#3A83C2;
    }
    #multi_tables_select input{
      width: 20%;
      margin-left:10px;
      position:static;
    }
    #multi_tables_select label{
      padding-left:0px;
    }
    /*-------------------------------*/
  </style>
  <div style="height:auto;">
      <div id="icon-options-general" class="icon32">
      </div>
      <h2 style="padding-top:10px;">TableBooking Calendar</h2>
      <div style="height:15px;"></div>
      <div style="padding-left:30px;">
        <div style="float:left;">Tables: </div>
        <div style="float:left; width:50%;" id="multi_tables_select">
        <form id="frmtables" method="post">
          <select id="opttables" name="opttables" > 
           	<?php foreach($taxonomies as $taxo){?>
                <option disabled="disabled" value="<?php echo $taxo->name;?>"><?php printf(__("%s","restbl-uststablebooking"), strtoupper($taxo->name));?></option>
                    <?php 
                    $term_id = $taxo->term_id;
                    $sql_table = "select distinct * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id where (p.post_status = 'publish' or p.post_status = 'future') and p.post_type='custom_tablebooking' and tt.term_id=".$term_id." ";
                    $tables = $wpdb->get_results($sql_table);	
                    foreach($tables as $table){
                    ?>
                     <option value="<?php echo $table->ID;?>"><?php printf(__("%s","restbl-uststablebooking"), $table->post_title);?></option>
                    <?php } ?>
               <?php } ?>
					 
          </select>
          
        </form>
        </div>
        <div style="clear:both"></div>
        <div id='calendar' style="clear:both;"></div>
        <div style="clear:both"></div>
      </div>
      <?php include_once('includes/add_tablebooking.php');?>
  </div>
  <div style="clear:both;"></div>
  
  <script type='text/javascript'>
	function restbl_get_tablebookings(){
		var table = jQuery('#opttables').val();
		jQuery.ajax({
				type: "POST",
        url: '<?php echo admin_url( 'admin-ajax.php' );?>',
				data: {
          action: 'restbl_get_tablebookings_by_table',  
          table: table
        },
				//dataType:'json',
				success: function (data) {
					console.log(data);
				},
				error : function(s , i , error){
					console.log(error);
				}
				
		});
	}	
		
	function restbl_generate_calendar(){
		 jQuery('#calendar').fullCalendar({
			header: {
				left: 'prev, next today, agenda',
				center: 'title',
				//right: 'month, agendaWeek, agendaDay'
				right: 'month, agendaWeek, agendaDay'
			},
			defaultView: 'agendaWeek',
			theme:true,
			selectable: true,
			selectHelper: true,
			editable: true,
			allDayDefault: false,
			dayClick: function(date, allDay, jsEvent, view) {
					 jQuery('#dtpdate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
					 //jQuery('#dtptodate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
					 jQuery("#addtablebooking_dialog").dialog("open");
			},
			events: [
			<?php  //die(print_r($uststablebookings));
      foreach($uststablebookings as $tablebooking){ ?>
				{
					id: <?php echo $tablebooking->tablebooking_id;?>,
					title: '<?php echo $tablebooking->table.'->'.$tablebooking->start_time.$tablebooking->timeshift.'-'.$tablebooking->end_time.$tablebooking->timeshift; ?>',
					start: '<?php echo $tablebooking->date.' '.$tablebooking->start_time;?>',
					end: '<?php echo $tablebooking->date.' '.$tablebooking->end_time;?>',
					backgroundColor : '#<?php echo $booked_bg_color;?>',
					editable: true
          
				},
			  <?php } ?>	
			],
      minTime: 0,
			maxTime: 24,
			slotMinutes: 30,
			eventColor: '#F05133'
		});
	}
	function generate_calendar_on_ajaxcall(tablebookings){
		jQuery('#calendar').fullCalendar({
				header: {
					left: 'prev, next today, agenda',
					center: 'title',
					//right: 'month, agendaWeek, agendaDay'
					right: 'month, agendaWeek, agendaDay'
				},
				defaultView: 'agendaWeek',
				theme:true,
				selectable: true,
				selectHelper: true,
				editable: true,
				dayClick: function(date, allDay, jsEvent, view) {
						 jQuery('#dtpfromdate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
						 jQuery('#dtptodate').val(jQuery.datepicker.formatDate('yy-mm-dd',date));
						 jQuery("#addtablebooking_dialog").dialog("open");
				},
				events: [
				],
				minTime: 0,
				maxTime: 24,
				slotMinutes: 30,
				eventColor: '#F05133'
			});
	}
	jQuery(document).ready(function() {
		
		restbl_generate_calendar();
		<?php if(isset( $_REQUEST['opttables'])){?>
        jQuery('#opttables').val(<?php echo $_REQUEST['opttables']?>);
    <?php } ?>

    jQuery("#addtablebooking_dialog").dialog({
					autoOpen: false,
					height: 600,
					width: 550,
					modal: true,
					buttons: {
							'Request TableBooking': function () {
									//jQuery(this).dialog('close');
									if(restbl_save_tablebooking()){
										jQuery(this).dialog("close");
									}
									else{
									}
							},
							Cancel: function () {
									jQuery(this).dialog('close');
									restbl_cleardata();
							}
					},
					close: function () {
						restbl_cleardata();
					}
			});
	});
	
 </script>