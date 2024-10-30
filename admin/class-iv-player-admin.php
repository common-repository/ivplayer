<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    IV_Player
 * @subpackage IV_Player/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    IV_Player
 * @subpackage IV_Player/admin
 * @author     Your Name <email@example.com>
 */
class IV_Player_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $iv_player    The ID of this plugin.
	 */
	private $iv_player;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $iv_player       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($iv_player, $version)
	{

		$this->iv_player = $iv_player;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in IV_Player_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The IV_Player_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->iv_player, plugin_dir_url(__FILE__) . 'css/iv-player-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in IV_Player_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The IV_Player_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->iv_player, plugin_dir_url(__FILE__) . 'js/iv-player-admin.js', array('jquery'), $this->version, false);
	}


	public function load_rn_scripts()
	{
		$asset_manifest = json_decode(file_get_contents(IV_PLAYER_ASSET_MANIFEST), true)['files'];


		if (isset($asset_manifest['main.css'])) {
			wp_enqueue_style($this->iv_player, get_site_url() . $asset_manifest['main.css']);
		}

		wp_enqueue_script($this->iv_player . '-runtime', get_site_url() . $asset_manifest['runtime-main.js'], array(), null, true);

		wp_enqueue_script($this->iv_player . '-main', get_site_url() . $asset_manifest['main.js'], array($this->iv_player . '-runtime'), null, true);
		wp_localize_script(
			$this->iv_player . '-main',
			'ajax_object',
			array('nonce' => wp_create_nonce('wp_rest'), 'public' => false, 'root' => esc_url_raw(rest_url()))
		);
		foreach ($asset_manifest as $key => $value) {
			if (preg_match('@static/js/(.*)\.chunk\.js@', $key, $matches)) {
				if ($matches && is_array($matches) && count($matches) === 2) {
					$name = $this->iv_player . "-" . preg_replace('/[^A-Za-z0-9_]/', '-', $matches[1]);
					wp_enqueue_script($name, get_site_url() . $value, array($this->iv_player . '-main'), null, true);
				}
			}

			if (preg_match('@static/css/(.*)\.chunk\.css@', $key, $matches)) {
				if ($matches && is_array($matches) && count($matches) == 2) {
					$name = $this->iv_player . "-" . preg_replace('/[^A-Za-z0-9_]/', '-', $matches[1]);
					wp_enqueue_style($name, get_site_url() . $value, array($this->iv_player), null);
				}
			}
		}
	}

	public function custom_post_type()
	{
		register_post_type(
			'iv-players',
			array(
				'labels'      => array(
					'name'          => __('Players', 'textdomain'),
					'singular_name' => __('Player', 'textdomain'),
				),
				'public'      => false,
				'show_in_rest' => true,
				'has_archive' => true,
				'rewrite'     => array('slug' => 'iv-players'),
				'supports' => array('title', 'custom-fields')
			)
		);
	}
	public function add_custom_fields()
	{
		register_rest_field('iv-players', 'video-fields', array(
			'get_callback'    =>  function ($params) {
				return get_post_meta($params['id']);
			},
			'update_callback' => function ($value, $object, $fieldName) {
				return update_post_meta($object->ID, $fieldName, $value);
			},
			'schema'          => null,
		));
	}
	public function add_top_level_menu()
	{
		add_menu_page(
			'IVPlayer',
			'IV Player',
			'manage_options',
			plugin_dir_path(__FILE__) . 'partials/iv-player-admin-display.php',
			null,
			'dashicons-playlist-video',
			20
		);
	}

	public function add_get_val()
	{
		global $wp;
		$wp->add_query_var('ppc');
	}
}
