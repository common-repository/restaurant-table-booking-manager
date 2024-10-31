<?php
function restbl_tables_shortcode($atts){
  global $table_prefix,$wpdb; 
  $shape= "";
  if(isset($_REQUEST['shape'])){
		$shape = $_REQUEST['shape'];
  }
	$sql_table = "select * from ".$table_prefix."term_taxonomy tt inner join ".$table_prefix."term_relationships tr on tt.term_taxonomy_id = tr.term_taxonomy_id inner join ".$table_prefix."posts p on p.id=tr.object_id inner join ".$table_prefix."postmeta pm on pm.post_id= p.id where p.post_status = 'publish' and pm.meta_key='_table_tableshape'";
	$tables = $wpdb->get_results($sql_table);
	?>
  <script type="text/javascript">
		jQuery(document).ready(function(){
		});
		
	</script>
  <?php	
	 $output ='<div style="">';
   foreach($tables as $tbl){
     $postid = $tbl->post_id;
     $tbl_img = get_post_meta($postid, '_table_image', true);
     $noofseat = get_post_meta($postid, '_table_noofseat', true);
     $output .='<div style="color:black;float:left;margin:10px;"> 
                  <a href="'.get_option("siteurl").'/?page_id='.USTSTABLEBOOKINGCALENDAR_PAGEID.'&tblid='.$tbl->ID.'&tblshape='.$shape.'" ><img style="width:300px;height:300px;" src="'.$tbl_img.'" /> </a>
                  <div style="clear:both;">'.$tbl->post_title.' </div>  
                  <div style="clear:both;">No Of Seat: '.$noofseat.' </div>   
                </div>';
   }
   	
   
   $output .='</div>';
	 return $output;
}
add_shortcode('gen_restbl_tables','restbl_tables_shortcode');