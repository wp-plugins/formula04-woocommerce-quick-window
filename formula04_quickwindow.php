<?php /**
 * Plugin Name: Formula04 Window
 * Plugin URI: http://formula04.com/plugins/quickwindow
 * Description: Just a simple little plug that adds a product popup window to woocommerce archive pages.  Customers on your site can not only view additional product information normally seen on the single product page; but they can also add products to the cart as well.  
 * Version: 1.0
 * Author: Verb Wit
 * Author URI: http://formula04.com/plugins/quickwindow
 * License: A "Slug" license name e.g. GPL2
 */ 
 
 
defined('ABSPATH') or die("No script Kittens please!");
 
 
 
//----------------------------
////Lets Make  Sure Woo Commerce is installed and running first
//----------------------------
add_action('init', 'compatible_plugin_running');
function myplugin_plugin_path() {
        //gets the absolute path to this plugin directory
			  return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}
function compatible_plugin_running() {
	
	$plugin = 'woocommerce';
	
  	switch($plugin):
	case('woocommerce'):
	
	  if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	   //WooCommerce is running, lets change load order of woocommerce template files.
	
	  //wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
	  //wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jquery.leanModal.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'lean_model', plugin_dir_url( __FILE__ ) . 'js/jquery.leanModal.min.js', array( 'jquery' ), 1, false );
		
		add_action('woocommerce_after_shop_loop_item_title', 'add_quick_window_button');
		add_action('wp_head', 'add_quick_window_button_scripts');
		//add_filter( 'woocommerce_locate_template', 'myplugin_woocommerce_locate_template', 10, 3 );
		
		add_action('wp_ajax_my_action', 'my_action_callback');
		add_action('wp_ajax_nopriv_my_action', 'my_action_callback');

		$options = get_option( 'formula04_settings' );
		
		if(  isset(  $options['formula04_use_f04_css']) && $options['formula04_use_f04_css'] == 1):
		
			add_action('wp_head', 'formula04_quick_window_button_css');
		else:
				
		
		endif;

		return true;
	   }else{
	  return false;
	  }
	  break;
	endswitch;//switch($plugin):
}// END function compatible_plugin_running($plugin = 'woocommerce') {
	
 
/* 
 
 if ( $overridden_template = locate_template( 'some-template.php' ) ) {
   // locate_template() returns path to file
   // if either the child theme or the parent theme have overridden the template
   load_template( $overridden_template );
 } else {
   // If neither the child nor parent theme have overridden the template,
   // we load the template from the 'templates' sub-directory of the directory this file is in
   load_template( dirname( __FILE__ ) . '/templates/some-template.php' );
 }*/

//----------------------------
////Changing load order of woocommerce template files AND set it to load files from our plugin folder if they are not found in the THEMES woocommerce template folder
 //add_filter( 'woocommerce_locate_template', 'myplugin_woocommerce_locate_template', 10, 3 );
//----------------------------
function myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {
  global $woocommerce;
  $_template = $template;
  if ( ! $template_path ) $template_path = $woocommerce->template_url;
  $plugin_path  = myplugin_plugin_path() . '/woocommerce/';
				 
  // Look within passed path within the theme - this is priority
  $template = locate_template(
							array(
							  $template_path . $template_name,				 
							  $template_name
							 ));
  // Modification: Get the template from this plugin, if it exists
  if ( ! $template && file_exists( $plugin_path . $template_name ) )
	$template = $plugin_path . $template_name;
				 
  // Use default template
  if ( ! $template )				 
	$template = $_template;

  // Return what we found
	 return $template;
	
}//function myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {
//End changing load order of woocommerce template files


//----------------------------
////Add Quick Window Button HTML
//----------------------------
function add_quick_window_button() {
	global $post;
		
	//Make sure the global var is a product
	//Only want the button to show on WooCommerce Products for the time being.
	if (get_post_type( $post )  &&  get_post_type( $post ) === 'product'):
	else:
	return;
	endif;
	$product_id = $post->ID;
	//Our Plugin Options
	$options = get_option( 'formula04_settings' );

	$f04_quickwindow_button_text = isset(  $options['formula04_quick_window_button_text']  )   && strlen(trim($options['formula04_quick_window_button_text'])) > 1 ? $options['formula04_quick_window_button_text'] :  'Quick Window';
	//Actual Html For Quick Button?>  
	<a rel="leanModal" class="quick_button button" href="#quick_window_wrapper" data-quick_window_id="<?php echo $product_id; ?>"><?php echo $f04_quickwindow_button_text; ?></a>
<?php }
//END Add Quick Window Button 

