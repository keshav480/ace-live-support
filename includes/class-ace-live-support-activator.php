<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * Fired during plugin activation
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Live_Support
 * @subpackage Ace_Live_Support/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ace_Live_Support
 * @subpackage Ace_Live_Support/includes
 * @author     AceWebx Team <developer@acewebx.com>
 */
class Ace_Live_Support_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
	
		/* -------------------------------------
		 * USERS TABLE
		 * ------------------------------------- */
		$table_name = $wpdb->prefix . 'ace_live_chat';
	
		$sql = "CREATE TABLE $table_name (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id VARCHAR(100) NOT NULL,
			name VARCHAR(200) NULL,
			email VARCHAR(200) NULL,
			type VARCHAR(50) NOT NULL DEFAULT 'guest',
			status VARCHAR(50) NOT NULL DEFAULT 'active',
			email_verified TINYINT(1) NOT NULL DEFAULT 0,
			verification_token VARCHAR(100) NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY user_id (user_id)
		) $charset_collate;";
		
	
		/* -------------------------------------
		 * MESSAGES TABLE
		 * ------------------------------------- */
		$messages_table = $wpdb->prefix . 'ace_live_chat_messages';
		$sql_messages = "CREATE TABLE $messages_table (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL, 
			messages LONGTEXT NOT NULL,
			unread_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id)
		) $charset_collate;";
	
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		dbDelta($sql_messages);
		
	}
	
	
}
