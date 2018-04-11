<?php
/**
 * Functions.php
 *
 * @package  Theme_Customization
 * @author   WooThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * functions.php
 * Add PHP snippets here
 */

 /********************************************************/
 // Install Google Analytics in WordPress
 /********************************************************/
 add_action('wp_footer', 'add_googleanalytics');
 function add_googleanalytics() { ?>
 	<script>
       (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
       (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
       m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
       })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

       ga('create', 'UA-xxxxxxx-x', 'auto');
       ga('send', 'pageview');

     </script>
 <?php
 }

 /********************************************************/
 //Allow SVG through WordPress Media Uploader
 /********************************************************/
 function cc_mime_types($mimes) {
   $mimes['svg'] = 'image/svg+xml';
   return $mimes;
 }
 add_filter('upload_mimes', 'cc_mime_types');

 /********************************************************/
 // TEST PHP injection
 /********************************************************/
echo "GIORGIO TEST PHP injection";
