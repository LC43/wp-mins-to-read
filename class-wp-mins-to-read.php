<?php
/**
 * WP Mins To Read
 *
 * @package   WP_MinsToRead
 * @author    Edward McIntyre <edward@edwardmcintyre.com>
 * @license   GPL-2.0+
 * @link      https://github.com/twittem/
 * @copyright 2013 Edward McIntyre
 */

/**
 * WP_MinsToRead class
 *
 * @package WP_MinsToRead
 * @author  Edward McIntyre <edward@edwardmcintyre.com>
 */

class WP_MinsToRead {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @const   string
	 */
	const VERSION = '1.0.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 * @deprecated
	 */
	protected $plugin_slug = 'wp-mins-to-read';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'manage_posts_custom_column' , array( $this, 'display_mtr_column' ), 10, 2 );
		add_filter( 'manage_posts_columns' , array( $this, 'add_mtr_column' ) );
		add_filter( 'save_post' , array( $this, 'delete_mtr_transient' ) );
	}

	public function delete_mtr_transient( $post_id ) {
		delete_transient( $post_id . '-minread' );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	public static function activate( $network_wide ) {
		// TODO: Define activation functionality
	}

	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality
	}


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wp-mins-to-read', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	}

	/**
	 * Calculate the mins to read for a givin post
	 *
	 * @since     1.0.0
	 *
	 * @param     mixed     Post ID
	 *
	 * @return    string    Returns 'min read' string
	 */
	static function calc_mtr( $post_id ) {

		//Get post content
		$content = get_post_field( 'post_content', $post_id, 'display' );

		//Calculate post wordcount
		$word_count = str_word_count( strip_tags( $content ) );

		//Calculate minutes to read
		$mtr_raw = ( $word_count / 200 );

		//round minutes to read
		$mtr_round = round( $mtr_raw );

		//if less them 1 min, make 1 min
		$mtr = 0 === $mtr_round ? __( '1 min read', 'wp-mins-to-read' ) : $mtr_round . __( ' min read', 'wp-mins-to-read' );

		//Set transient with out values
		set_transient( $post_id . '-minread',
			array(
				'value' => $mtr,
    			'time' => time(),
			), 0 );

		return $mtr;
	}

	/**
	 * Fetches the mins to read transient from the database or generates a new transient
	 *
	 * @since     1.0.0
	 *
	 * @param     mixed     Post ID
	 *
	 * @return    string    Returns 'min read' string
	 */
	static function get_mtr( $post_id ) {

		$transient = get_transient( $post_id . '-minread' );
		// If does not exists, calculate it
		if ( false === $transient ) {
			$mtr = WP_MinsToRead::calc_mtr( $post_id );
		} else {
			$mtr = $transient;
		}
		return $mtr;
	}

	/**
	 * Adds Min Read column to admin
	 *
	 * @since     1.0.0
	 */
	function add_mtr_column( $columns ) {
		return array_merge(
			$columns,
			array( 'mtr' => __( 'Min Read', 'wp-mins-to-read' ) )
		);
	}

	/**
	 * Adds Min Read vairables to admin column
	 *
	 * @since     1.0.0
	 */
	function display_mtr_column( $column, $post_id ) {
		if ( 'mtr' === $column ) {
			echo esc_html( WP_MinsToRead::get_mtr( $post_id ) );
		}
	}
}
