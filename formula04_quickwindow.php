<?php /**



* Plugin Name: Formula04 Quick Window
* Plugin URI: http://formula04.com/quickwindow
* Description: Just a simple little plug that adds a product popup window to woocommerce archive pages.  Customers on your site can not only view additional product information normally seen on the single product page; but they can also add products to the cart as well.  
* Version: 2.0.7
* Author: Verb Wit
* Tested up to: 4.1.1
* Author URI: https://profiles.wordpress.org/verb_form04/
* License: TOKILL
*/ 

defined('ABSPATH') or die("No script Kittens please!");


//----------------------------
//Do we need in include any files
//----------------------------
//require_once( plugin_dir_path( __FILE__ ) . 'file.php' );

//----------------------------
//Activation and Deactivation hooks to run when running classes from another file
//----------------------------
//register_activation_hook( __FILE__, array( 'utComments', 'activate' ) );
//register_deactivation_hook( __FILE__, array( 'utComments', 'deactivate' ) );
//End Comments Stuff




			
			
if ( ! class_exists( 'Form04WooQuickWindow' ) ) {
	class Form04wooquickwindow
	{
		/**
		 * Tag identifier used by file includes and selector attributes.
		 * @var string
		 */
		protected $tag = 'form04wooquickwindow';

		/**
		 * User friendly name used to identify the plugin.
		 * @var string
		 */
		protected $name = 'Form04WooQuickWindow';

		/**
		 * Current version of the plugin.
		 * @var string
		 */
		protected $version = '2.0';

		/**
		 * List of options to determine plugin behaviour.
		 * @var array
		 */
		protected $options = array();

		/**
		 * List of settings displayed on the admin settings page.
		 * @var array
		 */
		
		
		
		//----------------------------
		//CONSTRUCT
		//----------------------------
		
		public function __construct()
		{
			
			
			if ( $options = get_option( 'formula04_quickwindow_settings' ) ) {
					$this->options = $options;
			}else{
				
		
			}
					
		
		if ( is_admin() ) {
		 //Add Settings Menu Link	
		 add_action('admin_menu', array( &$this, 'formula_04_settings_menu' ));
		 //Load plugin settings page options
		 add_action( 'admin_init', array( &$this, 'formula04_quickwindow_settings_init' ));
				 
			//If we just installed plugin then change install messsage
			if( get_transient( 'formula04_quick_window_activated' ) ):
						add_filter( 'gettext',  array( &$this, 'plugin_activation_string'), 99, 3 );
			endif;		 
		
			$plugin = plugin_basename( __FILE__ );
			add_filter( "plugin_action_links_$plugin", array( &$this, 'plugin_add_settings_link') );

		
	
			
		}else{
		//We are in the frontend, or Not Admin	
		//Is our compatible pluggin running.
		add_action( 'init', array( &$this, 'compatible_plugin_running' ) );	
		
			
	
			
					
		}//if ( is_admin() ) {
		
		 add_action('wp_ajax_form04quickwindow', array( &$this, 'form04quickwindow_callback' ));		
		 add_action('wp_ajax_nopriv_form04quickwindow', array( &$this, 'form04quickwindow_callback' ));					
	

	}
	//----------------------------
	//END CONSTRUCT
	//----------------------------
		
	
		
		
		//----------------------------
		//Begin Functions
		//----------------------------
		public function compatible_plugin_running() {
		 $options = $this->options;	
		//Currently Just Compatible With WooCommerce
		$plugin = 'woocommerce';
	
		switch($plugin):
			case('woocommerce'):
			  if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			  		 
			 //add_shortcode( 'formula04quickwindow', 'add_quick_window_button_shortcode' );
			 add_shortcode( $this->tag, array( &$this, 'add_quick_window_button_shortcode' ) );
			 
						 
			//Should we auto show the buttons				
			 if(  isset(  $options['formula04_quickwindow_button_display']) && $options['formula04_quickwindow_button_display'] == 1):
			  	//Automatically Add button after product title
			 	add_action('woocommerce_after_shop_loop_item_title', array( &$this, 'add_quick_window_button' ));
			  else:
			  //Don't Auto Load Buttons :(
			  endif;//if(  isset(  $options['formula04_use_f04_css']) && $options['formula04_use_f04_css'] == 1):
			 
			 
			 
			 
			 
			 
			 
			 
			 //Add Scripts and styles
			 add_action('wp_head', array( &$this, 'add_quick_window_button_scripts' ));	
				
			 //add_filter( 'woocommerce_locate_template', 'myplugin_woocommerce_locate_template', 10, 3 );
	
			 
			  if(  isset(  $options['formula04_use_f04_css']) && $options['formula04_use_f04_css'] == 1):
				add_action('wp_head', array( &$this, 'formula04_quick_window_button_css') );
			  else:
			  //Don't Load Form04 Css :(
			  endif;//if(  isset(  $options['formula04_use_f04_css']) && $options['formula04_use_f04_css'] == 1):
  				
			  //Woocommerce is loaded
			  return true;
	  
			 }else{	
			//Woocommerce is NOT loaded 
			return false;
			}
		   break;//case('woocommerce'):
	  
		endswitch;//switch($plugin):
	
	}// END function compatible_plugin_running($plugin = 'woocommerce') {

		
		

	//Function that adds quickwindow button to page.
	public function add_quick_window_button() {
	
		global $post;
		//Make sure the global var is a product
		//Only want the button to show on WooCommerce Products for the time being.
		if (get_post_type( $post )  &&  get_post_type( $post ) === 'product'):
		else:
			return;
		endif;
				
		echo do_shortcode('[form04wooquickwindow]');
	}//public function add_quick_window_button() {	




	//Shortcode function that creates the actual HTML for the quick button
	public function add_quick_window_button_shortcode( $atts ) {
		
		
		global $post;
		$options = $this->options;
		$f04_quickwindow_button_text = isset(  $options['formula04_quick_window_button_text']  )  && strlen(trim($options['formula04_quick_window_button_text'])) > 1 ? $options['formula04_quick_window_button_text'] :  'Quick Window';
		$f04_quickwindow_button_extraclasses = isset(  $options['formula04_quick_window_button_classes']  )  && strlen(trim($options['formula04_quick_window_button_classes'])) > 1 ? $options['formula04_quick_window_button_classes'] :  '';
		$formula04_quick_window_button_id = isset(  $options['formula04_quick_window_button_id']  )  && strlen(trim($options['formula04_quick_window_button_id'])) > 1 ? $options['formula04_quick_window_button_id'] :  false;
	
		/**
		* Define the array of defaults
		*/ 
	
		$defaults = array(
			'button_text' => $f04_quickwindow_button_text,
			'class' => $f04_quickwindow_button_extraclasses,
			'id' => $formula04_quick_window_button_id,
			'product_id' => $post->ID ? $post->ID : false	
		);
	
		/**
		* Parse incoming $args into an array and merge it with $defaults
		*/ 
	
		$args = wp_parse_args( $atts, $defaults );
		extract($args);
			//If no product ID we should not be showing quick button
			if(!$product_id):
				return ('Invalid Product ID');				
			endif;	
			ob_start();
		?><a class="quick_button button <?php
		
			$unique_name = $this->toAscii(get_the_title($product_id)).'_'.$product_id;
		
			echo $class ? $class : '';?>" <?php 
			echo $id ? 'id = "'.$id.'"' : '';
			?><?php /*?>href="#quick_window_wrapper"<?php */?><?php        
			?>href="#<?php echo $unique_name; ?>" 
			
			data-quick_window_id="<?php echo $product_id; ?>"><?php
			echo $button_text;
		?></a><?php //<a rel="leanModal" class="quick_button butto ?>
	
	<?php  //.quick_button
	   $add_quick_window_button_shortcode = ob_get_contents();
	   ob_end_clean();
	   return $add_quick_window_button_shortcode;
	}//add_quick_window_button_shortcode




	  //----------------------------
	  // Utilities
	  //----------------------------
	  
	  //Converts string into a string that is suitableforurluse
	  public function toAscii($str, $replace=array(), $delimiter='-') {
	   setlocale(LC_ALL, 'en_US.UTF8');
	   if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	   }
	  
	   $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	   $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	   $clean = strtolower(trim($clean, '-'));
	   $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
	  
	   return $clean;
	  }
	  
	  
	  public function myplugin_plugin_path() {
       //gets the absolute path to this plugin directory
		  return untrailingslashit( plugin_dir_path( __FILE__ ) );
      }
	  
	  
	  //----------------------------
	  //  END Utilities
	  //----------------------------

	
		
