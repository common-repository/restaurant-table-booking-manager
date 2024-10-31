<?php
	global $table_prefix,$wpdb;
	$sql = "select * from ".$table_prefix."restbl_uststablebookings ORDER BY tablebooking_id DESC";
	$tablebookings = $wpdb->get_results($sql);
	?>
  <script type="text/javascript">
		jQuery(document).ready(function(){
				//============================= Search Script ========================================
				jQuery('#btnsearchtablebooking').on('click',function(){
						var searchtext = jQuery('#txtsearchtablebooking').val();
						jQuery.ajax
						({
								type: "POST",
                url: '<?php echo admin_url( 'admin-ajax.php' );?>',
								data: {
                  action: 'restbl_search_tablebooking',
                  searchtext: searchtext
                },
								success: function(data)
								{
								},
								error : function(s , i , error){
										console.log(error);
								}
						}).done(function(data){
              data = data.trim();
              restbl_loading_hide();
              jQuery("#inner_content").html(data);
            });
						
						
				});
				//============================= Pagination Script=====================================
				restbl_load_moredeals_data(1);
				/*----------------More Deals------------------*/
				function restbl_load_moredeals_data(page){
						restbl_loading_show();                    
						jQuery.ajax
						({
								type: "POST",
                url: '<?php echo admin_url( 'admin-ajax.php' );?>',
								data: {
                  action: 'restbl_load_managetablebooking_data',  
                  page: page
                },
								success: function(msg)
								{
								}
						}).done(function(msg){
                restbl_loading_hide();
                jQuery("#inner_content").html(msg);
            });
				
				}
				/*---------------------------------------------*/
				function restbl_loading_show(){
						jQuery('#loading').html("<img src='<?php echo GEN_USTSTABLEBOOKING_PLUGIN_URL; ?>/images/loading.gif'/>").fadeIn('fast');
				}
				function restbl_loading_hide(){
						jQuery('#loading').fadeOut('fast');
				}                
				jQuery('#inner_content').delegate('.pagination li.active','click',function(){
						var page = jQuery(this).attr('p');
						//loadData(page);
						restbl_load_moredeals_data(page);
						jQuery('html, body').animate({
								scrollTop: jQuery("#content_top").offset().top
						}, 1950);
						
				});           
				jQuery('#inner_content').delegate('#go_btn','click',function(){
						var page = parseInt(jQuery('.goto').val());
						var no_of_pages = parseInt(jQuery('.total').attr('a'));
						if(page != 0 && page <= no_of_pages){
								//loadData(page);
								restbl_load_moredeals_data(page);
								jQuery('html, body').animate({
										scrollTop: jQuery("#content_top").offset().top
								}, 2050);
						}else{
								alert('Enter a PAGE between 1 and '+no_of_pages);
								jQuery('.goto').val("").focus();
								return false;
						}
						
				});
				//=========================== End pagination Script=====================================
				jQuery('#inner_content').delegate('#lnkapprove','click',function(e){
					e.preventDefault();
					var tablebookingid = jQuery(this).parent().children('#hdntablebookingid').val();
					jQuery.ajax({
							type: "POST",
              url: '<?php echo admin_url( 'admin-ajax.php' );?>',
							data: {
                action: 'restbl_activate_tablebooking',
                tablebooking_id:tablebookingid
              },
							success: function (data) {
									var count = data.length;
									if(count>0){
										alert('TableBooking Activated');
									}
							},
							error : function(s , i , error){
									console.log(error);
							}
					});
					
				});	
				
				jQuery('#inner_content').delegate('#delete_tablebooking','click',function(e){
					e.preventDefault();
          if(!confirm('Are you sure want to deletes')){
            return false;
          }
					var tablebookingid = jQuery(this).parent().children('#hdntablebookingid').val();
					jQuery.ajax({
							type: "POST",
              url: '<?php echo admin_url( 'admin-ajax.php' );?>',
							data: {
                action: 'restbl_delete_tablebooking',
                tablebooking_id:tablebookingid
              },
							success: function (data) {
									var count = data.length;
									if(count>0){
										alert('tablebooking Deleted');
									}
							},
							error : function(s , i , error){
									console.log(error);
							}
					});
					console.log(jQuery(this).parent().parent().remove());
				});
					
		});
	</script>
  <style type="text/css">
		#btnsearchtablebooking{
			background:url('<?php echo GEN_USTSTABLEBOOKING_PLUGIN_URL ?>/images/search.png') no-repeat;
			width: 30px; 
			height: 30px; 
			cursor:pointer;
		}
	</style>
	<div class="wrapper">
  <div class="wrap" style="float:left; width:100%;">
    <div id="icon-options-general" class="icon32"><br />
    </div>
    
    <div style="width:50%;float:left;"><h2>TableBooking Mamagement</h2></div>
    <div style="width:29%;float:left;margin-top:15px;">
    	<form id="frmsearchb" method="post" action="">
      	<input type="text" name="txtsearchtablebooking" id="txtsearchtablebooking" value="" style="width:250px;height:40px;" />
      	<input type="button" id="btnsearchtablebooking" name="btnsearchtablebooking" value="" />
      </form>
      <!--<img src="<?php// echo GEN_USTSTABLEBOOKING_PLUGIN_URL ?>/images/search.png" height="20px" width="20px" />-->
    </div>
    
    <div class="main_div">
     	<div class="metabox-holder" style="width:80%; float:left;">
        <div id="namediv" class="stuffbox" style="width:99%;">
        <h3 class="top_bar">Manage TableBooking</h3>
				<div id="inner_content">		
        	<div class="data"></div>
			  	<div class="pagination"></div>			
				 <table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
          <thead>
            <tr>
              <th>Table</th>
              <th>Date</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Email</th>
              <th>Phone</th>
              <th></th>
            </tr>
          </thead>
          <tr>
					<?php
          foreach($tablebookings as $tablebooking){
          ?>
            <tr class="alternate">
                <td><?php echo $tablebooking->table;?></td>
                <td><?php echo $tablebooking->date;?></td>
                <td><?php echo $tablebooking->start_time;?></td>
                <td><?php echo $tablebooking->end_time;?></td>
                <td><?php echo $tablebooking->email;?></td>
                <td><?php echo $tablebooking->phone;?></td>
                
                <td>
                  <?php if(!$tablebooking->confirmed):?><a id="lnkapprove" href="" > Approve </a>&nbsp;&nbsp;&nbsp;<?php else :?><span id="" > <b>Approved </b></span>&nbsp;&nbsp;&nbsp;<?php endif;?>
                  <a href="<?php echo get_option('siteurl');?>/wp-admin/admin.php?page=add-tablebooking-menu&calltype=edittablebooking&id=<?php echo $tablebooking->tablebooking_id;?>">edit</a>
                  &nbsp;&nbsp;&nbsp;<a style="cursor:pointer;" id="delete_tablebooking">delete</a>
                  <input type="hidden" id="hdntablebookingid"  name="hdntablebookingid" value="<?php echo $tablebooking->tablebooking_id;?>" />
                </td>
            </tr>
            <?php
            }
            ?>
          </tr>
          <tfoot>
            <tr>
               <th>Table</th>
              <th>Date</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Email</th>
              <th>Phone</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
				</div>
				</div>
		  </div>
	  </div>
	 </div>
  </div>
  
  <div id='loading'></div>