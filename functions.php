<?php
/**
 * wooden functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wooden
 */

define('WOODEN_WEBCACHE_VERSION', '0.0.3');

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wooden_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /lang/ directory.
	 * If you're building a theme based on wooden, use a find and replace
	 * to change 'wooden' to the name of your theme in all the template files.
	 */
	load_theme_textdomain('wooden', get_template_directory() . '/lang');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(array(
		'main-menu' 	=> esc_html__( 'Primary', 'wooden' ),
	));

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support('html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	));

	/**
	 * Gutenberg configuration
	 * Documentation : https://developer.wordpress.org/block-editor/developers/themes/theme-support/
	 */
	add_theme_support('editor-styles');
	add_editor_style('style-editor.css');
	add_theme_support('align-full'); // supports block full alignment
	add_theme_support('align-wide'); // supports block wide alignment
	/*
	add_theme_support('disable-custom-colors'); // disable user custom colors
	add_theme_support('disable-custom-font-sizes'); // disable user custom font sizes
	add_theme_support('disable-custom-gradients');  // disable user custom gradients
	add_theme_support('responsive-embeds'); // enable auto responsive embed
	add_theme_support('dark-editor-style'); // enable dark theme intégration
	add_theme_support('wp-block-styles'); // enable default wp block styles 
	add_theme_support( 'editor-color-palette', array(
	    array(
	        'name' => __( 'strong magenta', 'wooden' ),
	        'slug' => 'strong-magenta',
	        'color' => '#a156b4',
	    ),
	    array(
	        'name' => __( 'light grayish magenta', 'wooden' ),
	        'slug' => 'light-grayish-magenta',
	        'color' => '#d0a5db',
	    ),
	    array(
	        'name' => __( 'very light gray', 'wooden' ),
	        'slug' => 'very-light-gray',
	        'color' => '#eee',
	    ),
	    array(
	        'name' => __( 'very dark gray', 'wooden' ),
	        'slug' => 'very-dark-gray',
	        'color' => '#444',
	    ),
	) );
	add_theme_support('editor-font-sizes', array(
	    array(
	        'name' => __('small', 'wooden'),
	        'shortName' => __('S', 'wooden'),
	        'size' => 12,
	        'slug' => 'small'
	    ),
	    array(
	        'name' => __('regular', 'wooden'),
	        'shortName' => __('M', 'wooden'),
	        'size' => 16,
	        'slug' => 'regular'
	    ),
	    array(
	        'name' => __('large', 'wooden'),
	        'shortName' => __('L', 'wooden'),
	        'size' => 36,
	        'slug' => 'large'
	    ),
	    array(
	        'name' => __('larger', 'wooden'),
	        'shortName' => __('XL', 'wooden'),
	        'size' => 50,
	        'slug' => 'larger'
	    )
	));
	*/

	/**
	 * Image sizes
	 */
	add_theme_support ('post-thumbnails');

	/**
	 * Functions which enhance the theme by hooking into WordPress.
	 */
	require get_template_directory() . '/inc/template-functions.php';
}
add_action('after_setup_theme', 'wooden_setup');

/**
 * Enqueue scripts and styles for front
 */
function wooden_scripts() {
	// JS
	wp_enqueue_script('wooden-commons', get_template_directory_uri() . '/js/commons.js', array('jquery'), WOODEN_WEBCACHE_VERSION, true);
	wp_enqueue_script('wooden-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), WOODEN_WEBCACHE_VERSION, true);
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply' );
	}
	// CSS
	wp_enqueue_style('wooden-style-commons', get_template_directory_uri() . '/css/commons.css', array(), WOODEN_WEBCACHE_VERSION);
	wp_enqueue_style('wooden-style-base', get_template_directory_uri() . '/css/normalize.css', array('wooden-style-commons'), WOODEN_WEBCACHE_VERSION);
	wp_enqueue_style('wooden-style', get_stylesheet_uri(), array('wooden-style-base'), apply_filters('wooden_style_version', WOODEN_WEBCACHE_VERSION));
}
add_action('wp_enqueue_scripts', 'wooden_scripts');

/**
 * Enqueue scripts and styles for admin
 */
function wooden_admin_scripts() {
	wp_enqueue_media();
	wp_enqueue_style('wooden-style-commons', get_template_directory_uri() . '/css/commons.css', array(), WOODEN_WEBCACHE_VERSION);
	wp_enqueue_script('wooden-commons', get_template_directory_uri() . '/js/commons.js', array('jquery'), WOODEN_WEBCACHE_VERSION, true);
}
add_action('admin_enqueue_scripts', 'wooden_admin_scripts');

/**
 * Trick permettant de ne pas afficher la metabox d'une taxonomie lorsque l'argument 'meta_box_cb' de la taxonomie est à false
 * Cet argument 'meta_box_cb' n'est plus pris en charge par Gutenberg : https://github.com/WordPress/gutenberg/issues/13816
 */
add_filter('rest_prepare_taxonomy', function($response, $taxonomy, $request){
	$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
	// Context is edit in the editor
	if( $context === 'edit' && $taxonomy->meta_box_cb === false ){
		$data_response = $response->get_data();
		$data_response['visibility']['show_ui'] = false;
		$response->set_data( $data_response );
	}
	return $response;
}, 10, 3);

/**
 * Retrieve current lang (WPML support)
 * @since Woodkit 1.0
 * @return void
 */
function wooden_get_current_lang() {
	// on interroge WPML
	if (defined('ICL_LANGUAGE_CODE')){
		return ICL_LANGUAGE_CODE;
	}
	// on interroge WP
	$wp_locale = get_locale();
	if (!empty($wp_locale)){
		if (strlen($wp_locale)>4)
			return substr($wp_locale, 0, 2);
			else return $wp_locale;
	}
	// inconnu
	return "";
}

/**
 * Retrieve Wooden template's tools directory path
 * @return string
 */
function wooden_get_tools_directory () {
	return get_template_directory().'/src/tools/';
}

/**
 * Retrieve Wooden template's tools directory path
 * @return string
 */
function wooden_get_tools_directory_uri () {
	return get_template_directory_uri().'/src/tools/';
}

/**
 * Retrieve website languages (WPML support)
 * @return NULL[]
 */
function wooden_get_langs() {
	$langs = array();
	if (function_exists("icl_get_languages")){
		$wpml_langs =  icl_get_languages();
		foreach ($wpml_langs as $code => $al) {
			$langs[] = strtolower($code);
		}
	}else{
		$langs[] = strtolower(wooden_get_current_lang());
	}
	return $langs;
}
