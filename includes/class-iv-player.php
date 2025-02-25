<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    IV_Player
 * @subpackage IV_Player/includes
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
 * @package    IV_Player
 * @subpackage IV_Player/includes
 * @author     Your Name <email@example.com>
 */
class IV_Player
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      IV_Player_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $iv_player    The string used to uniquely identify this plugin.
	 */
	protected $iv_player;

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
		if (defined('IV_PLAYER_VERSION')) {
			$this->version = IV_PLAYER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->iv_player = 'ivplayer';

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
	 * - IV_Player_Loader. Orchestrates the hooks of the plugin.
	 * - IV_Player_i18n. Defines internationalization functionality.
	 * - IV_Player_Admin. Defines all hooks for the admin area.
	 * - IV_Player_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-iv-player-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-iv-player-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-iv-player-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'open/class-iv-player-open.php';

		$this->loader = new IV_Player_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the IV_Player_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new IV_Player_i18n();

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

		$plugin_admin = new IV_Player_Admin($this->get_iv_player(), $this->get_version());
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_top_level_menu');
		$this->loader->add_action('init', $plugin_admin, 'custom_post_type');
		$this->loader->add_action('rest_api_init', $plugin_admin, 'add_custom_fields');
		$this->loader->add_action('init', $plugin_admin, 'add_get_val');

		if (isset($_GET['page'])) {
			if (substr($_GET['page'], 0, strlen($this->iv_player)) === $this->iv_player) {
				$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
				$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'load_rn_scripts');
				$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
			}
		}
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

		$plugin_public = new IV_Player_Public($this->get_iv_player(), $this->get_version());


		$this->loader->add_code('iv-player', $plugin_public, 'player_code');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'load_rn_scripts');



		// $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		// $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

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
	public function get_iv_player()
	{
		return $this->iv_player;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    IV_Player_Loader    Orchestrates the hooks of the plugin.
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