//----------------------------
////Add Scripts and styles needed to make the button and cart functions work properly
//----------------------------
function add_quick_window_button_scripts(){?>
	<?php //wc-add-to-cart-variation
 	$handle = 'wc-add-to-cart-variation';
 	$list = 'registered';
   
	//Is the variation script registered?
	if (wp_script_is( $handle, $list )) {
		//It is registered, now is it loaded?    
		 wp_enqueue_script( 'wc-add-to-cart-variation' );	
		 //wp_enqueue_script( 'wc-single-product' );		
		// return;
	} else {
	//Variation Script is not registered
		 //  wp_register_script( 'fluidVids.js', plugin_dir_url(__FILE__).'js/fluidvids.min.js');
		 //  wp_enqueue_script( 'fluidVids.js' );
}

//The Javascript
?>		
<script type="text/javascript">

<?php //Add Quick Window Element to Dom ?>
jQuery( document ).ready(function() {
		jQuery('body').append('<div id="quick_window_wrapper"></div>');	
});



<?php //Variation Form Reset Button ?>
jQuery(document).on('click', '#quick_window_wrapper .reset_variations', function(e){
	e.preventDefault();
	jQuery(this).parents('#quick_window_wrapper form.variations_form')[0].reset();
});

<?php //A bunch of javascript functions needed for the button to function properly ?>
jQuery(function() {
	 jQuery('a[rel*=leanModal]').leanModal({ top : 10, closeButton: ".modal_close" });
	 //jQuery('a[rel*=leanModal]').leanModal({ top : 200, closeButton: ".modal_close" });
	 
	<?php //control Quick Button Click ?>
	 jQuery(document).on('click', 'a.quick_button', function(e){	
		e.preventDefault();
	 <?php //Empty Out Quick Container ?>
		jQuery('#quick_window_wrapper').html('');	    	  
		<?php //Product ID ?>
		var $triggerID = jQuery(this).attr('data-quick_window_id'); 
		<?php 
		  //Lets do some ajax and test if we can find the product. 
		  //Add Ajax Nonce
		  $ajax_nonce = wp_create_nonce( "my-special-string" );	 ?>
		  
		  var $wwad = 'get_quick_window';  
		  var data = {
			  'action': 'my_action',
			  'security': '<?php echo $ajax_nonce; ?>',
			  'wwad' : $wwad,
			  'quick_id': $triggerID
		  };
		  
		  // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		  jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
			  
			  <?php //console.log(data); ?>
			  <?php //console.log(response); ?>
			 //Json Respnose			  
			  var jsonRes = JSON.parse(response);
			  var $product_type = jsonRes.product_type;
										  
			  <?php //Load Quick Window Content we recieved from ajax ?>
			  jQuery('#quick_window_wrapper').prepend('<div data-quick_window_product_id="'+$triggerID+'" class="quick_window_window" id="quick_window_'+$triggerID+'">'+jsonRes.html+'</div>');			
					  
			  switch($product_type){
				case "simple":
				<?php //Have to add data-product_id and product id As Wells as data-quantity and the product quantity  As Well as the extra classes to make the button work with Ajax  ?>
				jQuery('#quick_window_wrapper').find('.single_add_to_cart_button').attr('data-product_id', $triggerID).attr('data-quantity', '1').addClass('add_to_cart_button product_type_simple');		
				break; 
				
				case "variable":
				 jQuery('#quick_window_wrapper').find('.single_add_to_cart_button').attr('data-product_id', $triggerID).attr('data-quantity', '1').addClass('add_to_cart_button product_type_'+$product_type);
				break; 
				
				default:
				
				break; 			  
				  
			  }//switch($product_type){
		 });  //jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
	 })<?php //jQuery(document).on('click', 'a.quick_button', function(e){?>
	 
	 
	<?php //Change product quantity on ajax button in quickview window when quantity field is changed.?>
	jQuery(document).on("change", "#quick_window_wrapper form.cart input.qty", function() {
	if (this.value === "0")
			this.value = "1";
			jQuery("#quick_window_wrapper form.cart").find("button[data-quantity]").attr("data-quantity", this.value);
	}); //jQuery(document).on("change", "#quick_window_wrapper form.cart input.qty", function() {
	  
	  
	  
	  
	  <?php //control Variation selectbox change ?>
	 jQuery(document).on('change', '#quick_window_wrapper table.variations select', function(e){	
			e.preventDefault();
			var $all_variations = {};
			var $the_altered_select_box = jQuery(this);
			
			
			//Put all our varations into an array.
			jQuery('table.variations select').each(function(index, element) {
			   
			   //Had to default this to an empty space because otherwise the key(att. name) value(empty) pair will not pass to our ajax
			   var $attribute_value = jQuery(this).val().length > 0 ? jQuery(this).val() : ' '  ;
			   var $attribute_name = jQuery(this).attr('name').length > 0 ? jQuery(this).attr('name') : false  ;
			   
			   //If we are missing a value or name then go to next attribute
			   if(!$attribute_value || !$attribute_name){
				return;
			   }
			   
			   $all_variations[$attribute_name] = $attribute_value;
		});//jQuery('table.variations select').each(function(index, element) {
			//console.log($all_variations);
							
			
	  <?php //Get Product ID ?>
			var $triggerID = jQuery('.quick_window_window').attr('data-quick_window_product_id'); 
	 
			<?php $ajax_nonce = wp_create_nonce( "my-special-string" ); ?>
				
			var $wwad = 'update_variable_product';   
			var data = {
				'action': 'my_action',
				'security': '<?php echo $ajax_nonce; ?>',
				'wwad' : $wwad,
				'quick_id': $triggerID,
				'selected_attributes': $all_variations
			};
			
			console.log(data);
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		 
		 
		  jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
			 //Json Respnose		
			 console.log(response);	  
			  //GET Our JSON Response
			  var jsonRes = JSON.parse(response);
			  
			   //We failed to find a variation match
			  if(jsonRes.hasOwnProperty('failure')){
			  //	
			    $the_altered_select_box.parents('form.variations_form').find('input[name=variation_id]').val('');
			    jQuery('.single_variation_wrap').slideUp(300);	  

			  }else{
				  
				var $variation_id = jsonRes.variation_id;
				var addtocartbutton = jsonRes.add_to_cart_button;
				var $use_ajax = jsonRes.use_ajax;
				
				
				if(!isNaN(parseFloat($variation_id)) && isFinite($variation_id)){
				//We Found a Valid Variation ID
				//Are we using ajax with this button?
					if($use_ajax === 'yes'){
						jQuery('.quick_window_window .add_to_cart_button.product_type_variable').addClass('use_ajax');
					}else{
						jQuery('.quick_window_window .add_to_cart_button.product_type_variable').removeClass('use_ajax');
					}//if($use_ajax === 'yes'){
								  
					//Show Add To Cart Button.
					jQuery('.single_variation_wrap').slideDown(300);
					
					}else{
					//We Don't have a valid variation ID 	 
					jQuery('.single_variation_wrap').slideUp(300); 
					}// if(!isNaN(parseFloat(response)) && isFinite(response)){

					//Add Variation ID to hidden input in variation form
					$the_altered_select_box.parents('form.variations_form').find('input[name=variation_id]').val($variation_id);	
				}// if(jsonRes.hasOwnProperty('failure')){
			})//jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {			 
		 });//jQuery(document).on('change', 'table.variations select', function(e){	
	  
	  
	  <?php //Ajax Variation Add To Cart ?>
	   jQuery(document).on('click', '.add_to_cart_button.product_type_variable.use_ajax ', function(e){
		  e.preventDefault(); 
		  var $add_to_cart_button = jQuery(this);
		  var $triggerID = jQuery('.quick_window_window').attr('data-quick_window_product_id'); 
		  var $wwad = 'ajax_variation_add_to_cart';
		  var $variation_id = jQuery('.quick_window_window').find('input[name=variation_id]').val()
		  var $all_variations = {};
		  
		  jQuery('table.variations select').each(function(index, element) {
			   
			   var $attribute_value = jQuery(this).val().length > 0 ? jQuery(this).val() : ' '  ;
			   var $attribute_name = jQuery(this).attr('name').length > 0 ? jQuery(this).attr('name') : false  ;
			   
			   //If we are missing a value or name then go to next attribute
			   if(!$attribute_value || !$attribute_name){
				return;
			   }
			 
				
				$all_variations[$attribute_name] = $attribute_value;
			});//jQuery('table.variations select').each(function(index, element) {
					  
		  //Add Loading Animation.
		  jQuery(this).addClass( 'loading' );
		  //Ajax Variables			
			var data = {
				'action': 'my_action',
				'security': '<?php echo $ajax_nonce; ?>',
				'wwad' : $wwad,
				'quick_id': $triggerID,
				'variation_id': $variation_id,
				'quantity': $add_to_cart_button.attr('data-quantity'),
				'selected_attributes': $all_variations
			};
			
				
		  jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
			   
			   //If no response
				if ( ! response ){
					return;		
				}else{}
				
				//Remove Loading
				$add_to_cart_button.removeClass('loading')
									
				//If Error Response
				if ( response.error && response.product_url ) {
					window.location = response.product_url;
					return;
				}else{}
				
				
				// Block widgets and fragments
				jQuery('.shop_table.cart, .updating, .cart_totals,.widget_shopping_cart_top').fadeTo('400', '0.6').block({message: null, overlayCSS: {background: 'transparent url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } } );
		
				// Changes button classes
				$add_to_cart_button.addClass( 'added' );

		  })//jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {

	   
	   })//jQuery(document).on('click', '.ajax_variation_add_to_cart', function(e){
 
});<?php //  jQuery(function() { ?>

 
</script>   
<style>
    #lean_overlay {
    position: fixed;
    z-index:100;
    top: 0px;
    left: 0px;
    height:100%;
    width:100%;
    background: #000;
    display: none;
	
	}
	#quick_window_wrapper{
	background-color:#ffffff;
	width:800px;
	max-width:99%;
	display:none;
	left:auto;
	max-height:100%;
	overflow-y:auto;
	
	}
	.quick_button {
	max-width:100%;
	}