public function add_quick_window_button_scripts(){?>

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
	//Variation Script is not registered we need to register it/add it manually
		 //wp_register_script( 'somescript.js', plugin_dir_url(__FILE__).'js/somescript.min.js');
		 //wp_enqueue_script( 'somescript.js' );
	}//if (wp_script_is( $handle, $list )) {
//The Javascript?>
	<script type="text/javascript">
	<?php //Add Quick Window Element to Dom ?>
	jQuery( document ).ready(function() {
		//jQuery('body').append('<div id="quick_window_wrapper"></div>');	
		var $pop_container_up_html = '<div id="quick_window_wrapper" class="form04quickwindow_overlay"><div class="popup"><a class="close close_quickwindow" href="#">&times;</a><div class="popupcontent"></div></div>';
		jQuery('body').append($pop_container_up_html);	
	});

	<?php //Variation Form Reset Button ?>
	jQuery(document).on('click', '.form04quickwindow_overlay .reset_variations', function(e){
		e.preventDefault();
		var $current_id =  jQuery('.form04quickwindow_overlay').attr('id');
		jQuery(this).attr('href', "#"+$current_id )
	<?php //Prevent changing target and closing our pop up. ?>
		jQuery(this).parents('form.variations_form')[0].reset();
	});



<?php //A bunch of javascript functions needed for the button to function properly ?>
jQuery(function() {
	//jQuery('a[rel*=leanModal]').leanModal({ top : 10, closeButton: ".modal_close" });
    //jQuery('a[rel*=leanModal]').leanModal({ top : 200, closeButton: ".modal_close" });
    <?php //control Quick Button Click ?>
	 jQuery(document).on('click', 'a.quick_button', function(e){
		 var $href = jQuery(this).attr('href');
		 $href = $href.replace('#','');
		 jQuery('.form04quickwindow_overlay').attr('id', $href  );
	   	//e.preventDefault();
        <?php //Product ID ?>
		var $quick_window_id = jQuery(this).attr('data-quick_window_id');
		
		<?php //Have we already popped this up once ?>
		var $old_window = jQuery('#form04quickwindow_'+$quick_window_id).length	
		if($old_window > 0){
		//Do nothing, pop up window already has content
		 jQuery('.popupcontent').prepend( jQuery('#form04quickwindow_'+$quick_window_id) );	 
		}else{
		  $old_window = false;	
		  //Add Out popup window for this product on to screen.
		  var $new_popup_content = '<div  class="one_form04quickwindow_content" id = "form04quickwindow_'+$quick_window_id+'"><div class="loading_graphic"><div></div><div></div><div></div><div></div><div></div></div></div>';
		  jQuery('.popupcontent').prepend($new_popup_content);	 
		}
		
		//Which Element do we want to be targeting
		var $quickwindow_wrapper = jQuery('#form04quickwindow_'+$quick_window_id);
		
						
		<?php //Empty Out Quick Container ?>
		//jQuery('.popupcontent').html('Loading');	    	  
		<?php 

		  //Lets do some ajax and test if we can find the product. 
		  //Add Ajax Nonce

		  $ajax_nonce = wp_create_nonce( "my-special-string" );	 ?>
		  var $wwad = 'get_quick_window';  
		  var data = {
			  'action': 'form04quickwindow',
			  'security': '<?php echo $ajax_nonce; ?>',
			  'wwad' : $wwad,
			  'quick_id': $quick_window_id
		  };

		  

		  // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		  jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
			 //Json Respnose			  
			  var jsonRes = JSON.parse(response);
			  var $product_type = jsonRes.product_type;
			  //If he have already popped this window up, lets not load it through ajax again.
			  //Lets stick with what we got incase the user has an added to cart message or something like that.				  
			  if($old_window){}else{	
			  <?php //Load Quick Window Content we recieved from ajax ?>
			  $quickwindow_wrapper.html('<div data-quick_window_product_id="'+$quick_window_id+'" class="quick_window_window" id="quick_window_'+$quick_window_id+'">'+jsonRes.html+'</div>');			
			
			  switch($product_type){

				case "simple":
				<?php //Have to add data-product_id and product id As Wells as data-quantity and the product quantity  As Well as the extra classes to make the button work with Ajax  ?>
				$quickwindow_wrapper.find('.single_add_to_cart_button').attr('data-product_id', $quick_window_id).attr('data-quantity', '1').addClass('add_to_cart_button product_type_simple');		
				break; 

				case "variable":
				 $quickwindow_wrapper.find('.single_add_to_cart_button').attr('data-product_id', $quick_window_id).attr('data-quantity', '1').addClass('add_to_cart_button product_type_'+$product_type);
				break; 

        		default:
     			break; 			  
			  }//switch($product_type){
			 }// if($old_window){}else{
	 });  //jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
 })<?php //jQuery(document).on('click', 'a.quick_button', function(e){?>



