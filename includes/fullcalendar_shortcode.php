<?php  
  function restbl_ustscalendar_shortcode($atts){ 
	//if ( is_user_logged_in() ){
		global $table_prefix,$wpdb;
    
    $table = "";
    $shape = $_REQUEST['tblshape'];
    $tableid = $_REQUEST['tblid'];
    if(isset($_SESSION['tableid_front'])){
      $table = $_SESSION['tableid_front'];
      $tableid = $_SESSION['tableid_front'];
    }
		
		if($_POST){
			$table = $_REQUEST['opttables'];
      $shape = $_REQUEST['tblshape'];
      $tableid = $_SESSION['tableid_front'];
		}
		$sql = "";
		$sql_table = "";
		$tableinfo = "";
		$mintime = "";
		$maxtime = "";
		$interval = "";
		if($table==0){
			$sql = "select * from ".$table_prefix."restbl_uststablebookings where confirmed=1";
			$mintime = 0;
			$maxtime = 24;
			$interval = 30;
      $sql_table = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and pm.meta_key='_table_tableshape' and pm.meta_value='".$shape."' and p.ID=".$tableid;
	    $tableinfo = $wpdb->get_results($sql_table);
		}
		else{
			$sql = "select * from ".$table_prefix."restbl_uststablebookings where table_id like '%".$table."%' and confirmed=1";
      $sql_table = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and pm.meta_key='_table_tableshape' and pm.meta_value='".$shape."' and p.ID='".$tableid;
	   $tableinfo = $wpdb->get_results($sql_table);
		}
     $sql_taxonomy = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."terms t on tt.term_id = t.term_id where tt.taxonomy = 'restbl_custom_category' ORDER BY tt.term_id ASC";
	   $taxonomies = $wpdb->get_results( $sql_taxonomy );
    
    $booked_bg_color = restbl_uststablebooking_get_opt_val('_booked_bg_color',GEN_CCB_BOOKED_BG_COLOR);
		$uststablebookings = $wpdb->get_results($sql);
    
    unset($_SESSION['tableid_front']);
    
		$output = "<style type='text/css'>";
    include_once GEN_USTSTABLEBOOKING_DIR.'operations/get_cssfixfront.php';
		$output .= '</style>
      <script type="text/javascript">
      jQuery(document).ready(function(){
        /*jQuery(".multiselect").multipleSelect({
          placeholder: "Please select Table",
          selectAll: false,
          width:"69%",
          onClick: function(view){
            //evnt_get_eventprice();
          }
        });*/
    });  
			function restbl_submit_form(){
				var table = jQuery("#opttables").val();
				var sel = jQuery("option[value=" + table + "]", $("select[name=opttables]") );
				if (sel.length > 0){
					sel.attr("selected", "selected");
				}
				var table = jQuery("select[name=opttables] option:selected").text();
				//jQuery("#frmtables").submit();
			}
		</script>
		<style type="text/css">
				#calendar {
					/*max-width: 800px;*/
					/*margin: 0 auto;*/
					/*margin: 30px;*/
					}
          .ui-dialog{
            z-index:10000!important;
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
					table{
						margin:0!important;
					}
          /*---------*/
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
          /*=-----------*/
				</style>';
				$postid = $tableinfo[0]->post_id;
        //die(print_r($tableinfo));
        //echo '$postid: '.$postid;
        $postid = $tableid;
        $tbl_img = get_post_meta($postid, '_table_image', true);
        $noofseat = get_post_meta($postid, '_table_noofseat', true);
        $airconditioned = get_post_meta($postid, '_table_airconditioned', true);
        $description = get_post_meta($postid, '_table_description', true);
			if($tableid){	
				$output .='<div style="">
				<div id="table_details">
					<div style="float:left;width:49%;"><img src="'.$tbl_img.'" style="width:400px;height:300px;" /> </div>
					<div style="float:left;width:49%;padding-left:10px;">
					  Table: '.$tableinfo[0]->post_title.'<br>
					  No of Seat: '.$noofseat.' <br>
					  Air Conditioned: '.$airconditioned.' <br>  
					  Table Shape: '.$tableinfo[0]->meta_value.' <br>
					  Description: '.$description.'<br>
					</div> 
				</div>';
			}  
            $output .='<div style="clear:both;padding-top:50px;">
              <div style="float:left;">Tables: </div>
              <div style="float:left; width:50%;" id="multi_tables_select" >
              <form id="frmtables" method="post">
                <select id="opttables" name="opttables"  >
                <option value="0">All</option>
                ';	
                  foreach($taxonomies as $taxo){
                    $output .= '<option disabled="disabled" value="'.$taxo->name.'">'.strtoupper($taxo->name).'</option>';
                    $term_id = $taxo->term_id;
                    $sql_table = "select distinct * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id where (p.post_status = 'publish' or p.post_status = 'future') and p.post_type='custom_tablebooking' and tt.term_id=".$term_id." ";
                    $tables = $wpdb->get_results($sql_table);	
                    foreach($tables as $table){
                      $output .= '<option value="'.$table->ID.'">'.$table->post_title.'</option>';
                    }
                  }
                $output .='</select> 
              </form>
              </div>
              <div style="clear:both"></div>
              <div id="calendar"></div>
              <div style="clear:both"></div>
           </div> 
      	</div>
				<!--<div id="calendar"></div>-->
				';
				
				//========================================================== Add tablebooking Popup =============================================================
				include_once('add_tablebooking_front_popup.php');	
				//================================================================================================================================================
				$output .= "<script type='text/javascript'>
				function restbl_generate_calendar(){
					jQuery('#calendar').fullCalendar({
						header: {
							left: 'prev, next today, agenda',
							center: 'title',
							right: 'month,agendaWeek,agendaDay'
						},
						defaultView: 'agendaWeek',
						theme:true,
						selectable: true,
						selectHelper: true,
						editable: true,
						allDayDefault: false,
						dayClick: function(date, allDay, jsEvent, view) {
								jQuery('#dtpdate').val(jQuery.datepicker.formatDate('yy-mm-dd',date)); 
                jQuery('#addtablebooking_dialog').dialog('open');
						},
						events: [";
						foreach($uststablebookings as $tablebooking){
						$output .="
                    {
                      id: '".$tablebooking->tablebooking_id."',
                      title: ' ".$tablebooking->table."->".$tablebooking->start_time.$tablebooking->timeshift."-".$tablebooking->end_time.$tablebooking->timeshift."', 
                      start: '".$tablebooking->date." ".$tablebooking->start_time."',
                      end: '".$tablebooking->date." ".$tablebooking->end_time."',
                      backgroundColor : '#".$booked_bg_color."',
                      editable: true
                      
                    },";
						}	
						$output .="],
						minTime:0,
						maxTime:24,
						slotMinutes:30,
						eventColor: '#F05133'
					});
				}
				jQuery(document).ready(function() {
						restbl_generate_calendar();";
            if(isset($_REQUEST["opttables"])){
              $output .="jQuery('#opttables').val('".$_REQUEST["opttables"]."');";
            }
						$output .="jQuery('#addtablebooking_dialog').dialog({
								autoOpen: false,
								height: 603,
								width: 550,
								modal: true,
                zIndex: 10000,
								buttons: {
										'Request TableBooking': function () {
												if(restbl_save_tablebooking()){
													jQuery(this).dialog('close');
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
				</script>";

			return $output;		
	
	}
	add_shortcode('gen_restbl_ustscalendar','restbl_ustscalendar_shortcode');