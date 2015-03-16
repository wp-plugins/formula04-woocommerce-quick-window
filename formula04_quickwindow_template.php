<?php
/*Formula04 Quick Window Window Content Template.
You can overide this template with your own by creating formula04_quickwindow_template.php in your themes root directory.
*/
$quick_window_options = get_option('formula04_quickwindow_settings');
$allow_add_to_cart    = isset($quick_window_options['formula04_quickwindow_allow_add_to_cart']) && $quick_window_options['formula04_quickwindow_allow_add_to_cart'] === '1' ? true : false;
$product              = get_product(get_the_ID());
$product_type         = $product->product_type; //Does this product have variations
$available_variations = $product_type === 'variable' ? $product->get_available_variations() : false;
?>

<div class="quick-window-image images">
  <?php
if (has_post_thumbnail()):
?>
 <?php
    echo get_the_post_thumbnail(get_the_ID(), apply_filters('single_product_large_thumbnail_size', 'shop_single'));
?>
 <?php
else:
?>
 <img src="<?php
    echo woocommerce_placeholder_img_src();
?>" alt="Placeholder" />
  <?php
endif;
?>
</div>
<?php //.quick-window-image.images 
?>
<div class="quick-window-content">
  <?php
/**
 * woocommerce_before_shop_loop_item_title hook
 *
 * @hooked woocommerce_show_product_loop_sale_flash - 10
 * @hooked woocommerce_template_loop_product_thumbnail - 10
 */
//Remove Price AND Thumnail image From Loop Since we show it down below.
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price');
do_action('woocommerce_before_shop_loop_item_title');
?>
 <?php
woocommerce_template_single_title();
/**
 * woocommerce_after_shop_loop_item_title hook
 *
 * @hooked woocommerce_template_loop_rating - 5
 * @hooked woocommerce_template_loop_price - 10
 */
do_action('woocommerce_after_shop_loop_item_title');
?>
 <a class="quick_window_product_page_link" href="<?php
echo get_permalink(get_the_ID());
?>">
  <?php
_e('View Product Page', 'formula04');
?>
 </a>
  <?php
woocommerce_template_single_price();
?>
 <?php
woocommerce_template_single_excerpt();
?>
 <?php
if ($allow_add_to_cart):
//What Cart Button To Show Based On Proudct Type 
    switch ($product_type):
        case ('simple'):
            woocommerce_template_single_add_to_cart();
            break;
        case ('variable'):
            woocommerce_variable_add_to_cart();
            break;
        case ('grouped'):
            woocommerce_grouped_add_to_cart();
            break;
        default:
            woocommerce_template_single_add_to_cart();
    endswitch;
endif; //if($allow_add_to_cart):
/* woocommerce_after_shop_loop_item hook
 *
 * @hooked woocommerce_template_loop_add_to_cart - 10
 
 */
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
do_action('woocommerce_after_shop_loop_item');
?>
</div>
<?php //#quick-window-content 