</style>
<?php 
}//	add_quick_window_button_scripts()
	

	function my_action_callback() {
	global $wpdb; // this is how you get access to the database

	if(!isset( $_POST['quick_id'])):
		return;
	endif;
	
	//Our Plugin Options
	$options = get_option( 'formula04_settings' );
	
	//What We Are Doing
	$wwad = $_POST['wwad'];
	$ID = intval( $_POST['quick_id'] );
	
		ob_start();	
		
		switch($wwad):
		
		case('get_quick_window'):
		global $woocommerce;
		
		//echo do_shortcode('[product_page id="'.$ID.'"]');
		//woocommerce_get_template_part( 'content', 'single-product' );
		//global $product, $post, $woocommerce;
		//Do Query to Get Product
		$quick_window_args = array(
			'p' => $ID,
			'post_type' => 'product',
			'post_status'  => 'publish'		
		);
		
		//Do quick window query
		$quick_window_query = new WP_Query($quick_window_args);
		
		
		$product = '';
	 	$product_type = '';
		
		//Do we have any prodcts? 
		if ( $quick_window_query->have_posts() ) : while ( $quick_window_query->have_posts() ) : $quick_window_query->the_post(); ?>
        <?php $product = get_product( get_the_ID() );
	 		  $product_type = $product->product_type; ?>
        
        
             <div class="woocommerce quick-window product" data-product_id="<?php echo $ID; ?>">
                  
				 <?php if(  !isset(  $options['formula04_quickwindow_template'] ) ): 
				  	$template = '';
                 else:
                 	$template = $options['formula04_quickwindow_template'];                        
				 endif; ?>
                 
                 
				  <?php //Load Quick Window Template 
				  		//Which Template are we loading?
						switch($template):
						  //Load Woocommerce single template
						  
						  case('woocommerce_single'):                   
							  //Look and see if the client has loaded their own custom template.
							echo woocommerce_get_template_part( 'content', 'single-product' );
						  break; 			  
						  case('formula04_quickwindow_template'):
						  default: 						
							//Is there a template in their theme folder?
							$formula04_template_file =  get_stylesheet_directory() . '/formula04_quickwindow_template.php';
							  if(file_exists ( $formula04_template_file )):							  
							  
							  else:
							  	//Load My Own Template
							  	$formula04_template_file =  myplugin_plugin_path().'/formula04_quickwindow_template.php';
							  endif;
							  load_template( $formula04_template_file, $require_once = true );						
						endswitch;//switch($template):
						
						//Load Correct Template
						
				 ?>
             </div><?php //.woocommerce.quick-window.product ?>
		 <?php
		  endwhile;
		  wp_reset_postdata();
		  
		 else:
		  	
		 //No Product Found
		  
		 endif;		  		
		
		  $single_product_quick_window_output['html']  = ob_get_contents();
		  ob_end_clean();
		  
		  $single_product_quick_window_output['product_type'] = $product_type;
		  echo json_encode($single_product_quick_window_output);	
	
	
	break; //END  Case:get_quick_window
	
	case('update_variable_product'):
		global $woocommerce;
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($_POST,true))."</pre>";
		//Attributes currently selected on product
		$selected_attributes = $_POST['selected_attributes'];
		foreach($selected_attributes as  $key => $value):
			if($value ===  ' '):
				$selected_attributes[$key] = '';	
			endif;
		endforeach;
	
		
		
		$selected_attributes_count =  count($selected_attributes);
	
		//Do Query to Get Product
		$update_variable_product_args = array(
			'p' => $ID,
			'post_type' => 'product',
			'post_status'  => 'publish'		
		);
		
		//Do quick window query
		$update_variable_product_query = new WP_Query($update_variable_product_args);
		
		//Do we have any prodcts? 
		if ( $update_variable_product_query->have_posts() ) : while ( $update_variable_product_query->have_posts() ) : $update_variable_product_query->the_post(); 

		$product = get_product( get_the_ID() );
		$product_type = $product->product_type;
		$available_variations = $product_type === 'variable' ? $product->get_available_variations() : false;
				
		//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($selected_attributes,true))."</pre>";
		//Do we have any available variations to this product
		if($available_variations):
		
			//We currently have not found the variation ID
			$variation_found = false;
			//Go through Each Variation and see if we find all our matching attributes but no more.
			foreach($available_variations as $key => $one_variation):
			
			
			
				//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($one_variation,true))."</pre>";
				//echo 'This is Variation ID number '.$one_variation['variation_id']." \n";
				//echo 'This Variation has the attributes:'." \n";
				foreach($one_variation['attributes'] as  $variation_attribute_name => $variation_attribute_value):
				
					//echo 'Variation Name = '.$variation_attribute_name." \n";
					//echo 'Variation Value = '.$variation_attribute_value." \n";
					$variation_array[$variation_attribute_name] = $variation_attribute_value;	
				endforeach;
		
				$difference = array_diff_assoc($variation_array, $selected_attributes);
				//echo" \n <hr />DIFFERENCE ARRAY \n <pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($difference,true))."</pre>";
				
				//If we want to user ajax with variation add to cart.
				if(empty($difference)):
					$extra_class = '';
					$variation_found['use_ajax'] = 'yes';
					//echo 'Variation ID is '.$one_variation['variation_id'];
					$variation_id = $one_variation['variation_id'];
					$add_to_cart_button = '<a href="'.esc_url( $product->add_to_cart_url() ).'&variation_id='.$variation_found.'&attribute_stock-colors=1" class="'.$extra_class.'">Add to Cart</a>';
					$variation_found['variation_id'] = $variation_id;
					$variation_found['add_to_cart_button'] = $add_to_cart_button;
					echo json_encode($variation_found);
					die();
					break;				
				endif;
				//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($one_variation,true))."</pre>";
			endforeach;//END foreach($available_variations as $key => $one_variation):
		
		if(!$variation_found):
			
			$variation_found['failure'] = 'No Matching Variations Found';
			echo json_encode($variation_found);
			//If we get here, no varuation was ever found.
			//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($available_variations,true))."</pre>";
		endif;
		
		die();
		else:
		
		
		endif;	/*END if($available_variations):*/
			
	    endwhile; //while ( $update_variable_product_query->have_posts() ) :
		wp_reset_postdata();
		else:
		  //No Product Found
		endif; //END if ( $update_variable_product_query->have_posts() ) 
	
	
	
	
	
	
	break;//case('update_variable_product'):
	case('ajax_variation_add_to_cart'):
		global $woocommerce;
		
		
		$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['quick_id'] ) );
		$quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
		$variation_id = $_POST['variation_id'];
		$variation  = $_POST['selected_attributes'];
		
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		
		
		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation  ) ) {
			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				wc_add_to_cart_message( $product_id );
			}
	
			// Return fragments
			WC_AJAX::get_refreshed_fragments();
		} else {
			$this->json_headers();
	
			// If there was an error adding to the cart, redirect to the product page to show any errors
			$data = array(
				'error' => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
				);
			echo json_encode( $data );
		}
	
	break;
	default:	
	   		