//Close Quick Windo
//Make sure to set hash to out popupwindow is not active but invisible
jQuery(document).on("click", ".close_quickwindow", function() {
	 window.location.hash = '';
});

	 
<?php //Change product quantity on ajax button in quickview window when quantity field is changed.?>
jQuery(document).on("change", ".one_form04quickwindow_content form.cart input.qty", function() {
	if (this.value === "0")
		this.value = "1";
	jQuery(this).parents('.one_form04quickwindow_content').find("button[data-quantity]").attr("data-quantity", this.value);
}); //jQuery(document).on("change", ".one_form04quickwindow_content form.cart input.qty", function() {	  

<?php //control Variation selectbox change ?>
jQuery(document).on('change', '.one_form04quickwindow_content table.variations select', function(e){	
  e.preventDefault();
  var $all_variations = {};
  var $the_altered_select_box = jQuery(this);
 //GET ID of current pop up box content
   var $this_wrapper =  jQuery(this).parents('.quick_window_window').attr('id');
   var $triggerID =  jQuery(this).parents('.quick_window_window').attr('data-quick_window_product_id');
  
  //Put all our varations into an array.
  jQuery('#'+$this_wrapper+' table.variations select').each(function(index, element) {
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

	<?php $ajax_nonce = wp_create_nonce( "my-special-string" ); ?>
	var $wwad = 'update_variable_product';   
	var data = {
		'action': 'form04quickwindow',
		'security': '<?php echo $ajax_nonce; ?>',
		'wwad' : $wwad,
		'quick_id': $triggerID,
		'selected_attributes': $all_variations
		};
		//console.log(data);
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
 		  jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
			 //Json Respnose		
			 //GET Our JSON Response
			  var jsonRes = JSON.parse(response);
			  //We failed to find a variation match
			  if(jsonRes.hasOwnProperty('failure')){
			    //Clear out variation ID field since none was founbd.
				$the_altered_select_box.parents('form.variations_form').find('input[name=variation_id]').val('');
				//Slide Up Add to Cart Button
			     jQuery('#'+$this_wrapper+' .single_variation_wrap').slideUp(300);	  
			  }else{
				var $variation_id = jsonRes.variation_id;
				var addtocartbutton = jsonRes.add_to_cart_button;
				var $use_ajax = jsonRes.use_ajax;

				if(!isNaN(parseFloat($variation_id)) && isFinite($variation_id)){

				//We Found a Valid Variation ID
				//Are we using ajax with this button?
					if($use_ajax === 'yes'){
						
						 jQuery('#'+$this_wrapper+' .add_to_cart_button.product_type_variable').addClass('use_ajax');
					}else{
						jQuery('#'+$this_wrapper+' .add_to_cart_button.product_type_variable').removeClass('use_ajax');
					}//if($use_ajax === 'yes'){
						//Show Add To Cart Button.
						jQuery('#'+$this_wrapper+' .single_variation_wrap').slideDown(300);
				}else{
					//We Don't have a valid variation ID 	 
					jQuery('#'+$this_wrapper+' .single_variation_wrap').slideUp(300); 
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
		  var $this_wrapper =  jQuery(this).parents('.quick_window_window').attr('id');
		  var $triggerID = jQuery(this).parents('.quick_window_window').attr('data-quick_window_product_id'); 
		  var $wwad = 'ajax_variation_add_to_cart';
		  var $variation_id = jQuery(this).parents('.quick_window_window').find('input[name=variation_id]').val()
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
				'action': 'form04quickwindow',
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
.quick_button {
	max-width:100%;
}
	
	
<?php //loading graphic ?>	

	
	
<?php //All CSS POP UP ?>
<?php //CSS POP UP ?>
.button_success_send {
  font-size: 1em;
  padding: 10px;
  color: #fff;
  border: 2px solid #06D85F;
  border-radius: 20px/50px;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease-out;
  
}
.button:hover {
  background: #06D85F;
}

.form04quickwindow_overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.7);
  transition: opacity 500ms;
  visibility: hidden;
  opacity: 0;
  z-index:-40;
}
.form04quickwindow_overlay:target {
  visibility: visible;
  opacity: 1;
  z-index:4000;
}
.form04quickwindow_overlay:target .popup {
	z-index:40;
	
	
}
.popup {
  margin: 5vh auto;
  padding: 20px;
  background: #fff;
  border-radius: 5px;
  width: 60%;
  position: relative;
  transition: all 5s ease-in-out;
  height:90vh;
  box-sizing:border-box;
  z-index:-20;
  
}

.popup h2 {
  margin-top: 0;
  color: #333;
  font-family: Tahoma, Arial, sans-serif;
}
.popup .close {
  position: absolute;
  top: 20px;
  right: 30px;
  transition: all 200ms;
  font-size: 30px;
  font-weight: bold;
  text-decoration: none;
  color: #333;
}
.popup .close:hover {
  color: #06D85F;
}
.popup .popupcontent {
  max-height: 100%;
  overflow: auto;
  padding-top:5%;
  box-sizing:border-box;
}
.popup .popupcontent .one_form04quickwindow_content{
	display:none;	
}

.popup .popupcontent .one_form04quickwindow_content:first-child{
	display:block;	
}


@media screen and (max-width: 700px){
  .box{
    width: 70%;
  }
  .popup{
    width: 70%;
  }
}	
<?php //ALL CSS POP UP ?>


</style>

<?php 

}//	add_quick_window_button_scripts()
public function form04quickwindow_callback() {
	
		
		global $wpdb; // this is how you get access to the database
		//No Quick ID set then we have no business here
		if(!isset( $_POST['quick_id'])):
			return;
		endif;
		
		//Our Plugin Options
		$options = $this->options;
		
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

		if ( $quick_window_query->have_posts() ) : while ( $quick_window_query->have_posts() ) : $quick_window_query->the_post();
			$product = get_product( get_the_ID() );
	 		$product_type = $product->product_type; 
			  ?><div class="woocommerce quick-window product" data-product_id="<?php echo $ID; ?>"><?php 
			if(  !isset(  $options['formula04_quickwindow_template'] ) ): 
			  	$template = '';
             else:
               	$template = $options['formula04_quickwindow_template'];                        
		    endif;
		  //Load Quick Window Template 
		  //Which Template are we loading?
			switch($template):
			  //Load Woocommerce single template
			  case('woocommerce_single'):                   
			  //Look and see if the client has loaded their own custom template.
				echo woocommerce_get_template_part( 'content', 'single-product' );
			  break; 			  
			  case('formula04_quickwindow_template'):
			  default: 						
				  //Check for custom template in their theme folder?
				  $formula04_template_file =  get_stylesheet_directory() . '/formula04_quickwindow_template.php';
				  if(file_exists ( $formula04_template_file )):	
				  else:
					//Load My Own Template
					$formula04_template_file =  $this->myplugin_plugin_path().'/formula04_quickwindow_template.php';
				  endif;
				  
				  //Have we found our template file yet?
				  if(file_exists ( $formula04_template_file )):	
					load_template( $formula04_template_file, $require_once = true );						  
				  else:
				    //If my custom file is not there, and the client has not entered one either, then use woocommerces
					echo woocommerce_get_template_part( 'content', 'single-product' );
				  endif;					
	  		 endswitch;//switch($template):
			//Load Correct Template
			 ?></div><?php //<div class="woocommerce quick-window product"  
		  endwhile;
		  wp_reset_postdata();
		 else:
		 //No Product Found
		 endif;	//if ( $quick_window_query->have_posts() ) : while ( $quick_window_query->have_posts() ) : $quick_window_query->the_post();	  		
          $single_product_quick_window_output['html']  = ob_get_contents();
    	  ob_end_clean();

		  
		  $single_product_quick_window_output['product_type'] = $product_type;
		 //Output HTML
		  echo json_encode($single_product_quick_window_output);	
	break; //END  Case:get_quick_window

	



	case('update_variable_product'):

		global $woocommerce;
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
	
				//Find differences betweeen the two arrays
				$difference = array_diff_assoc($variation_array, $selected_attributes);
				//echo" \n <hr />DIFFERENCE ARRAY \n <pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($difference,true))."</pre>";
				
				//If there are no differences in the array then we have found our variation
				if(empty($difference)):
					$extra_class = '';
					$variation_found['use_ajax'] = 'yes';
					//echo 'Variation ID is '.$one_variation['variation_id'];
					$variation_id = $one_variation['variation_id'];
					$add_to_cart_button = '<a href="'.esc_url( $product->add_to_cart_url() ).'&variation_id='.$variation_id.'&attribute_stock-colors=1" class="'.$extra_class.'">Add to Cart</a>';
					$variation_found['variation_id'] = $variation_id;
					$variation_found['add_to_cart_button'] = $add_to_cart_button;
					echo json_encode($variation_found);

					die();
					break;				

				endif;
			endforeach;//END foreach($available_variations as $key => $one_variation):

		
			//WE found no matching varations out of the  available_variations
			if(!$variation_found):
				$variation_found['failure'] = 'No Matching Variations Found';
				echo json_encode($variation_found);
				//If we get here, no varuation was ever found.
				//echo" <br /><hr /><pre style='background-color:black; color:white;'>".htmlspecialchars(print_r($available_variations,true))."</pre>";
			endif;

			die();
		else:
			//There are no available variations.
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



		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation  ) ) :
			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				wc_add_to_cart_message( $product_id );
			}

			// Return fragments
			WC_AJAX::get_refreshed_fragments();
		else:
			$this->json_headers();
			// If there was an error adding to the cart, redirect to the product page to show any errors
			$data = array(
				'error' => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
				);
			echo json_encode( $data );
		endif;//if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation  ) ) :

		break;
		default:	
	endswitch; //switch($wwad):
	die(); // this is required to terminate immediately and return a proper response
	
	}//Public Functionform04quickwindow_callback


