<?php

/*   
Component: Site
Description: Site specific functions. Most functions only need to be tweaked
Author: Surface / Trevor Morris
Author URI: http://www.madebysurface.co.uk
Version: 0.0.1
*/

// include all the function components
$functions = glob(dirname(__FILE__) . '/functions/*.php');
foreach($functions as $function) {
	if(!in_array(basename($function), array('function.php'))) {
		require_once $function;
	}
}

// include all the custom post types
$cpts = glob(dirname(__FILE__) . '/functions/custom_post_types/*.php');
foreach($cpts as $cpt) {
	if(!in_array(basename($cpt), array('_cpt.php'))) {
		require_once $cpt;
	}
}

/**
 * thumbnails
 * @desc	General thumbnail sizes. For individual post types, see relavant functions file
 * @hook	add_image_size
 */
add_theme_support('post-thumbnails');
add_theme_support('automatic-feed-links');
set_post_thumbnail_size(700, 1000, true); // Normal post thumbnails	

/**
 * template_get_nav
 * @desc	Site-wide navigation
 * @param	string	$type
 * @return	array
 */
function template_get_nav($type = null) {
	$navigation	= array(
		'home' => array(
			'text'			=> 'Home',
			'href'			=> '/',
			'class'			=> array('home'),
			'page_id'		=> 4,
		),
		'about' => array(
			'text'			=> 'About',
			'href'			=> '/about/',
			'class'			=> array('about'),
			'page_id'		=> 5,
		),
		'news-events' => array(
			'text'			=> 'News & Events',
			'href'			=> '/news-events/',
			'class'			=> array('news-events'),
			'page_id'		=> 6,
		),
		'contact' => array(
			'text'			=> 'Contact Us',
			'href'			=> '/contact/',
			'class'			=> array('contact'),
			'page_id'		=> 7,
		),
	);
	
	if($type === 'footer') {}
	
	return $navigation;
}

/**
 * template_is_section
 * @desc	Check what page is set
 * @global	object	$post
 * @param	string	$page
 * @return	boolean 
 */
function template_is_section($page) {
	global $post;
	
	if(is_search()) {
		return false; // no section is active
	}
	
	switch(strtolower($page)) {
		case 4:
		case 'homepage':
		case 'home':
			return is_front_page();
			break;
		
		case 5:
		case 'about':
		case 'about-us':
			return (is_object($post) && $post->ID === 5) || (!empty($post->ancestors) && is_array($post->ancestors) && in_array(5, $post->ancestors)) || is_page('about') || is_page('about-us');
			break;
		
		case 6:
		case 'news':
			return (is_object($post) && $post->ID === 6) || (!empty($post->ancestors) && is_array($post->ancestors) && in_array(6, $post->ancestors)) || is_home() || is_category() || is_tag() || is_singular('post') || is_date();
			break;
		
		case 7:
		case 'contact':
		case 'contact-us':
			return (is_object($post) && $post->ID === 7) || (!empty($post->ancestors) && is_array($post->ancestors) && in_array(7, $post->ancestors)) || is_page('contact') || is_page('contact-us');
			break;
	}
	
	return false;
}

/**
 * template_section_class
 * @desc	Add 'active' to the class for the navigation, based on page ID
 * @global	object			$post
 * @param	int				$page_id
 * @param	string|array	$class
 * @return	array
 */
function template_section_class($page_id, $class) {
	global $post;
	
	$classes	= !is_array($class) ? array($class) : $class;
	$active		= false;
	
	if(!is_null($page_id) && is_object($post) && ($page_id === $post->ID || in_array($post->post_name, $classes))) {
		$active = true;
		$classes[] = 'active';
	}
	if(template_is_section($page_id)) {
		$active = true;
		$classes[] = 'active';
	}
	
	return $classes;
}

/**
 * site_body_classes
 * @desc	Add extra information to the body class
 * @hook	add_filter('body_class');
 * @param	array	$classes
 * @return	array
 */
