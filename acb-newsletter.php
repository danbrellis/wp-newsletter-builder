<?php
/*
Plugin Name: WP Newsletter HTML Builder
Description: A web-based solution for generating the HTML for an e-newsletter. Use the provided template or add your own. Create a new newsletter and then add your Newsletter Articles to it using custom post types.
Author: Dan Brellis
Author URL: https://github.com/danbrellis/

Note: If theme styles conflict with newsletter layout, add the follow code into your theme's function.php
function acb_news_wp_enqueue_scripts() {
	if ( 'e_newsletter' == get_post_type() && is_single() && !is_admin() ){
		wp_dequeue_style([replace-with-theme-style-handle]);
	}
}
add_action('wp_enqueue_scripts', 'acb_news_wp_enqueue_scripts');

*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * ACBNEWS class.
 *
 * Main Class which inits the CPT and plugin
 */
class ACBNEWS {

	private $plugin_url;
	private $plugin_path;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		global $wpdb;

		// Include required files
		if ( is_admin() ) {
			include_once( 'includes/class-admin.php' );
		}

		include_once( 'includes/class-acb-newsletter.php' );

		// Activation
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'init_taxonomy' ), 10 );

		// Actions
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'register_globals' ) );
		add_action( 'init', array( $this, 'init_taxonomy' ) );
		add_action( 'after_setup_theme', array( $this, 'compatibility' ) );
		add_action( 'the_post', array( $this, 'setup_download_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		
		//Templates and output
		add_filter( 'single_template', array($this, 'single_template') );
		
		
		//Imagery
		if(function_exists( 'add_image_size' )){ 
			add_image_size( 'newsletter-hero', apply_filters('acb_newsletter_hero_size', 548) );
		}
	}

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
		if ( 'e_newsletter' !== get_post_type() || !is_single() ) return; //bail if we're not on a newsletter post type
		wp_enqueue_style( 'acb_newsletter_email', $this->plugin_url() . '/assets/css/acb-newsletter-email.css' );
		wp_enqueue_style( 'acb_newsletter_display', $this->plugin_url() . '/assets/css/acb-newsletter-display.css' );
		
		wp_enqueue_script( 'acb_newsletter_js', $this->plugin_url() . '/assets/js/acb-newsletter.js', 'jquery', false, true );
	}

	/**
	 * Localisation
	 *
	 * @access private
	 * @return void
	 */
	public function load_plugin_textdomain() {
		//load_textdomain( 'acb_nwsltr', WP_LANG_DIR . '/download-monitor/download_monitor-' . get_locale() . '.mo' );
		//load_plugin_textdomain( 'download-monitor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Register environment globals
	 *
	 * @access private
	 * @return void
	 */
	public function register_globals() {
		$GLOBALS['acb_newsletter'] = null;
	}

	/**
	 * When the_post is called, get product data too
	 *
	 * @access public
	 * @param mixed $post
	 * @return void
	 */
	public function setup_download_data( $post ) {
		if ( is_int( $post ) )
			$post = get_post( $post );

		if ( $post->post_type !== 'e_newsletter' )
			return;

		$GLOBALS['acb_newsletter'] = new ACB_Newsletter( $post->ID );
	}

	/**
	 * Add Theme Compatibility
	 *
	 * @access public
	 * @return void
	 */
	public function compatibility() {
		// Post thumbnail support
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
			remove_post_type_support( 'post', 'thumbnail' );
			remove_post_type_support( 'page', 'thumbnail' );
		} else {
			add_post_type_support( 'e_newsletter', 'thumbnail' );
		}
	}

	/**
	 * Init taxonomies
	 *
	 * @access public
	 * @return void
	 */
	public function init_taxonomy() {

		if ( post_type_exists( "e_newsletter" ) )
			return;
			
	    /**
		 * Post Types
		 */
		register_post_type(__('e_newsletter'),
			array(
				'labels' => array(
					'name' => __( 'e-Newsletters' ),
					'singular_name' => __( 'e-Newsletter' )
				),
				'public' => true,
				'show_in_nav_menus' => false,
				'has_archive' => true,
				'rewrite' => array('slug' => 'newsletters'),
				'capability_type' => 'post',
				'supports' => array('title','thumbnail','excerpt'),
				'menu_icon' => 'dashicons-welcome-write-blog'
			)
		);
		remove_post_type_support( 'e_newsletter', 'editor');
		
		register_post_type(__('acb_newsletter_item'),
			array(
				'labels' => array(
					'name'               => _x( 'Newsletter Items', 'post type general name', 'acb_nwsltr' ),
					'singular_name'      => _x( 'Newsletter Item', 'post type singular name', 'acb_nwsltr' ),
					'menu_name'          => _x( 'Newsletter Items', 'admin menu', 'acb_nwsltr' ),
					'name_admin_bar'     => _x( 'Newsletter Item', 'add new on admin bar', 'acb_nwsltr' ),
					'add_new'            => _x( 'Add Newsletter Item', 'book', 'acb_nwsltr' ),
					'add_new_item'       => __( 'Add New Newsletter Item', 'acb_nwsltr' ),
					'new_item'           => __( 'New Newsletter Item', 'acb_nwsltr' ),
					'edit_item'          => __( 'Edit Newsletter Item', 'acb_nwsltr' ),
					'view_item'          => __( 'View Newsletter Item', 'acb_nwsltr' ),
					'all_items'          => __( 'All Newsletter Items', 'acb_nwsltr' ),
					'search_items'       => __( 'Search Newsletter Items', 'acb_nwsltr' ),
					'not_found'          => __( 'No newsletter item found.', 'acb_nwsltr' ),
					'not_found_in_trash' => __( 'No newsletter items found in Trash.', 'acb_nwsltr' )
				),
				'public'             => false,
				'show_in_nav_menus' => false,
				'show_ui'            => true,
				'show_in_menu'       => 'edit.php?post_type=e_newsletter',
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'supports'           => array( 'title', 'editor', 'author', 'revisions', 'thumbnail' )
			)
		);
		
		
	}
	
	/** Display Functions ****************************************************/
	
	/**
	 * Filters the single template for the newsletter post type
	 */
	function single_template($single_template) {
		 global $post;
	
		 if ($post->post_type == 'e_newsletter') {
			  $single_template = $this->get_template_part('single', 'e_newsletter', $this->plugin_path().'/includes/', true); //dirname( __FILE__ ) . '/includes/single-e_newsletter.php';
		 }
		//var_dump($this->get_template_part('single', 'e_newsletter', $this->plugin_url().'/includes/', true));
		 return $single_template;
	}

	/** Helper functions *****************************************************/
	
	/**
	 * get_template_part function.
	 *
	 * @access public
	 * @param mixed $slug
	 * @param string $name (default: '')
	 * @return void
	 */
	public function get_template_part( $slug, $name = '', $custom_dir = '', $return = false ) {
		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/wp-newsletter-builder/slug-name.php
		if ( $name )
			$template = locate_template( array ( "{$slug}-{$name}.php", "plugins/wp-newsletter-builder/templates/{$slug}-{$name}.php" ) );

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( $this->plugin_path() . "/templates/{$slug}-{$name}.php" ) )
			$template = $this->plugin_path() . "/templates/{$slug}-{$name}.php";

		// If a custom path was defined, check that next
		if ( ! $template && $custom_dir && file_exists( trailingslashit( $custom_dir ) . "{$slug}-{$name}.php" ) )
			$template = trailingslashit( $custom_dir ) . "{$slug}-{$name}.php";

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/plugins/wp-newsletter-builder/templates/slug.php
		if ( ! $template )
			$template = locate_template( array( "{$slug}.php", "plugins/wp-newsletter-builder/templates/{$slug}.php" ) );

		// If a custom path was defined, check that next
		if ( ! $template && $custom_dir && file_exists( trailingslashit( $custom_dir ) . "{$slug}-{$name}.php" ) )
			$template = trailingslashit( $custom_dir ) . "{$slug}-{$name}.php";

		// Get default slug-name.php
		if ( ! $template && file_exists( $this->plugin_path() . "/templates/{$slug}.php" ) )
			$template = $this->plugin_path() . "/templates/{$slug}.php";

		if ( $template ){
			if($return) return $template;
			else load_template( $template, false );
		}
	}
	
	/**
	 * Get the plugin url
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_url() {
		if ( $this->plugin_url )
			return $this->plugin_url;

		return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
	}

	/**
	 * Get the plugin path
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_path() {
		if ( $this->plugin_path )
			return $this->plugin_path;

		return $this->plugin_path = plugin_dir_path( __FILE__ );
	}
	
}

/**
 * Init download_monitor class
 */
$GLOBALS['ACBNEWS'] = new ACBNEWS();

?>
