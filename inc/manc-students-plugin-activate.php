<?php
/**
 * @package  MancStudentsPlugin
 */

class MancStudentsPluginActivate
{
	public static function activate() {
		flush_rewrite_rules();
	}
}