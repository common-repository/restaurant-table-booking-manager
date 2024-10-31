<?php
/*
Plugin Name: Restaurant Table Booking Manager
Plugin URI: http://upscalethought.com
Description: Restaurant Table Booking Management System
Version: 1.0
Author: UpScaleThought
Author URI: http://upscalethought.com
Text Domain: gen-restbl-uststablebooking
Domain Path: /i18n/languages/
*/
define('GEN_USTSTABLEBOOKING_PLUGIN_URL', plugins_url('',__FILE__));
define("GEN_USTS_BASE_URL", WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)));
define( 'GEN_USTSTABLEBOOKING_DIR', plugin_dir_path(__FILE__) );
define('GEN_CCB_BOOKED_BG_COLOR','138219') ;

//$uststable_shapes_page = get_page_by_path('gen-restbl-table-shapes');
$ustsbooking_tables_page = get_page_by_path('gen-restbl-tables');
$uststablebooking_calendar_page = get_page_by_path('gen-usts-tablebooking-calendar');

//$uststable_shapes_page_id = 0;
$ustsbooking_tables_page_id = 0;
$uststablebooking_calendar_page_id= 0;

if(isset($ustsbooking_tables_page)){
  $ustsbooking_tables_page_id = $ustsbooking_tables_page->ID;
}
if(isset($uststablebooking_calendar_page)){
  $uststablebooking_calendar_page_id = $uststablebooking_calendar_page->ID;
}

define('USTSTABLES_PAGEID', $ustsbooking_tables_page_id);
define('USTSTABLEBOOKINGCALENDAR_PAGEID', $uststablebooking_calendar_page_id);
include_once('includes/fullcalendar_shortcode.php');
include_once('includes/create_page.php');
include_once('operations/uststablebooking_init.php');
include_once('includes/tables_shortcode.php');
//=================================================
function restbl_create_custom_post_type() {
	register_post_type( 'custom_tablebooking',
		array(
			'labels' => array(
				'name' => __( 'TableBookings', 'restbl-uststablebooking' ),
				'singular_name' => __( 'TableBooking', 'restbl-uststablebooking' ),
				'menu_name'=>__('Restaurant Table Booking Manager', 'restbl-uststablebooking'),
				'all_items'=>__('Tables', 'restbl-uststablebooking'),
				'add_new_item'=>__('Add New Table', 'restbl-uststablebooking'),
				'add_new'=> __('Add New Table', 'restbl-uststablebooking'),
				'not_found'=>__('No TableBookings Found.', 'restbl-uststablebooking'),
				'search_items'=>__('Search TableBookings', 'restbl-uststablebooking'),
				'edit_item'=>__('Edit TableBooking', 'restbl-uststablebooking'),
				'view_item'=>__('View TableBooking', 'restbl-uststablebooking'),
				'not_found_in_trash'=>__('No TableBookings found in Trash', 'restbl-uststablebooking')
			),
			'public' => true,
			'has_archive' => false,
			'rewrite' => array('slug' => 'custom_tablebookings'),
      'supports' => array('title','thumbnail')
		)
	);
}

add_action( 'init', 'restbl_create_book_taxonomy' );   // commented to disable room category

function restbl_create_book_taxonomy() {
	register_taxonomy(
		'restbl_custom_category',
		'custom_tablebooking',
		array(
			'label' => __( 'Category', 'restbl-uststablebooking' ),
			'rewrite' => array( 'slug' => 'restbl_custom_category' ),
			'hierarchical' => true,
		)
	);
}

function  restbl_add_metabox_for_table(){
add_meta_box(
		'table_attribute_metabox', // ID, should be a string
		''.__("Table Attribute Settings","restbl-uststablebooking").'', // Meta Box Title
		'restbl_table_meta_box_content', // Your call back function, this is where your form field will go
		'custom_tablebooking', // The post type you want this to edit screen section (�post�, �page�, �dashboard�, �link�, �attachment� or �custom_post_type� where custom_post_type is the custom post type slug)
		'normal', // The placement of your meta box, can be �normal�, �advanced�or side
		'high' // The priority in which this will be displayed
		);
}