public function formula04_quick_window_button_css(){?>

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

	line-height: 1.4em;

}

.quick_window_window .quick-window-content .price .amount{ 

font-size: 2.3em;

font-family: 'Open Sans', sans-serif;

font-weight: 600;

margin-bottom: 0px;

line-height: 1em;

letter-spacing: -.06em;

line-height: 1.4em;s

}





.quick_window_window .quick_window_product_page_link{ line-height:1em;}

.quick_window_window .quick-window-image.images{ padding:10px; box-sizing:border-box;}

.quick_window_product_page_link{}

.quick-window-content{ float:left; width:49%;  box-sizing:border-box;} 

.quick_window_window .quick-window-image.images{ float:left;width:49%; margin:0px margin-left:1%; box-sizing:border-box;}

.quick-window-content:AFTER{content:""; clear:left;}

.quick_window_window .variations tbody tr select{

	padding:5px;

}

.quick_window_window .variations tbody tr{

	margin-bottom:10px;

}



.quick_window_window p{

	line-height:1.4em;

	margin-bottom:5px;

	

}

.quick-window .loading_graphic{
	max-width:80%;
	margin:auto;
	background-color:transparent;
	line-height:100%;
	vertical-align:middle;
	width:200px;
	height:200px;
	text-align:center;
	font-size:3em;
}

