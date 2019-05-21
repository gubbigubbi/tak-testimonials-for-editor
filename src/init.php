<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Contants
define( 'TAK_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * `wp-blocks`: includes block type registration and related functions.
 *
 * @since 1.0.0
 */
function tak_cgb_block_assets() {
	// Styles.
	wp_enqueue_style(
		'sk-cgb-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		array( 'wp-blocks' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: filemtime — Gets file modification time.
	);
} // End function tak_cgb_block_assets().

// Hook: Frontend assets.
add_action( 'enqueue_block_assets', 'tak_cgb_block_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0.0
 */
function tak_cgb_editor_assets() {
	// Scripts.
	wp_enqueue_script(
		'sk-cgb-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element' ), // Dependencies, defined above.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Styles.
	wp_enqueue_style(
		'sk-cgb-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: filemtime — Gets file modification time.
	);
} // End function tak_cgb_editor_assets().

// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'tak_cgb_editor_assets' );

/**
 * Server Side Rendering
 */
require_once( TAK_PLUGIN_PATH . './server.php' );

class TakWPApiFeaturedImage {

	/**
	 * The endpoints we want to target
	 */
	public $target_endpoints = '';

	/**
	 * Constructor
	 * @uses rest_api_init
	 */
	function __construct() {
		$this->target_endpoints = array('post');
		add_action( 'rest_api_init', array( $this, 'add_image' ));
	}


	/**
	 * Add Images to json api
	 */
	function add_image() {

		/**
		 * Add 'featured_image'
		 */
		register_rest_field( $this->target_endpoints, 'featured_image',
			 array(
				 'get_callback'    => array( $this, 'get_image_url_full'),
				 'update_callback' => null,
				 'schema'          => null,
			 )
		 );

		 /**
			* Add 'featured_image_thumbnail'
			*/
		 register_rest_field( $this->target_endpoints, 'featured_image_thumbnail',
				array(
					'get_callback'    => array( $this, 'get_image_url_thumb'),
					'update_callback' => null,
					'schema'          => null,
				)
			);
	 }

 /**
	* Get Image: Thumb
	*/
 function get_image_url_thumb(){
	 $url = $this->get_image('thumbnail');
	 return $url;
 }

 /**
	* Get Image: Full
	*/
 function get_image_url_full(){
	 $url = $this->get_image('square_thumbnail');
	 return $url;
 }

 /**
	* Get Image Helpers
	*/
 function get_image($size) {
	 $id = get_the_ID();

	 if ( has_post_thumbnail( $id ) ){
			 $img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $size );
			 $url = $img_arr[0];
			 return $url;
	 } else {
			 return false;
	 }
 }
}

new TakWPApiFeaturedImage;

add_image_size( 'square_thumbnail', 640, 640, true );