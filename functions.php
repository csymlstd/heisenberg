<?php
/**
 * Heisenberg functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Heisenberg
 */

if ( ! function_exists( 'heisenberg_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function heisenberg_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Heisenberg, use a find and replace
	 * to change 'heisenberg' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'heisenberg', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'heisenberg' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'heisenberg_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // heisenberg_setup
add_action( 'after_setup_theme', 'heisenberg_setup' );


/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function _heisenberg_content_width() {
	$GLOBALS['content_width'] = apply_filters( '_heisenberg_content_width', 640 );
}
add_action( 'after_setup_theme', '_heisenberg_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function heisenberg_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'heisenberg' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'heisenberg_widgets_init' );

/**
 * Enqueue styles.
 */
if ( !function_exists( 'heisenberg_styles' ) ) :

	function heisenberg_styles() {
		// Enqueue our stylesheet
		$handle = 'heisenberg_styles';
		$src =  get_template_directory_uri() . '/assets/dist/css/app.css';
		$deps = '';
		$ver = filemtime( get_template_directory() . '/assets/dist/css/app.css');
		$media = '';
		wp_enqueue_style( $handle, $src, $deps, $ver, $media );
	}

add_action( 'wp_enqueue_scripts', 'heisenberg_styles' );

endif;


/**
 * Enqueue scripts.
 */
function heisenberg_scripts() {

	// Add Foundation JS to footer
	wp_enqueue_script( 'foundation-js',
		get_template_directory_uri() . '/assets/dist/js/foundation.js',
		array( 'jquery' ), '6.2.3', true
	);

	// Add our concatenated JS file after Foundation
	$handle = 'heisenberg_appjs';
	$src =  get_template_directory_uri() . '/assets/dist/js/app.js';
	$deps = array( 'jquery' );
	$ver = filemtime( get_template_directory() . '/assets/dist/js/app.js');
	$in_footer = true;
	wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'heisenberg_scripts' );


/**
 * Disable Auto Paragraphs
 *
 * @link https://codex.wordpress.org/Function_Reference/wpautop
 */
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );


/**
 *
 * Add ACF Options Page
 *
 */
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
}


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';



/*******************************************************************************
* Make YouTube and Vimeo oembed elements responsive. Add Foundation's .flex-video
* class wrapper around any oembeds
*******************************************************************************/

add_filter( 'embed_oembed_html', 'heisenberg_oembed_flex_wrapper', 10, 4 );
function heisenberg_oembed_flex_wrapper( $html, $url, $attr, $post_ID ) {
	if ( strpos($url, 'youtube') || strpos($url, 'youtu.be') || strpos($url, 'vimeo') ) {
		return '<div class="flex-video widescreen">' . $html . '</div>';
	}
	return $html;
}


/*******************************************************************************
* Custom login styles for the theme. Sass file is located in ./assets/login.scss
* and is spit out to ./assets/dist/css/login.css by gulp. Functions are here so
* that you can move it wherever works best for your project.
*******************************************************************************/

// Load the CSS
add_action( 'login_enqueue_scripts', 'heisenberg_login_css' );
function heisenberg_login_css() {
	wp_enqueue_style( 'heisenberg_login_css', get_template_directory_uri() .
	'/assets/dist/css/login.css', false );
}

// Change header link to our site instead of wordpress.org
add_filter( 'login_headerurl', 'heisenberg_remove_logo_link' );
function heisenberg_remove_logo_link() {
	return get_bloginfo( 'url' );
}

// Change logo title in from WordPress to our site name
add_filter( 'login_headertitle', 'heisenberg_change_login_logo_title' );
function heisenberg_change_login_logo_title() {
	return get_bloginfo( 'name' );
}

/**
 * Register meta box for color options.
 *
 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
 */
function mb_colors_register_meta_boxes() {
    add_meta_box( 'mb-colors', 'Brand Colors', 'mb_colors_display', array('page','post'), 'side', 'low' );
}
add_action( 'add_meta_boxes', 'mb_colors_register_meta_boxes' );

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function mb_colors_display( $post ) {
	//wp_nonce_field( basename( __FILE__ ), 'mb_colors_nonce' );

	echo '<p>Click color to copy hex to clipboard.</p>';

	echo '<div class="color-select clipboard bg-red" data-clipboard-text="#ED5549">#ed5549</div>';
	echo '<div class="color-select clipboard bg-black" data-clipboard-text="#111f32">#111f32</div>';
}

function add_color_scripts( $hook ) {

  global $post;
  wp_enqueue_script(  'clipboard-js', get_stylesheet_directory_uri().'/assets/js/clipboard.min.js', null, null, true );

	wp_register_style('heisenberg_admin', get_stylesheet_directory_uri().'/assets/dist/css/admin.css');
	wp_enqueue_style('heisenberg_admin');
}
add_action( 'admin_enqueue_scripts', 'add_color_scripts', 10, 1 );

/*******************************************************************************
* Shortcode for Social Media
*******************************************************************************/

add_filter('widget_text', 'do_shortcode');

function social_shortcode($atts) {
	$output = '<div class="social">';

	if(have_rows('social_media', 'option')):
		while(have_rows('social_media', 'option')): the_row();
			$output .= '<a href="'.get_sub_field('url').'" class="fa social fa-'.get_sub_field('media').'" title=""></a>';
		endwhile;
	endif;

	$output .= '</div>';

	return $output;
}
add_shortcode('social_media', 'social_shortcode');

function contact_shortcode($atts) {
	if($atts['id'] == 'address') {
		$output = get_field('address', 'option').' '.get_field('city','option').', '.get_field('state','option').' '.get_field('zip', 'option');
	} elseif($atts['id'] == 'email') {
		$output = get_field('email','option');
	} elseif($atts['id'] == 'phone') {
		$output = get_field('phone_number', 'option');
	}

	return $output;
}
add_shortcode('contact','contact_shortcode');

function logo_shortcode($atts) {

	$file = file_get_contents( get_template_directory() . '/assets/img/svg/'.$atts['type'].'-'.$atts['id'].'.svg' );
	if(isset($atts['class'])) { $class = $atts['class']; } else { $class = ''; }
	if($file) {
		if(isset($atts['alt'])) {
			print '<div class="logo alt '.$atts['type'].'-'.$atts['id'].' '.$class.'">';
		} else {
			print '<div class="logo '.$atts['type'].'-'.$atts['id'].' '.$class.'">';
		}

		print $file;

		print '</div>';
	} else {
		print 'A matching logo could not be found.';
	}

}
add_shortcode('logo', 'logo_shortcode');

function block_shortcode($atts) {
	if(isset($atts['id'])) {
		if($atts['id'] == 'our_people') {
			$output = '';

			$args = array(
				'post_type' => 'people'
			);
			$loop = new WP_Query($args);

			while($loop->have_posts()): $loop->the_post();
				$output .= '<div class="small-10 medium-4 large-3 people">';
					if(get_field('photo')):	  			$output .= '<div class="photo"><img src="'.get_field('photo').'" /></div>'; endif;
																					$output .= '<h4 class="name">'.get_field('first_name').' '.get_field('last_name').'</h4>';
				  if(get_field('position')):			$output .= '<div class="position">'.get_field('position').'</div>'; endif;
					if(get_field('email')):	    		$output .= '<div class="email"><a href="mailto:'.get_field('email').'">'.get_field('email').'</a></div>'; endif;
					if(get_field('phone_number')):	$output .= '<div class="phone">'.get_field('phone_number').'</div>'; endif;
				$output .= '</div>';
			endwhile;

			wp_reset_postdata();

		}
	}
	return $output;
}

add_shortcode('block', 'block_shortcode');