endswitch;
		
	die(); // this is required to terminate immediately and return a proper response
}//Ajax Actiohn


//
function formula04_quick_window_button_css(){?>
<style type="text/css">
@import url(http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,800,700,600,300);
<?php //font-family: 'Open Sans', sans-serif; ?>


<?php if(is_admin_bar_showing()): ?>
#quick_window_wrapper{ margin-top:30px;}
<?php endif;  ?>

.quick_window_window{ padding: 10px;}


.quick_window_window .quick-window-content .product_title {
	font-family: 'Open Sans', sans-serif;
	font-size: 2em;
	font-weight: 300;
	margin-bottom:0px;
}
.quick_window_window .quick-window-content .price .amount{ 
font-size: 2.3em;
font-family: 'Open Sans', sans-serif;
font-weight: 600;
margin-bottom: 0px;
line-height: 1em;
letter-spacing: -.06em;
}

.quick_window_window .quick-window-image.images{ margin: 0px 10px 10px;}
.quick_window_product_page_link{}
.quick-window-content{ float:left; width:49%;} 
.quick-window-image images{ float:left;width:49%; margin-left:1%;}
.quick-window-content:AFTER{content:""; clear:left;}

.quick_window_window .variations tbody tr select{
	padding:5px;
	
}
.quick_window_window .variations tbody tr{
	margin-bottom:10px;
	
}

</style>	
<?php }//formula04_quick_window_button_css

	