function site_body_classes($classes) {
	
	if(!is_array($classes)) {
		$classes = (array) $classes;
	}
	
	if(is_search()) {
		 // no section is active
		$classes[] = 'listing';
		return $classes;
	}
	if(is_404()) {
		 $classes[] = 'page';
	}
	
	if(template_is_section('homepage')) {
		$classes[] = 'homepage';
	}
	if(template_is_section('news')) {
		$classes[] = 'listing';
		$classes[] = 'news';
	}
	if(template_is_section('contact')) {
		$classes[] = 'contact';
	}
	
	return $classes;
}
add_filter('body_class', 'site_body_classes');

/**
 * site_new_excerpt_length
 * @hook	add_filter('excerpt_length');
 * @param	int $length
 * @return	int
 */
function site_new_excerpt_length($length) {
	return 50;
}
add_filter('excerpt_length', 'site_new_excerpt_length');

/**
 * Add excerpts to pages
 */
add_post_type_support('page', 'excerpt');

/**
 * register_javascript_css
 * @desc	Register the JavaScript and CSS
 * @hook	add_action('template_redirect');
 */
function site_register_javascript_css() {
	$post_type = get_post_type();

	if(!is_admin()) {
		wp_deregister_script('jquery');
		wp_deregister_script('NextGEN');
		wp_deregister_script('thickbox');
		wp_deregister_script('shutter');
		wp_deregister_script('swfobject');
		wp_deregister_script('events-manager');
		wp_deregister_script('jquery-cycle');
		wp_dequeue_style('NextGEN');
		wp_dequeue_style('shutter');
		wp_dequeue_style('thickbox');
		wp_dequeue_style('events-manager');
		wp_dequeue_style('ngg-slideshow');

		// javascript
		wp_register_script('jquery', get_stylesheet_directory_uri() . '/js/jquery/1.8.3.js', false, '1.8.3', true);
		wp_register_script('plugin.cycle', get_stylesheet_directory_uri() . '/js/jquery/plugin/cycle-2.99.js', false, '2.99', true);
		wp_register_script('plugin.fancybox', get_stylesheet_directory_uri() . '/js/jquery/plugin/fancybox-2.0.6.js', false, '2.0.6', true);
		
		// app
		wp_register_script('app', get_stylesheet_directory_uri() . '/js/app/app.js', array('jquery'), '1.0', true);
		wp_register_script('app.options', get_stylesheet_directory_uri() . '/js/app/options.js', array('jquery', 'app'), '1.0', true);
		wp_register_script('app.models', get_stylesheet_directory_uri() . '/js/app/models.js', array('jquery', 'app'), '1.0', true);
		wp_register_script('app.run', get_stylesheet_directory_uri() . '/js/app/run.js', array('jquery', 'app'), '1.0', true);
		
		wp_enqueue_script('jquery');
		
		if(template_is_section('homepage')) {
			wp_enqueue_script('plugin.cycle');
		}
		if(template_is_section('gallery')) {
			wp_enqueue_script('plugin.fancybox');
		}
		
		wp_enqueue_script('app');
		wp_enqueue_script('app.options');
		wp_enqueue_script('app.models');
		wp_enqueue_script('app.run');

		// css
		wp_enqueue_style('open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,600', false, false);
		wp_enqueue_style('normalize', get_stylesheet_directory_uri() . '/css/normalize.css', false, false);
		wp_enqueue_style('screen', get_stylesheet_directory_uri() . ' /css/screen.css', false, false);
	}
}
add_action('template_redirect', 'site_register_javascript_css');

/**
 * register_navigation
 * @desc	Registering custom navigations with WordPress
 * @hook	add_action('init');
 */
function register_navigation() {
	register_nav_menus(array(
		'main'	=> __('Main Navigation'),
	));
}
//add_action('init', 'register_navigation');