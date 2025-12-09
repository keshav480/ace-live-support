<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://acewebx.com
 * @since      1.0.0
 *
 * @package    Ace_Live_Support
 * @subpackage Ace_Live_Support/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ace_Live_Support
 * @subpackage Ace_Live_Support/includes
 * @author     AceWebx Team <developer@acewebx.com>
 */
class Ace_Live_Support
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ace_Live_Support_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('ACE_LIVE_SUPPORT_VERSION')) {
			$this->version = ACE_LIVE_SUPPORT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ace-live-support';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ace_Live_Support_Loader. Orchestrates the hooks of the plugin.
	 * - Ace_Live_Support_i18n. Defines internationalization functionality.
	 * - Ace_Live_Support_Admin. Defines all hooks for the admin area.
	 * - Ace_Live_Support_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ace-live-support-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ace-live-support-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ace-live-support-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ace-live-support-public.php';

		$this->loader = new Ace_Live_Support_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ace_Live_Support_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Ace_Live_Support_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Ace_Live_Support_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'admin_menu');
		$this->loader->add_action('wp_ajax_ace_chat_get_user', $plugin_admin, 'wp_ajax_ace_chat_get_user');
		$this->loader->add_action('wp_ajax_ace_chat_send_admin_user', $plugin_admin, 'wp_ajax_ace_chat_send_admin_user');
		$this->loader->add_action('admin_init',  $plugin_admin, 'ace_register_chat_settings');
		$this->loader->add_action('wp_ajax_ace_clear_chat', $plugin_admin,  'ace_clear_chat');
		$this->loader->add_action('wp_ajax_ace_delete_user',$plugin_admin, 'ace_delete_user');
		$this->loader->add_action('wp_ajax_ace_test_smtp', $plugin_admin,'ace_test_smtp');
		$this->loader->add_action('admin_init', $plugin_admin,'ace_sanitize_support_icon');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Ace_Live_Support_Public($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('wp_ajax_ace_chat_get', $plugin_public, 'ace_chat_get');
		$this->loader->add_action('wp_ajax_nopriv_ace_chat_get',$plugin_public,'ace_chat_get');
		$this->loader->add_action('wp_footer', $plugin_public, 'ace_live_chat_support');
		$this->loader->add_action('wp_ajax_ace_chat_send', $plugin_public, 'wp_ajax_ace_chat_send_data');
		$this->loader->add_action('wp_ajax_nopriv_ace_chat_send', $plugin_public, 'wp_ajax_ace_chat_send_data');
		$this->loader->add_action('wp_ajax_ace_save_guest_email',$plugin_public, 'ace_save_guest_email');
		$this->loader->add_action('wp_ajax_nopriv_ace_save_guest_email',$plugin_public, 'ace_save_guest_email');
		$this->loader->add_action('init',$plugin_public, 'ace_start_session', 1);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ace_Live_Support_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
