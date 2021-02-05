<?php

/**
 * Trigger this file on Plugin uninstall
 *
 * @package  MancStudentsPlugin
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;
$wpdb->query( "DROP TABLE {$wpdb->prefix}students" );