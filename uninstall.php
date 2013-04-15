<?php
//
//  file created in 2013/03/20 16:30:18.
//  LastUpdated :2013/03/28 09:53:56.
//  author iNo
//

if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

require_once( dirname( __FILE__ ) . '/ws-serendip-event-manager.class.php' );

$sem = new Serendip_Event_Manager();

// Delete table place
$table_name = $sem->place_table;
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

delete_option( 'ws_serendip_event_manager_place_db_version' );
delete_option( 'ws_serendip_event_manager_count_days_from_posts' );
delete_option( 'ws_serendip_event_manager_count_posts_per_page' );

// vim:set noet:fenc=utf8:fdl=0 fdm=marker:ts=4 sw=4 sts=0:
?>