function restbl_table_meta_box_content($post){
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	 
	 jQuery('#upload_tableimage_button').click(function() {
			formfield = jQuery('#tablemetabox_image').attr('name');
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
			window.send_to_editor = function(html) {
				var regex = /src="(.+?)"/;
				var rslt =html.match(regex);
				imgurl = rslt[1];
        //alert(imgurl);
				jQuery('#tablemetabox_image').val(imgurl);
				tb_remove();
			}
			return false;
	 });
	 
});
</script>
<?php
$table_tableshape = get_post_meta($post->ID, '_table_tableshape', true);
$table_noofseat = get_post_meta($post->ID, '_table_noofseat', true);
$table_image = get_post_meta($post->ID, '_table_image', true);
$table_airconditioned = get_post_meta($post->ID, '_table_airconditioned', true);
$table_description = get_post_meta($post->ID, '_table_description', true);
?>
<table >
  <tbody>
  	
    <tr>
      <th scope="row"><?php _e("Table Shape","restbl-uststablebooking"); ?>:</th>
      <td>
      	<select id="tableshape" name="tableshape">
        	<option value="round" <?php if($table_tableshape=='round') echo 'selected'; ?> ><?php _e("Round","restbl-uststablebooking"); ?></option>
          <option value="square" <?php if($table_tableshape=='square') echo 'selected'; ?> ><?php _e("Square","restbl-uststablebooking"); ?></option>
          <option value="rectangle" <?php if($table_tableshape=='rectangle') echo 'selected'; ?> ><?php _e("Rectangle","restbl-uststablebooking"); ?></option>
        </select>
      </td>
    </tr>
    <tr>
    	<th scope="row"><?php _e("No of Seat","restbl-uststablebooking"); ?>:</th>
      <td><input type="text" name="tablemetabox_noofseat" id="tablemetabox_noofseat" value="<?php if(isset($table_noofseat)) echo $table_noofseat;?>" style="width:300px;" /></td>
    </tr>
    
    <tr>
    	<th scope="row"><?php _e("Table Image","restbl-uststablebooking"); ?>:</th>
      <td>
      	<input type="text" class="code"  name="tablemetabox_image" id="tablemetabox_image" value="<?php if(isset($table_image)) echo $table_image;?>" style="width:300px;" />
        <input  id="upload_tableimage_button" class="button" type="button" value="<?php _e("Upload Image","restbl-uststablebooking"); ?>" />
      </td>
    </tr>
    <tr>
    	<th scope="row"><?php _e("Air Conditioned","restbl-uststablebooking"); ?>:</th>
      <td>
      	<select id="tablemetabox_airconditioned" name="tablemetabox_airconditioned">
          <option value="nonac" <?php if($table_airconditioned=='nonac') echo 'selected'; ?> ><?php _e("Non AC","restbl-uststablebooking"); ?></option>
        	<option value="ac" <?php if($table_airconditioned=='ac') echo 'selected'; ?> ><?php _e("AC","restbl-uststablebooking"); ?></option>
        </select>
      </td>
    </tr>
    <tr>
      <th></th>
      <td></td>
    </tr>
    <tr>
      <th scope="row"><?php _e("Description","restbl-uststablebooking"); ?>:</th>
      <td>
      	<?php
					$content = $table_description;
					$editor_id = 'mycustomeditor';
					$settings = array('wpautop'=>true,'media_buttons'=>true,'textarea_name'=>'tablemetabox_description','textarea_rows'=>8);
					wp_editor( $content, $editor_id,$settings );
				?>
      </td>
    </tr>
  </tbody>
</table>
<?php
}
function restbl_save_event_metabox(){
	global $post;
	// Get our form field 
	if( $_POST ) :
		$table_tableshape = esc_attr( $_POST['tableshape'] );
		$table_noofseat = esc_attr( $_POST['tablemetabox_noofseat'] );
    $table_image = esc_attr( $_POST['tablemetabox_image'] );
		$table_airconditioned = esc_attr( $_POST['tablemetabox_airconditioned'] );
		$table_description = esc_attr( $_POST['tablemetabox_description'] );
		
		// Update post meta
		update_post_meta($post->ID, '_table_tableshape', $table_tableshape);
		update_post_meta($post->ID, '_table_noofseat', $table_noofseat);
    update_post_meta($post->ID, '_table_image', $table_image);
		update_post_meta($post->ID, '_table_airconditioned', $table_airconditioned);
		update_post_meta($post->ID, '_table_description', $table_description);
		
	endif;
}

add_action( 'save_post', 'restbl_save_event_metabox' );
add_action('add_meta_boxes','restbl_add_metabox_for_table');

/*---------------------*/
function restbl_add_tablebookings_menu(){
	//add_submenu_page('edit.php?post_type=custom_tablebooking', 'Payment Settings', 'Payment Settings', 'manage_options', 'restblpg_settings', 'restblpg_global_settings');
	add_submenu_page( 'edit.php?post_type=custom_tablebooking', 'Add Tablebooking', 'Add Tablebooking', 'manage_options', 'add-tablebooking-menu', 'restbl_add_tablebooking_settings' );
	add_submenu_page( 'edit.php?post_type=custom_tablebooking', 'Manage Tablebooking', 'Manage Tablebooking', 'manage_options', 'manage-tablebooking-menu', 'restbl_manage_tablebooking_settings');
	add_submenu_page( 'edit.php?post_type=custom_tablebooking', 'tablebooking Calendar', 'Tablebooking Calendar', 'manage_options', 'tablebooking-calendar-menu', 'restbl_tablebooking_calendar' );	
	//add_submenu_page( 'edit.php?post_type=custom_tablebooking', 'TableBooking Settings', 'TableBooking Settings', 'manage_options', 'tablebooking-settings-menu', 'restbl_tablebooking_settings_page' );
	add_submenu_page( 'edit.php?post_type=custom_tablebooking', 'FrontEnd CSS Fix', 'FrontEnd CSS Fix', 'manage_options', 'css-fix-menu', 'restbl_cssfix_front_setting' );
	add_submenu_page( 'edit.php?post_type=custom_tablebooking', 'PRO VERSION', 'PRO VERSION', 'manage_options', 'pro-version-menu', 'restbl_pro_version_menu' );
}
//-------------tablebooking Settings-----------------------
function restbl_uststablebooking_get_opt_val($opt_name,$default_val){
		if(get_option($opt_name)!=''){
			return $value = get_option($opt_name);
		}else{
			return $value =$default_val;
		}
}
//
function restbl_tablebooking_calendar(){
	include_once('calendar-fullcalendar.php');
}
function restbl_manage_tablebooking_settings(){
	include_once('includes/manage_tablebooking.php');
}
function restbl_add_tablebooking_settings(){
	include_once('includes/add_tablebooking_backend.php');
}
function restbl_cssfix_front_setting(){
	include_once('includes/add_cssfix_front.php');	
}
function restbl_pro_version_menu(){
	include_once('includes/tablebooking_pro_version.php');	
}
add_action('admin_menu','restbl_add_tablebookings_menu');
/*---------------------*/

function restbl_tablebooking_uninstall(){
  restbl_programmatically_delete_page(USTSTABLES_PAGEID);
  restbl_programmatically_delete_page(USTSTABLEBOOKINGCALENDAR_PAGEID);
}

