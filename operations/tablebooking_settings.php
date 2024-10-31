<?php 
//define('CCB_PROCESSING_BG_COLOR','7FCA27') ;
//define('GEN_CCB_BOOKED_BG_COLOR','138219') ;
/*$options = array (
								 '_processing_bg_color',
								 '_booked_bg_color'
								 );*/
$options = array (
								 '_booked_bg_color'
								 );

if(isset($_REQUEST['reset'])){
  if($_REQUEST['reset']){
    foreach($options as $opt){
      delete_option ($opt);
      $_POST[$opt]='';
      add_option( $opt, $_POST[$opt] );	
    }
  }
}
								 
if ( count($_POST) > 0 && isset($_POST['savesettings']) ){
	foreach($options as $opt ){
			delete_option ( $opt, $_POST[$opt] );
			add_option ( $opt, $_POST[$opt] );
	}
	
}

//$processing_bg_color = restbl_uststablebooking_get_opt_val('_processing_bg_color',CCB_PROCESSING_BG_COLOR); 
$booked_bg_color = restbl_uststablebooking_get_opt_val('_booked_bg_color',GEN_CCB_BOOKED_BG_COLOR); 

?>
<script type="text/javascript">
  jQuery(document).ready(function(){
    var daysstr = '';
    var days = '';
    <?php
    $bookingdays = "";
    if(get_option('_restbl_booking_days') != "" || get_option('_restbl_booking_days') != NULL){
      $bookingdays = get_option('_restbl_booking_days');
      ?>
      var daysstr = "<?php echo $bookingdays?>";
      var days = daysstr.split(',');  
      <?php
      }
    ?>
    //var daysstr = <?php echo $bookingdays?>;
    //var days = daysstr.split(',');
    jQuery('#days').val(days);
    
    jQuery('#savesettings').click(function(e){
      e.preventDefault();
      alert('main function..');
      restbl_save_settings();
    })
  });
  
  function restbl_save_settings(){
		alert('save function..1.');
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
    
    if(days ==""){
			alert('Please input Table Booking Days.');
			return;	
		}
		
		jQuery.ajax({
				type: "POST",
						url: '<?php echo admin_url( 'admin-ajax.php' );?>',
						data: {
							action: 'restbl_add_settings_ajax_request',	
							days: days
						},
					success: function (data) {
						if(data.length>0){
							alert('added successfully');
						}
				},
				error : function(s , i , error){
						console.log(error);
				}
		});

	}
</script>
<div>
  <div id="icon-link-manager" class="icon32"></div>
  <h2>Settings</h2><br>
  <div id="namediv" class="stuffbox" style="width:45%;min-height:187px;">
		<h3 class="top_bar" style="padding:8px;">TablementBooking Settings</h3>
    	
      <form id="frmtablebookingsettings" action="" method="post">
      	<table>
        <!--	<tr>
          	<td>Processing Background: </td>
            <td><input class="color" type="text" name="_processing_bg_color" id="_processing_bg_color" value="<//?php echo $processing_bg_color;?>" /></td>
          </tr>-->
          <tr>
            <td><?php _e("Booking Days","restbl-uststablebooking"); ?></td>
            <td>
              <select name="days" id="days" multiple>
                <option value="0"><?php _e("Sun","restbl-uststablebooking"); ?></option>
                <option value="1"><?php _e("Mon","restbl-uststablebooking"); ?></option>
                <option value="2"><?php _e("Tue","restbl-uststablebooking"); ?></option>
                <option value="3"><?php _e("Wed","restbl-uststablebooking"); ?></option>
                <option value="4"><?php _e("Thu","restbl-uststablebooking"); ?></option>
                <option value="5"><?php _e("Fri","restbl-uststablebooking"); ?></option>
                <option value="6"><?php _e("Sat","restbl-uststablebooking"); ?></option>
              </select><span style="color:red;">*</span> 
              <br>
              <p style="font-style:italic;font-size:11px;">[<?php _e("Hold down the Ctrl (windows) / Command (Mac) button to select multiple options","restbl-uststablebooking"); ?>.]</p>
            </td>
          </tr>
          <tr>
          	<td>Booked Background: </td>
            <td><input class="color" type="text" name="_booked_bg_color" id="_booked_bg_color" value="<?php echo $booked_bg_color;?>" /></td>
          </tr>
          <tr>
          	<td></td>
            <td></td>
          </tr>
        </table>
        <div style="float:left;margin-top:17px;padding-left:4px;">
        	<input type="submit" id="savesettings" name="savesettings" class="button-primary" style="width:100px;" value="Save Changes" />
        </div>
        <div style="float:left;margin-top:17px;margin-left:5px;">
          <form method="post" action="" >
            <input type="submit" name="reset" class="button-primary" style="width:100px" value="Default Settings" />
          </form>
        </div>
      </form>
      </div>
      
	</div>
<div style="clear:both;"></div>