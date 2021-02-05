<?php
/**
 * @package  MancStudentsPlugin
 */

class MancStudentsPluginDeactivate
{
	public static function deactivate() {
		global $wpdb;
		$wpdb->query( "DROP TABLE ".$wpdb->prefix."students" );

		flush_rewrite_rules();
	}
}