register_activation_hook( __FILE__, 'restbl_uststablebooking_install' );
register_deactivation_hook( __FILE__, 'restbl_tablebooking_uninstall');
add_action( 'init', 'restbl_create_custom_post_type' );
//====== session start =================================
add_action('init', 'restbl_tablebookingStartSession', 1);
function restbl_tablebookingStartSession() {
    if(!session_id()) {
        session_start();
    }
}
//------
function usts_tablebookingjs(){
	wp_register_script('tablebookingjs',plugins_url('/includes/js/tablebooking.js',__FILE__));
	wp_localize_script( 'tablebookingjs', 'ustsTableBookingAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	
	wp_enqueue_script( 'tablebookingjs');
}
function usts_tablebookingjs_front(){
	wp_register_script('tablebookingjs_front',plugins_url('/includes/js/tablebooking_front.js',__FILE__),'jquery',"",true);
	wp_localize_script( 'tablebookingjs_front', 'ustsTableBookingAjax_front', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	wp_enqueue_script( 'tablebookingjs_front');
}

add_action('admin_enqueue_scripts','usts_tablebookingjs');
add_action('wp_enqueue_scripts','usts_tablebookingjs_front');
//
function usts_set_ajax_table_session(){
		//die();
		global $table_prefix,$wpdb;	
		//echo 'im here';die();
		if ( isset($_REQUEST) ) {
			$_SESSION['tableid'] = $_REQUEST['tableid']; 
			echo $_SESSION['tableid'];
		}
		exit;
}

add_action( 'wp_ajax_nopriv_usts_set_ajax_table_session','usts_set_ajax_table_session' );
add_action( 'wp_ajax_usts_set_ajax_table_session', 'usts_set_ajax_table_session' );

//
function usts_set_ajax_table_session_front(){
		global $table_prefix,$wpdb;	
		if ( isset($_REQUEST) ) {
			$_SESSION['tableid_front'] = $_REQUEST['tableid']; 
			echo $_SESSION['tableid_front'];
		}
		exit;
}

add_action( 'wp_ajax_nopriv_usts_set_ajax_table_session_front','usts_set_ajax_table_session_front' );
add_action( 'wp_ajax_usts_set_ajax_table_session_front', 'usts_set_ajax_table_session_front' );
//
function usts_cart_session_ajax_request(){
	if ( isset($_REQUEST) ) {
		$indx = $_REQUEST['indx'];
		$shopping_cart_arr = $_SESSION['tablebookingcart'];	
	  unset($shopping_cart_arr[$indx]);
		sort($shopping_cart_arr);
	  $_SESSION['tablebookingcart'] = $shopping_cart_arr;	
		print_r($shopping_cart_arr);//json_encode($shopping_cart_arr);
	}
	exit;
	//die() ;
}
add_action( 'wp_ajax_nopriv_usts_cart_session_ajax_request','usts_cart_session_ajax_request' );
add_action( 'wp_ajax_usts_cart_session_ajax_request', 'usts_cart_session_ajax_request' );
//=========Payment System-----------------------------------------------------------------------------------
define('WP_CUSTOM_PRODUCT_URL', plugins_url('',__FILE__));
define('WP_CUSTOM_PRODUCT_PATH',plugin_dir_path( __FILE__ ));
function add_admin_additional_script(){
  wp_enqueue_script( 'thickbox');
  wp_enqueue_style ( 'thickbox');
  wp_enqueue_media();

  wp_enqueue_script( 'post' );
  wp_enqueue_style ( 'restblpg_admin_style',plugins_url( '/restblpg_resource/admin/css/admin.css', __FILE__ ));
  //wp_enqueue_script( 'jquery-no-conflict.js', plugins_url( '/restblpg_resource/js/jquery-no-conflict.js', __FILE__ ) );
}
function add_frontend_additional_script(){
	wp_enqueue_style( 'custom.css', plugins_url( '/restblpg_resource/css/custom.css', __FILE__ ) );
}
function load_custom_wp_admin_style() {
}
function restbl_get_tablebookings(){
  if(isset($_REQUEST)){
    global $table_prefix,$wpdb;
    $tablebooking_id = $_REQUEST['tablebooking_id'];
    $sql = "select * from ".$table_prefix."restbl_uststablebookings where tablebooking_id=".$tablebooking_id;
    $result = $wpdb->get_results($sql);
    echo json_encode($result);
  }
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_get_tablebookings','restbl_get_tablebookings' );
add_action( 'wp_ajax_restbl_get_tablebookings', 'restbl_get_tablebookings' );

function restbl_load_managetablebooking_data_front(){
  if(isset($_REQUEST)){
    $msg ='<style type="text/css">
        #loading{
            width: 50px;
            position: absolute;
            /*top: 100px;
            left: 100px;
            margin-top:200px;*/
            height:50px;
        }
        #inner_content{
           padding: 0 20px 0 0!important;
        }
        #inner_content .pagination ul li.inactive,
        #inner_content .pagination ul li.inactive:hover{
            background-color:#ededed;
            color:#bababa;
            border:1px solid #bababa;
            cursor: default;
        }
        #inner_content .data ul li{
            list-style: none;
            font-family: verdana;
            margin: 5px 0 5px 0;
            color: #000;
            font-size: 13px;
        }

        #inner_content .pagination{
            width: 80%;/*800px;*/
            height: 45px;
        }
        #inner_content .pagination ul li{
            list-style: none;
            float: left;
            border: 1px solid #006699;
            padding: 2px 6px 2px 6px;
            margin: 0 3px 0 3px;
            font-family: arial;
            font-size: 14px;
            color: #006699;
            font-weight: bold;
            background-color: #f2f2f2;

            /*display:inline;
            cursor:pointer;*/
        }
        #inner_content .pagination ul li:hover{
            color: #fff;
            background-color: #006699;
            cursor: pointer;
        }
        .go_button
        {
          background-color:#f2f2f2;
          border:1px solid #006699;
          color:#cc0000;
          padding:2px 6px 2px 6px;
          cursor:pointer;
          position:absolute;
          /*margin-top:-1px;*/
          width:50px;
        }
        .total
        {
          float:right;
          font-family:arial;
          color:#999;
          padding-right:150px;
        }
        #namediv input {
          width:5%!important;
        }
        /*---------------------------------*/
    </style>';
    if($_POST['page'])
      {
      $page = $_POST['page'];
      $cur_page = $page;
      $page -= 1;
      $per_page = 10;//15;
      $previous_btn = true;
      $next_btn = true;
      $first_btn = true;
      $last_btn = true;
      $start = $page * $per_page;
        global $table_prefix,$wpdb;
        $sql = "select * from ".$table_prefix."restbl_uststablebookings ";
        $result_count = $wpdb->get_results($sql);
        $count = count($result_count);
        $sql = $sql.' LIMIT '.$start.', '.$per_page.'';
        $result_page_data = $wpdb->get_results($sql); 
      $msg = "<div id='content_top'></div>";
      if(count($result_page_data)){
            $msg .= '<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
                        <thead>
                          <tr>
                            <th>Room</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tr>';
                    foreach($result_page_data as $tablebooking){
                      $msg .= '<tr class="alternate">
                                  <td>'.$tablebooking->room.'</td>
                                  <td>'.$tablebooking->from_date.'</td>
                                  <td>'.$tablebooking->to_date.'</td>
                                  <td>'.$tablebooking->email.'</td>
                                  <td>'.$tablebooking->phone.'</td>

                                  <td>
                                    ';
                      if(!$tablebooking->confirmed){
                          $msg .= '<a id="lnkapprove" href="" > Approve </a>&nbsp;&nbsp;&nbsp;';
                      }
                      else {
                          $msg .= '<span id="" > <b>Approved </b></span>&nbsp;&nbsp;&nbsp;';
                      }
                      $msg .= '<a onclick="restbl_open_edit_popup('.$tablebooking->tablebooking_id.')" style="cursor:pointer;text-decoration:none;" >edit</a>
                                    &nbsp;&nbsp;&nbsp;<a id="delete_tablebooking" href="#" >delete</a>
                                    <input type="hidden" id="hdntablebookingid"  name="hdntablebookingid" value="'.$tablebooking->tablebooking_id.'" />

                                  </td>
                              </tr>';
                    }
                    $msg .= '</tr>
                              <tfoot>
                                <tr>
                                  <th>Room</th>
                                  <th>From Date</th>
                                  <th>To Date</th>
                                  <th>Email</th>
                                  <th>Phone</th>
                                  <th></th>
                                </tr>
                              </tfoot>
                            </table>';	
        //}
      }
      else{
        $msg .= '<div style="padding:80px;color:red;">Sorry! No Data Found!</div>';
      }	
      $msg = "<div class='data'>" . $msg . "</div>"; // Content for Data

      $no_of_paginations = ceil($count / $per_page);
      /* ---------------Calculating the starting and endign values for the loop----------------------------------- */
      if ($cur_page >= 7) {
          $start_loop = $cur_page - 3;
          if ($no_of_paginations > $cur_page + 3)
              $end_loop = $cur_page + 3;
          else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
              $start_loop = $no_of_paginations - 6;
              $end_loop = $no_of_paginations;
          } else {
              $end_loop = $no_of_paginations;
          }
      } else {
          $start_loop = 1;
          if ($no_of_paginations > 7)
              $end_loop = 7;
          else
              $end_loop = $no_of_paginations;
      }
      /* ----------------------------------------------------------------------------------------------------------- */
      $msg .= "<div class='pagination'><ul>";

      // FOR ENABLING THE FIRST BUTTON
      if ($first_btn && $cur_page > 1) {
          $msg .= "<li p='1' class='active'>First</li>";
      } else if ($first_btn) {
          $msg .= "<li p='1' class='inactive'>First</li>";
      }

      // FOR ENABLING THE PREVIOUS BUTTON
      if ($previous_btn && $cur_page > 1) {
          $pre = $cur_page - 1;
          $msg .= "<li p='$pre' class='active'>Previous</li>";
      } else if ($previous_btn) {
          $msg .= "<li class='inactive'>Previous</li>";
      }
      for ($i = $start_loop; $i <= $end_loop; $i++) {

          if ($cur_page == $i)
              $msg .= "<li p='$i' style='color:#fff;background-color:#006699;' class='active'>{$i}</li>";
          else
              $msg .= "<li p='$i' class='active'>{$i}</li>";
      }

      // TO ENABLE THE NEXT BUTTON
      if ($next_btn && $cur_page < $no_of_paginations) {
          $nex = $cur_page + 1;
          $msg .= "<li p='$nex' class='active'>Next</li>";
      } else if ($next_btn) {
          $msg .= "<li class='inactive'>Next</li>";
      }

      // TO ENABLE THE END BUTTON
      if ($last_btn && $cur_page < $no_of_paginations) {
          $msg .= "<li p='$no_of_paginations' class='active'>Last</li>";
      } else if ($last_btn) {
          $msg .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
      }
      $goto = "<input type='text' class='goto' size='1' style='margin-left:30px;height:24px;'/><input type='button' id='go_btn' class='go_button' value='Go'/>";
      $total_string = "<span class='total' a='$no_of_paginations'>Page <b>" . $cur_page . "</b> of <b>$no_of_paginations</b></span>";
      $img_loading = "<span ><div id='loading'></div></span>";
      $msg = $msg . "" . $goto . $total_string . $img_loading . "</ul></div>";  // Content for pagination
      echo $msg;
    }
  }
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_load_managetablebooking_data_front','restbl_load_managetablebooking_data_front' );
add_action( 'wp_ajax_restbl_load_managetablebooking_data_front', 'restbl_load_managetablebooking_data_front' );
function restbl_check_tablebooking(){
  if(isset($_REQUEST)){
    global $table_prefix,$wpdb;
    $hdntablebookingid = $_REQUEST['hdntablebookingid'];
    $table = $_REQUEST['table'];
    $date = $_REQUEST['date'];
    $start_time = $_REQUEST['starttime'];
    $end_time = $_REQUEST['endtime'];
    //$timeshift = $_REQUEST['timeshift'];

    $table_cond = "table like '%".$table."%'";  

    $date = $_REQUEST['date'];
    $starttime = $_REQUEST['starttime'];

    $sql = "";
    if($hdntablebookingid != '' || $hdntablebookingid != NULL ){
      $sql = "select * from ".$table_prefix."restbl_uststablebookings where (".$table_cond.") and 
        date = '".$date."' and  
        ((start_time > '".$start_time."' and end_time < '".$end_time."') or 
        (end_time > '".$start_time."' and end_time < '".$end_time."') or 
        (start_time > '".$start_time."' and start_time < '".$end_time."') or 
        (start_time < '".$start_time."' and end_time > '".$end_time."') )
        and timeshift = '".$timeshift."'
        and tablebooking_id!=".$hdntablebookingid;
    }
    else{
      $sql = "select * from ".$table_prefix."restbl_uststablebookings where (".$table_cond.") and 
        date = '".$date."' and  
        ((start_time > '".$start_time."' and end_time < '".$end_time."') or 
        (end_time > '".$start_time."' and end_time < '".$end_time."') or 
        (start_time > '".$start_time."' and start_time < '".$end_time."') or 
        (start_time < '".$start_time."' and end_time > '".$end_time."') )
        and timeshift = '".$timeshift."'";
    }
    $result = $wpdb->get_results($sql);
    $yesno = "";
    if(count($result)>0){
      $yesno .= "yes";	
    }
    else{
      $yesno .= "no";
    }
    echo $yesno;
  }
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_check_tablebooking','restbl_check_tablebooking' );
add_action( 'wp_ajax_restbl_check_tablebooking', 'restbl_check_tablebooking' );
function restbl_save_tablebooking(){
  if ( count($_POST) > 0 ){ 
    global $table_prefix,$wpdb;
    $hdntablebookingid = $_REQUEST['hdntablebookingid'];
    //echo $hdntablebookingid;
    //exit;
    $tableid = $_REQUEST['tableid'];
    $table = $_REQUEST['table'];
    $date = $_REQUEST['date'];
    //$date = DATE_FORMAT($date,'%Y-%m-%d');
    //$dateparts = explode("/", $date);
    //$date = $dateparts[2]."-".$dateparts[0]."-".$dateparts[1]; 
    //echo $date; exit;
    $start_time = $_REQUEST['start_time'];

    $end_time = $_REQUEST['end_time'];
    //$time_shift = $_REQUEST['time_shift'];
    //echo $time_shift; exit;
    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    $email = $_REQUEST['email'];
    $phone = $_REQUEST['phone'];
    $details = $_REQUEST['details'];
    $tablebookingby = $_REQUEST['tablebookingby'];
    $noofseat = $_REQUEST['noof_seat'];
    //$price = $_REQUEST['price'];
    //$payment_method = $_REQUEST['payment_method'];

    $values = array(
      'table_id'=>$tableid,
      'table'=>$table,
      'date'=>$date,
      'start_time'=>$start_time, 
      'end_time'=>$end_time, 
      'first_name'=>$first_name, 
      'last_name'=>$last_name, 
      'email'=>$email, 
      'phone'=>$phone, 
      'details'=>$details, 
      'tablebooking_by'=>$tablebookingby,
      'noof_seat' => $noofseat,  
      'confirmed'=> 0
      //'custom_price'=>$price, 
      //'payment_method'=>$payment_method,
    );
    if($hdntablebookingid == "" || $hdntablebookingid == NULL){
      $wpdb->insert($table_prefix.'restbl_uststablebookings',$values );	
      $inserted_id = $wpdb->insert_id;
      echo $inserted_id;
    }
    else{
      $wpdb->update(
         $table_prefix.'restbl_uststablebookings',
         $values,
         array('tablebooking_id' =>$hdntablebookingid)
       );
       echo $hdntablebookingid;
    }
  }
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_save_tablebooking','restbl_save_tablebooking' );
add_action( 'wp_ajax_restbl_save_tablebooking', 'restbl_save_tablebooking' );
function restbl_get_room_bycat(){
  if(isset($_REQUEST)){
    global $table_prefix,$wpdb;
    $term_id = $_REQUEST['term_id'];
    $sql_room = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and tt.term_id=".$term_id." and pm.meta_key='_room_price'";
    $result = $wpdb->get_results($sql_room);
    echo json_encode($result);
  }
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_get_room_bycat','restbl_get_room_bycat' );
add_action( 'wp_ajax_restbl_get_room_bycat', 'restbl_get_room_bycat' );
function restbl_save_tablebooking_session(){
  if ( count($_POST) > 0 ){ 
    global $table_prefix,$wpdb;
    $hdntablebookingid = $_REQUEST['hdntablebookingid'];
    $scheduleid = $_REQUEST['tableid'];
    $schedule = $_REQUEST['table'];
    $date = $_REQUEST['date'];
    $start_time = $_REQUEST['start_time'];
    $end_time = $_REQUEST['end_time'];
    $time_shift = $_REQUEST['time_shift'];
    $first_name = $_REQUEST['first_name'];
    $last_name = $_REQUEST['last_name'];
    $email = $_REQUEST['email'];
    $phone = $_REQUEST['phone'];
    $details = $_REQUEST['details'];
    $tablebookingby = $_REQUEST['tablebookingby'];
    $price = $_REQUEST['price'];
    $payment_method = $_REQUEST['payment_method'];

    $values = array(
      'arr_type'=>'raw',
      'carthdntablebookingid'=> $hdntablebookingid, 
      'table_id'=>$scheduleid,
      'table'=>$schedule,
      'date'=>$date,
      'start_time'=>$start_time, 
      'end_time'=>$end_time, 
      'time_shift'=>$time_shift,
      'first_name'=>$first_name, 
      'last_name'=>$last_name, 
      'email'=>$email, 
      'phone'=>$phone, 
      'details'=>$details, 
      'tablebooking_by'=>$tablebookingby, 
      'custom_price'=>$price, 
      'payment_method'=>$payment_method,
    );
    $count = 0;
    if(isset($_SESSION['tablebookingcart'])){
      $count = count($_SESSION['tablebookingcart']);
    }
    else{
      $count = 0;
    }
    $_SESSION['tablebookingcart'][$count] = $values;
    echo "added to session";
  }
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_save_tablebooking_session','restbl_save_tablebooking_session' );
add_action( 'wp_ajax_restbl_save_tablebooking_session', 'restbl_save_tablebooking_session' );
function restbl_save_cssfixfront(){
  if ( count($_POST) > 0 ){ 
    global $table_prefix,$wpdb;
    $cssfix = $_REQUEST['cssfix'];
    $css = $_REQUEST['css'];
    $isupdate ="";
    if($cssfix == "front"){
      $isupdate = update_option('cssfix_front',$css);
    }
    if($isupdate){
      echo "added";
    }
  }
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_save_cssfixfront','restbl_save_cssfixfront' );
add_action( 'wp_ajax_restbl_save_cssfixfront', 'restbl_save_cssfixfront' );
function restbl_search_tablebooking(){
  global $table_prefix,$wpdb;
  $search_text = $_REQUEST['searchtext'];
  $sql = "select * from ".$table_prefix."restbl_uststablebookings where email='".$search_text."' or phone='".$search_text."' or table='".$search_text."' or date='".$search_text."'";
  $result = $wpdb->get_results($sql);
  $msg = "<div id='content_top'></div>";
  if(count($result)){
        $msg .= '<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
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
                    <tr>';
                foreach($result as $tablebooking){
                  $msg .= '<tr class="alternate">
                              <td>'.$tablebooking->table.'</td>
                              <td>'.$tablebooking->date.'</td>
                              <td>'.$tablebooking->start_time.' '.$tablebooking->timeshift.'</td>  
                              <td>'.$tablebooking->end_time.' '.$tablebooking->timeshift.'</td>
                              <td>'.$tablebooking->email.'</td>
                              <td>'.$tablebooking->phone.'</td>

                              <td>
                                ';
                  $msg .= '<a href="'.site_url().'/wp-admin/admin.php?page=add-tablebooking-menu&calltype=edittablebooking&id='.$tablebooking->tablebooking_id.'">edit</a>
                                &nbsp;&nbsp;&nbsp;<a id="delete_tablebooking" href="#" onclick="return confirm("Are you sure want to delete");">delete</a>
                                <input type="hidden" id="hdntablebookingid"  name="hdntablebookingid" value="'.$tablebooking->tablebooking_id.'" />
                              </td>
                          </tr>';
                }
                $msg .= '</tr>
                          <tfoot>
                            <tr>
                              <th>Table</th>
                              <th>From Date</th>
                              <th>To Date</th>
                              <th>Email</th>
                              <th>Phone</th>
                              <th></th>
                            </tr>
                          </tfoot>
                        </table>';	
  }
  else{
    $msg .= '<div style="padding:80px;color:red;">Sorry! No Data Found!</div>';
  }
  $msg = "<div class='data'>" . $msg . "</div>";
  echo $msg;
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_search_tablebooking','restbl_search_tablebooking' );
add_action( 'wp_ajax_restbl_search_tablebooking', 'restbl_search_tablebooking' );
function restbl_load_managetablebooking_data(){
  if($_POST['page'])
  {
    $page = $_POST['page'];
    $cur_page = $page;
    $page -= 1;
    $per_page = 10;
    $previous_btn = true;
    $next_btn = true;
    $first_btn = true;
    $last_btn = true;
    $start = $page * $per_page;
      global $table_prefix,$wpdb;
      $sql = "select * from ".$table_prefix."restbl_uststablebookings ORDER BY tablebooking_id DESC";
      $result_count = $wpdb->get_results($sql);
      $count = count($result_count);
      $sql = $sql.' LIMIT '.$start.', '.$per_page.'';
      $result_page_data = $wpdb->get_results($sql); 
    $msg = "<style type='text/css'>
      /*-----paginations------*/
      #loading{
          width: 50px;
          position: absolute;
          /*top: 100px;
          left: 100px;
          margin-top:200px;*/
          height:50px;
      }
      #inner_content{
         padding: 0 20px 0 0!important;
      }
      #inner_content .pagination ul li.inactive,
      #inner_content .pagination ul li.inactive:hover{
          background-color:#ededed;
          color:#bababa;
          border:1px solid #bababa;
          cursor: default;
      }
      #inner_content .data ul li{
          list-style: none;
          font-family: verdana;
          margin: 5px 0 5px 0;
          color: #000;
          font-size: 13px;
      }

      #inner_content .pagination{
          width: 80%;/*800px;*/
          height: 45px;
      }
      #inner_content .pagination ul li{
          list-style: none;
          float: left;
          border: 1px solid #006699;
          padding: 2px 6px 2px 6px;
          margin: 0 3px 0 3px;
          font-family: arial;
          font-size: 14px;
          color: #006699;
          font-weight: bold;
          background-color: #f2f2f2;

          /*display:inline;
          cursor:pointer;*/
      }
      #inner_content .pagination ul li:hover{
          color: #fff;
          background-color: #006699;
          cursor: pointer;
      }
      .go_button
      {
        background-color:#f2f2f2;
        border:1px solid #006699;
        color:#cc0000;
        padding:2px 6px 2px 6px;
        cursor:pointer;
        position:absolute;
        /*margin-top:-1px;*/
        width:50px;
      }
      .total
      {
        float:right;
        font-family:arial;
        color:#999;
        padding-right:150px;
      }
      #namediv input {
        width:5%!important;
      }
      /*---------media query-------------*/
      /*@media screen and (min-width: 360px) and (max-width:991px){
        #imgproduct{
          width:46%;
        }	
      }*/
      /*---------------------------------*/
    </style>";  
    $msg .= "<div id='content_top'></div>";
    if(count($result_page_data)){
          $msg .= '<table class="wp-list-table widefat fixed bookmarks" cellspacing="0">
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
                      <tr>';
                  foreach($result_page_data as $tablebooking){
                    $msg .= '<tr class="alternate">
                                <td>'.$tablebooking->table.'</td>
                                <td>'.$tablebooking->date.'</td>
                                <td>'.$tablebooking->start_time.' '.$tablebooking->timeshift.'</td>  
                                <td>'.$tablebooking->end_time.' '.$tablebooking->timeshift.'</td>
                                <td>'.$tablebooking->email.'</td>
                                <td>'.$tablebooking->phone.'</td>

                                <td>
                                  ';
                     if(!$tablebooking->confirmed){
                       $msg .= '<a id="lnkapprove" href="" > Approve </a>&nbsp;&nbsp;&nbsp;';
                     }
                     else{
                       $msg .= '<span id="" > <b>Approved </b></span>&nbsp;&nbsp;&nbsp;';
                     } 
                    
                    $msg .= '<a href="'.site_url().'/wp-admin/admin.php?page=add-tablebooking-menu&calltype=edittablebooking&id='.$tablebooking->tablebooking_id.'">edit</a>
                                  &nbsp;&nbsp;&nbsp;<a id="delete_tablebooking" href="#" onclick="return confirm(\'Are you sure want to delete\');">delete</a>
                                  <input type="hidden" id="hdntablebookingid"  name="hdntablebookingid" value="'.$tablebooking->tablebooking_id.'" />
                                </td>
                            </tr>';
                  }
                  $msg .= '</tr>
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
                          </table>';	
    }
    else{
      $msg .= '<div style="padding:80px;color:red;">Sorry! No Data Found!</div>';
    }	
    $msg = "<div class='data'>" . $msg . "</div>"; // Content for Data
    $no_of_paginations = ceil($count / $per_page);
    /* ---------------Calculating the starting and endign values for the loop----------------------------------- */
    if ($cur_page >= 7) {
        $start_loop = $cur_page - 3;
        if ($no_of_paginations > $cur_page + 3)
            $end_loop = $cur_page + 3;
        else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
            $start_loop = $no_of_paginations - 6;
            $end_loop = $no_of_paginations;
        } else {
            $end_loop = $no_of_paginations;
        }
    } else {
        $start_loop = 1;
        if ($no_of_paginations > 7)
            $end_loop = 7;
        else
            $end_loop = $no_of_paginations;
    }
    /* ----------------------------------------------------------------------------------------------------------- */
    $msg .= "<div class='pagination'><ul>";
    // FOR ENABLING THE FIRST BUTTON
    if ($first_btn && $cur_page > 1) {
        $msg .= "<li p='1' class='active'>First</li>";
    } else if ($first_btn) {
        $msg .= "<li p='1' class='inactive'>First</li>";
    }
    // FOR ENABLING THE PREVIOUS BUTTON
    if ($previous_btn && $cur_page > 1) {
        $pre = $cur_page - 1;
        $msg .= "<li p='$pre' class='active'>Previous</li>";
    } else if ($previous_btn) {
        $msg .= "<li class='inactive'>Previous</li>";
    }
    for ($i = $start_loop; $i <= $end_loop; $i++) {

        if ($cur_page == $i)
            $msg .= "<li p='$i' style='color:#fff;background-color:#006699;' class='active'>{$i}</li>";
        else
            $msg .= "<li p='$i' class='active'>{$i}</li>";
    }
    // TO ENABLE THE NEXT BUTTON
    if ($next_btn && $cur_page < $no_of_paginations) {
        $nex = $cur_page + 1;
        $msg .= "<li p='$nex' class='active'>Next</li>";
    } else if ($next_btn) {
        $msg .= "<li class='inactive'>Next</li>";
    }
    // TO ENABLE THE END BUTTON
    if ($last_btn && $cur_page < $no_of_paginations) {
        $msg .= "<li p='$no_of_paginations' class='active'>Last</li>";
    } else if ($last_btn) {
        $msg .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
    }
    $goto = "<input type='text' class='goto' size='1' style='margin-left:30px;height:24px;'/><input type='button' id='go_btn' class='go_button' value='Go'/>";
    $total_string = "<span class='total' a='$no_of_paginations'>Page <b>" . $cur_page . "</b> of <b>$no_of_paginations</b></span>";
    $img_loading = "<span ><div id='loading'></div></span>";
    $msg = $msg . "" . $goto . $total_string . $img_loading . "</ul></div>";  // Content for pagination
    echo $msg;
  }
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_load_managetablebooking_data','restbl_load_managetablebooking_data' );
add_action( 'wp_ajax_restbl_load_managetablebooking_data', 'restbl_load_managetablebooking_data' );
function restbl_activate_tablebooking(){
  if ( count($_POST) > 0 ){
    global $table_prefix,$wpdb;
    $tablebookingid = $_REQUEST['tablebooking_id'];	
     $values = array('confirmed'=>1);
     $wpdb->update(
           $table_prefix.'restbl_uststablebookings',
           $values,
           array('tablebooking_id' =>$tablebookingid)
         );
     echo $tablebookingid;		 
  }
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_activate_tablebooking','restbl_activate_tablebooking' );
add_action( 'wp_ajax_restbl_activate_tablebooking', 'restbl_activate_tablebooking' );
function restbl_delete_tablebooking(){
  if ( count($_POST) > 0 ){ 
    global $table_prefix,$wpdb;
    $tablebookingid = $_REQUEST['tablebooking_id'];	
    $aff_rows = $wpdb->query("delete from ".$table_prefix."restbl_uststablebookings where tablebooking_id='".$tablebookingid."'");
    echo $aff_rows;		 
  }  
  exit;
}
add_action( 'wp_ajax_nopriv_restbl_delete_tablebooking','restbl_delete_tablebooking' );
add_action( 'wp_ajax_restbl_delete_tablebooking', 'restbl_delete_tablebooking' );
function restbl_add_settings_ajax_request(){
  $days = $_REQUEST['days'];
  update_option("_restbl_booking_days",$days);
}
add_action( 'wp_ajax_nopriv_restbl_add_settings_ajax_request','restbl_add_settings_ajax_request' );
add_action( 'wp_ajax_restbl_add_settings_ajax_request', 'restbl_add_settings_ajax_request' );
//==================================End Ajax Call ===========================================================
	function restbl_fullcalendarincludejs(){
    wp_register_script( 'jquery.multiple.select',plugins_url('/multiselect/multiple-select/jquery.multiple.select.js',__FILE__), array( 'jquery' ));
		wp_register_script( 'fullcalendarjs',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.js',__FILE__));
    wp_register_script( 'jquery.bt.min',plugins_url('/tooltip/beautytips-master/jquery.bt.min.js',__FILE__), array( 'jquery' ));
		wp_register_script( 'jscolor',plugins_url('/jscolor/jscolor.js',__FILE__));
		//wp_register_script( 'gcaljs',plugins_url('/fullcalendar/gcal.js',__FILE__));	
		wp_enqueue_script( 'jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-datepicker');
    
		//wp_enqueue_script( 'jqueryminjs');
		//wp_enqueue_script( 'jqueryuijs');
    wp_enqueue_script( 'jquery.multiple.select');
		wp_enqueue_script( 'fullcalendarjs');
    wp_enqueue_script( 'jquery.bt.min');
		wp_enqueue_script( 'jscolor');
		//wp_enqueue_script( 'gcaljs');	
	}
	function 	restbl_fullcalendarincludecss(){
			wp_register_style( 'jquery-ui',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/cupertino/jquery-ui.min.css',__FILE__));
			//wp_register_style( 'jquery-ui',plugins_url('/assets/css/jquery/jquery-ui.css',__FILE__));
      wp_register_style( 'multiple-select',plugins_url('/multiselect/multiple-select/multiple-select.css',__FILE__));
      wp_register_style( 'fullcalendarcss',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.css',__FILE__));
			wp_register_style( 'fullcalendarprintcss',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.print.css',__FILE__));
			wp_register_style( 'jquery.bt',plugins_url('/tooltip/beautytips-master/jquery.bt.css',__FILE__));
			
			wp_enqueue_style( 'jquery-ui');
      wp_enqueue_style( 'multiple-select');
			wp_enqueue_style( 'fullcalendarcss');
			wp_enqueue_style( 'fullcalendarprintcss');
			wp_enqueue_style( 'jquery.bt');
	}
	add_action('admin_enqueue_scripts','restbl_fullcalendarincludejs');
	add_action('admin_enqueue_scripts','restbl_fullcalendarincludecss');
  function restbl_fullcalendarincludejs_front(){
    wp_register_script( 'fullcalendar',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.js',__FILE__), array( 'jquery' ));
    wp_register_script( 'jquery.multiple.select',plugins_url('/multiselect/multiple-select/jquery.multiple.select.js',__FILE__), array( 'jquery' ));
    wp_register_script( 'jquery.bt.min',plugins_url('/tooltip/beautytips-master/jquery.bt.min.js',__FILE__));
    
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery.multiple.select');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-datepicker');
    
    wp_enqueue_script( 'fullcalendar' );
    wp_enqueue_script( 'jquery.bt.min' );
  }
  function 	restbl_fullcalendarincludecss_front(){
    //wp_register_style( 'jquery-ui',plugins_url('/assets/css/jquery/jquery-ui.css',__FILE__));
    
    wp_register_style( 'jquery-ui',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/cupertino/jquery-ui.min.css',__FILE__));
    wp_register_style( 'fullcalendar',plugins_url('/fullcalendar/fullcalendar-1.6.4/fullcalendar/fullcalendar.css',__FILE__));
    wp_register_style( 'jquery.bt',plugins_url('/tooltip/beautytips-master/jquery.bt.css',__FILE__));
    
    
    wp_enqueue_style( 'jquery-ui');
    wp_register_style( 'multiple-select',plugins_url('/multiselect/multiple-select/multiple-select.css',__FILE__));
    wp_enqueue_style( 'multiple-select');
    wp_enqueue_style( 'fullcalendar');
    wp_enqueue_style( 'jquery.bt');
  }
  add_action('wp_enqueue_scripts','restbl_fullcalendarincludejs_front');
	add_action('wp_enqueue_scripts','restbl_fullcalendarincludecss_front');