//----------------------------
//BACKEND STUFF
//----------------------------
// Hook for adding admin menus
add_action('admin_menu', 'formula_04_settings_menu');
// action function for above hook
function formula_04_settings_menu() {
    // Add a new submenu under Settings:
    add_options_page(__('Formula 04 Quick Window','formula04'), __('F04 Quick Window','formula04'), 'manage_options', 'formula04-quick-window', 'formula_04_settings_page');
}
// mt_settings_page() displays the page content for the Test settings submenu
function formula_04_settings_page() {
    echo "<h2>" . __( 'Formula 04 Quick Window Settings', 'formula04' ) . "</h2>";
	?>
	<form action='options.php' method='post'>		
		<?php
		settings_fields( 'F04QuickWindowSet' );
		do_settings_sections( 'F04QuickWindowSet' );
		submit_button();
		?>
		
	</form>
	<?php 
	
}//formula_04_settings_page




//ADD SETTINGS PAGE(S)

//add_action( 'admin_menu', 'formula04_add_admin_menu' );
add_action( 'admin_init', 'formula04_settings_init' );
function formula04_settings_init(  ) { 

	register_setting( 'F04QuickWindowSet', 'formula04_settings' );

	add_settings_section(
		'formula04_F04QuickWindowSet_section', 
		__( 'Use this section to customize Formula 04 Quick Window ', 'formula04' ), 
		'formula04_settings_section_callback', 
		'F04QuickWindowSet'
	);
	
	add_settings_field( 
		'formula04_quick_window_button_text', 
		__( 'Formula04 QuickWindow Button Text', 'formula04' ), 
		'formula04_quick_window_button_text_render', 
		'F04QuickWindowSet', 
		'formula04_F04QuickWindowSet_section' 
	);	
	

	add_settings_field( 
		'formula04_use_f04_css', 
		__( 'Use Formula04 CSS?', 'formula04' ), 
		'formula04_use_f04_css_render', 
		'F04QuickWindowSet', 
		'formula04_F04QuickWindowSet_section' 
	);
	
	
	add_settings_field( 
		'formula04_quickwindow_template', 
		__( 'Pop Up Window Content Setting', 'formula04' ), 
		'formula04_quick_window_content_render', 
		'F04QuickWindowSet', 
		'formula04_F04QuickWindowSet_section' 
	);

	

}
function formula04_use_f04_css_render(  ) { 
	$options = get_option( 'formula04_settings' );?>
	<input type='checkbox' name='formula04_settings[formula04_use_f04_css]' <?php echo $options  && isset($options['formula04_use_f04_css']) && $options['formula04_use_f04_css'] ? 'checked' : '';  /* checked( $options['formula04_use_f04_css'], 1 );*/ ?> value='1'>
	<span class=""><?php _e('Load our built in css to style the product quick window window', 'formula04'); ?></span>
    
	<?php
}
function formula04_quick_window_content_render(  ) { 
	$options = get_option( 'formula04_settings' );
	$f04_quick_window_template = isset(  $options['f04_quick_window_template']  );?>
    
    <select name='formula04_settings[formula04_quickwindow_template]'>
        <option value="formula04_quickwindow_template"  <?php echo isset($options['formula04_quickwindow_template']) && $options['formula04_quickwindow_template'] ==  'formula04_quickwindow_template'? 'selected' : '';?>  >FORMULA04 Default/Custom Template</option>
        <option value="woocommerce_single"  <?php echo isset($options['formula04_quickwindow_template']) && $options['formula04_quickwindow_template'] ==  'woocommerce_single'? 'selected' : '';?>>Product Page Template from WooCommerce</option>
    </select>
    <span class=""><?php _e('Select the quick window content template', 'formula04'); ?></span>
<?php
}


