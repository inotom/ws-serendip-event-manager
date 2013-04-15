<?php
/*
 * Plugin Name: Serendip Event Manager
 * Plugin URI: http://www.serendip.ws/
 * Description: This is a plug-in that view, manage and post event information.
 * Version: 1.0
 * Author: iNo
 * Author URI: http://www.serendip.ws/
 * License: GPL2
 *
 * file created in 2013/03/18 10:01:23.
 * LastUpdated :2013/03/28 10:36:59.
 * */

// Load required files
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "ws-serendip-event-manager.class.php";
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . "functions.php";

// Make Instance
global $ws_serendip_event_manager;
$ws_serendip_event_manager = new Serendip_Event_Manager();

// Register Activation Hook
register_activation_hook( __FILE__, array( $ws_serendip_event_manager, 'activate' ) );

// Check upgrade database
add_action( 'plugins_loaded', array( $ws_serendip_event_manager, 'ws_serendip_event_manager_update_place_db_check' ) );

// vim:set noet:fenc=utf8:fdl=0 fdm=marker:ts=4 sw=4 sts=0:
?>
