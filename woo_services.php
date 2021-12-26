<?php
/**
* Plugin Name: Woo Services
* Plugin URI: https://www.google.com/
* Description: Provide WooCommerce Services for mobile apps.
* Version: 1.0
* Author: Salman Rais
* Author URI: http://rais.com/
**/

if (!defined('ABSPATH')) {
	die('You can not access this file!');
}
require __DIR__ . '/vendor/autoload.php';
require('load.php');

/**
 * Woo Service
 */

class WooServices
{
	
	function __construct()
	{
		add_action( 'admin_menu', array( new Adminsetting, 'register_page' ) );
		add_action( 'admin_init', array( new Adminsetting, 'page_init' ) );
		add_action('rest_api_init', array(new Route,'route_init'));
	}
	
}

new WooServices();
?>