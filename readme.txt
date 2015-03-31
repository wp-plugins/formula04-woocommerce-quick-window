=== Formula04 WooCommerce Quick Window ===
Contributors: Verb_Form04 
Tags: Woocommerce, Popup, Pop UP 
Requires at least: 3.5.0
Tested up to: 4.1.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=verbiphone%40gmail%2ecom

Creates a button/popup that allows your customers to view more details about a product without having to go to the individual page; add to cart too!

== Description ==

Create's a pop up that contains the Product information for one product. A You can choose to automatically add a "popup" button to each product on the shop page; or using a shortcode you can control where the button appears and what product will be shown in the popup after said button is clicked. A You also can enable the adding of the product directly to the cart from the pop up. A This prevents your users having to go to the individual product page before adding the product to their cart.  
  
Just a very basic plug not meant to be anything too overcomplicated. IfA you have an ideas or feature requests let me know!

== Installation ==
After installing you will need to go to the Formula04 Quick Window Settings page and tweak a few things.

**Auto Display Quick View Button** - Select this if you want the pop-up button to automatically appear under each product listing.  This will apply to most widgets as well.

**Formula04 QuickWindow Button Text** - What text do you want to be on the pop up button.  This will be the universal throughout every Formula04 QuickWindow button on your site.

**Formula04 QuickWindow Button Extra Classes AND Formula04 QuickWindow Button ID** - Add a CSS Class or ID to a button.  This will be the universal throughout every Formula04 QuickWindow button on your site.

**Pop Up Window Content Setting** - What do you want to appear in the pop-up window?  You can choose from either whatever your current single product page template is, or you can use a custom layout. 

**Use Formula04 CSS?** - enables a very tiny bit of css that will be applied to the pop window assuming you are using the formula04default custom template.

**Allow Add To Cart from Formula04 Quick Window** - do you want to allow your visitors to add a product to their cart directly from the Formula04 QuickWindow?


== Frequently Asked Questions ==

= A Where can I see DEMO with one click? = 

**<a href="http://www.formula04.com/quickwindow/" target="_blank" title="Formula04 Woocommerce Quick Window demo">Right HERE</a>**

= A How do I use my own template for the pop up window? =

There is a file located in the Formula04 Woocommerce Quick Window Plugin Folder named "**formula04\_quickwindow\_template.php"**

Typically this will be located in "**wp-content/plugins/formula04-woocommerce-quick-window/**"

Copy this file to your Theme/Child Theme's directory.

Typically it would looke like this

**wp-content/themes/YOURTHEMENAME/formula04\_quickwindow\_template.php**

= How can I add a single quickwindow button exactly where I want it =

Use the Shortcode it is **[form04wooquickwindow]**

Attributes are
product_id - if none is specified then the script will look for a product using the current global ID
id - CSS ID
class CSS Classes
button_text - Text that appears on the actual button.

Example:

[form04wooquickwindow button_text="Quick View" product_id="Product ID HERE" id="Some_CSS_ID_HERE" class="SOME CSS CLASS HERE" ]