@media screen and (max-width:768px){

   .quick-window-content{ float:none; width:99%; margin:auto; padding-bottom:20px;} 

   .quick_window_window .quick-window-image.images{

	   max-width:100%;

	   float:none;

	   margin:auto;

	   

   }

   

   

}







</style>

<?php }//formula04_quick_window_button_css




//----------------------------
//BACKEND STUFF
//----------------------------
public function plugin_activation_string( $translated_text, $untranslated_text, $domain )
    {
        $old = array(
            "Plugin <strong>activated</strong>.",
            "Selected plugins <strong>activated</strong>." 
        );

        $new = "Formula04 Quick Window is installed.  Let's get it  <strong>PoPPing!</strong>";

        if ( in_array( $untranslated_text, $old, true ) )
            $translated_text = $new;

        return $translated_text;
     }

//Add Settings link on plugins.php page
public function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page='.$this->tag.'">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}


//add settings menu link in admin
public function formula_04_settings_menu() {
   // Add a new submenu under Settings:
   
   add_options_page(__('Formula 04 Quick Window','formula04'), __('F04 Quick Window','formula04'), 'manage_options', $this->tag,  array( &$this, 'formula_04_settings_page' ));
}

public function formula_04_settings_page() {
   echo "<h2>" . __( 'Formula 04 Quick Window Settings', 'formula04' ) . "</h2>";?>
	<form action='options.php' method='post'><?php
		settings_fields( 'F04QuickWindowSet' );
		do_settings_sections( 'F04QuickWindowSet' );
		submit_button();
?></form><?php 
}//formula_04_settings_page
				 




