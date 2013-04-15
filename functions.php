<?php
/**
 * Template tags for users
 *
 * @package Serendip Event Manager
 * @since 0.1
 *
 * file created in 2013/03/28 10:58:45.
 * LastUpdated :2013/03/28 11:18:59.
 * author iNo
 * */

function ws_serendip_event_manager_get_new_event_query() {
	global $ws_serendip_event_manager;
	return $ws_serendip_event_manager->get_query( false );
}

function ws_serendip_event_manager_get_old_event_query() {
	global $ws_serendip_event_manager;
	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	return $ws_serendip_event_manager->get_query( true, $paged );
}

// vim:set noet:fenc=utf8:fdl=0 fdm=marker:ts=4 sw=4 sts=0:
?>