function formula04_quick_window_button_text_render(  ) { 
	$options = get_option( 'formula04_settings' );
	$f04_quick_window_button_text = isset(  $options['formula04_quick_window_button_text']  )  ? $options['formula04_quick_window_button_text'] :  '';?>
    <input type="text" value="<?php echo $f04_quick_window_button_text; ?>" name="formula04_settings[formula04_quick_window_button_text]" />
    
   
<?php
}


function formula04_settings_section_callback(  ) { ?>
	<hr />
	<?php 
	//echo __( 'This section description', 'formula04' );
}

class WC_Settings_Tab_Demo {
 
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_demo', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_demo', __CLASS__ . '::update_settings' );
    }
    
    
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_demo'] = __( 'ADD WOOCOMMERCE TAB', 'woocommerce-settings-tab-demo' );
        return $settings_tabs;
    }
 
 
    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }
 
 
    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }
 
 
    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {
 
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Section Title', 'woocommerce-settings-tab-demo' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_demo_section_title'
            ),
            'title' => array(
                'name' => __( 'Title', 'woocommerce-settings-tab-demo' ),
                'type' => 'text',
                'desc' => __( 'This is some helper text', 'woocommerce-settings-tab-demo' ),
                'id'   => 'wc_settings_tab_demo_title'
            ),
            'description' => array(
                'name' => __( 'Description', 'woocommerce-settings-tab-demo' ),
                'type' => 'textarea',
                'desc' => __( 'This is a paragraph describing the setting. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda. Lorem ipsum yadda yadda yadda.', 'woocommerce-settings-tab-demo' ),
                'id'   => 'wc_settings_tab_demo_description'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_demo_section_end'
            )
        );
 
        return apply_filters( 'wc_settings_tab_demo_settings', $settings );
    }
 
}
 
WC_Settings_Tab_Demo::init();