public function formula04_quickwindow_settings_init(  ) { 


	register_setting( 'F04QuickWindowSet', 'formula04_quickwindow_settings' );
	
	add_settings_section(
		'formula04_F04QuickWindowSet_section', 
		__( 'Use this section to customize Formula 04 Quick Window ', 'formula04' ), 
		 array( &$this, 'formula04_quickwindow_settings_section_callback'), 
		'F04QuickWindowSet'
	);


	
	add_settings_field( 
	'formula04_quickwindow_button_display', 
	__( 'Auto Display Quick View Button ', 'formula04' ), 
	 array( &$this, 'formula04_quickwindow_button_display_render'), 
	'F04QuickWindowSet', 
	'formula04_F04QuickWindowSet_section' 
	);
	
	


	add_settings_section(
		'formula04_F04QuickWindowSet_quickbutton_section', 
		__( 'Formula04 Quick Window Button Options', 'formula04' ), 
		 array( &$this, 'formula04_quickwindow_button_settings_callback'), 
		'F04QuickWindowSet'
	);		
	
	

		




	add_settings_field( 
		'formula04_quick_window_button_text', 
		__( 'Formula04 QuickWindow Button Text', 'formula04' ), 
		 array( &$this, 'formula04_quick_window_button_text_render'), 
		'F04QuickWindowSet', 
		'formula04_F04QuickWindowSet_quickbutton_section' 

	);		

	add_settings_field( 
		'formula04_quick_window_button_classes', 
		__( 'Formula04 QuickWindow Button Extra Classes', 'formula04' ), 
		 array( &$this, 'formula04_quick_window_button_classes_render'), 
		'F04QuickWindowSet', 
		'formula04_F04QuickWindowSet_quickbutton_section' 
	);	

	add_settings_field( 
		'formula04_quick_window_button_id', 
		__( 'Formula04 QuickWindow Button ID', 'formula04' ), 
		 array( &$this, 'formula04_quick_window_button_id_render'), 
		'F04QuickWindowSet', 
		'formula04_F04QuickWindowSet_quickbutton_section' 
	);	

	add_settings_section(
		'formula04_F04QuickWindowSet_checkout_section', 
		__( 'Formula04 Custom Template Options', 'formula04' ), 
		 array( &$this, 'formula04_quickwindow_settings_checkout_section_callback'), 
		'F04QuickWindowSet'
	);

		
	add_settings_field( 
		'formula04_quickwindow_template', 
		__( 'Pop Up Window Content Setting', 'formula04' ), 
		 array( &$this, 'formula04_quick_window_content_render'), 
		'F04QuickWindowSet', 
		'formula04_F04QuickWindowSet_checkout_section' 
	);	
		
		
		

	add_settings_field( 
	'formula04_use_f04_css', 
	__( 'Use Formula04 CSS?', 'formula04' ), 
	 array( &$this, 'formula04_use_f04_css_render'), 
	'F04QuickWindowSet', 
	'formula04_F04QuickWindowSet_checkout_section' 
	);
	
	add_settings_field( 
	'formula04_quickwindow_allow_add_to_cart', 
	__( 'Allow Add To Cart from Formula04 Quick Window', 'formula04' ), 
	 array( &$this, 'formula04_quickwindow_allow_add_to_cart_render'), 
	'F04QuickWindowSet', 
	'formula04_F04QuickWindowSet_checkout_section' 
	);
}//formula04_quickwindow_settings_init(  ) { 

//----------------------------
//Actual Output of Settings Fields.
//----------------------------

public function formula04_quickwindow_button_display_render(  ) { 
	$options = $this->options;?>
	<input type='checkbox' name='formula04_quickwindow_settings[formula04_quickwindow_button_display]' <?php echo $options  && isset($options['formula04_quickwindow_button_display']) && $options['formula04_quickwindow_button_display'] ? 'checked' : '';?> value='1'>
    <span class=""><?php
	 _e('If this is selected, the Formula04 Quick View Button will be automatically shown.  <br />Leave this unchecked if you plan on using the <strong>shortcode</strong> in your templates and content to determine where the buttons show up. ', 'formula04');
?></span>
<?php
}

public function formula04_quickwindow_allow_add_to_cart_render(  ) { 
	$options = $this->options;?>
	<input type='checkbox' name='formula04_quickwindow_settings[formula04_quickwindow_allow_add_to_cart]' <?php echo $options  && isset($options['formula04_quickwindow_allow_add_to_cart']) && $options['formula04_quickwindow_allow_add_to_cart'] ? 'checked' : '';?> value='1'>
<?php
}

public function formula04_use_f04_css_render(  ) { 

	$options = $this->options;?>

<input type='checkbox' name='formula04_quickwindow_settings[formula04_use_f04_css]' <?php echo $options  && isset($options['formula04_use_f04_css']) && $options['formula04_use_f04_css'] ? 'checked' : '';?> value='1'>

<span class="">

<?php _e('Load our built in css to style the product quick window', 'formula04'); ?>

</span>

<?php

}

public function formula04_quick_window_content_render(  ) { 
	$options = $this->options;
	$f04_quick_window_template = isset(  $options['f04_quick_window_template']  );?>
    <select name='formula04_quickwindow_settings[formula04_quickwindow_template]'>
        <option value="formula04_quickwindow_template"  <?php echo isset($options['formula04_quickwindow_template']) && $options['formula04_quickwindow_template'] ==  'formula04_quickwindow_template'? 'selected' : '';?>  >FORMULA04 Default/Custom Template</option>
        <option value="woocommerce_single"  <?php echo isset($options['formula04_quickwindow_template']) && $options['formula04_quickwindow_template'] ==  'woocommerce_single'? 'selected' : '';?>>Product Page Template from WooCommerce</option>
    </select>

    <span class="">
	  <?php _e('Select the quick window content template', 'formula04'); ?>
    </span>

<?php }

public function formula04_quick_window_button_text_render(  ) { 
	$options = $this->options;
	$f04_quick_window_button_text = isset(  $options['formula04_quick_window_button_text']  )  ? $options['formula04_quick_window_button_text'] :  '';?>
    <input size="50" placeholder="Text that appears on Quick View Button" type="text" value="<?php echo $f04_quick_window_button_text; ?>" name="formula04_quickwindow_settings[formula04_quick_window_button_text]" />
<?php }

public function formula04_quick_window_button_classes_render(  ) { 
	$options = $this->options;
	$f04_quick_window_button_classes = isset(  $options['formula04_quick_window_button_classes']  )  ? $options['formula04_quick_window_button_classes'] :  '';?>
    <input  size="50" placeholder="Add extra classes to the button" type="text" value="<?php echo $f04_quick_window_button_classes; ?>" name="formula04_quickwindow_settings[formula04_quick_window_button_classes]" />
<?php }

public function formula04_quick_window_button_id_render(  ) { 
	$options = $this->options;
	$formula04_quick_window_button_id = isset(  $options['formula04_quick_window_button_id']  )  ? $options['formula04_quick_window_button_id'] :  '';?>
	<input size="50" placeholder="Add an ID to the button" type="text" value="<?php echo $formula04_quick_window_button_id; ?>" name="formula04_quickwindow_settings[formula04_quick_window_button_id]" />
<?php }

public function formula04_quickwindow_settings_checkout_section_callback( ){?>
<hr /><?php }
public function formula04_quickwindow_button_settings_callback(  ) { ?><hr /><?php 
	//echo __( 'This section description', 'formula04' );
}
public function formula04_quickwindow_settings_section_callback(  ) { ?><hr /><?php 
	//echo __( 'This section description', 'formula04' );
}

// Display the admin notification








//Install AND Uninstall Stuff.
 static function activate() {
	 //Set to expire in 15 seconds
	   set_transient( 'formula04_quick_window_activated', true, 15 );
 }

static function deactivate() {
            // do not generate any output here
 }

static function uninstall() {
            // do not generate any output here
			delete_option( 'formula04_quickwindow_settings' );
 }



		
	}//class Form04wooquickwindow
	new Form04wooquickwindow;
}//if ( ! class_exists( 'Form04wooquickwindow' ) ) {	


 
 
	
	




register_activation_hook( __FILE__, array( 'Form04wooquickwindow', 'activate' ) );	
register_deactivation_hook( __FILE__, array( 'Form04wooquickwindow', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'Form04wooquickwindow', 'uninstall' ) );	