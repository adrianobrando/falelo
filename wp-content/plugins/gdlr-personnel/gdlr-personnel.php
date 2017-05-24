<?php
/*
Plugin Name: Goodlayers Personnel Post Type
Plugin URI: 
Description: A Custom Post Type Plugin To Use With Goodlayers Theme ( This plugin functionality might not working properly on another theme )
Version: 1.0.0
Author: Goodlayers
Author URI: http://www.goodlayers.com
License: 
*/
include_once( 'gdlr-personnel-item.php');	
include_once( 'gdlr-personnel-option.php');	

// action to loaded the plugin translation file
add_action('plugins_loaded', 'gdlr_personnel_init');
if( !function_exists('gdlr_personnel_init') ){
	function gdlr_personnel_init() {
		load_plugin_textdomain( 'gdlr-personnel', false, dirname(plugin_basename( __FILE__ ))  . '/languages/' ); 
	}
}

// add action to create personnel post type
add_action( 'init', 'gdlr_create_personnel' );
if( !function_exists('gdlr_create_personnel') ){
	function gdlr_create_personnel() {
		global $theme_option;
		
		if( !empty($theme_option['personnel-slug']) ){
			$personnel_slug = $theme_option['personnel-slug'];
			$personnel_category_slug = $theme_option['personnel-category-slug'];
		}else{
			$personnel_slug = 'personnel';
			$personnel_category_slug = 'personnel_category';
		}
		
		register_post_type( 'personnel',
			array(
				'labels' => array(
					'name'               => __('Personnel', 'gdlr-personnel'),
					'singular_name'      => __('Personnel', 'gdlr-personnel'),
					'add_new'            => __('Add New', 'gdlr-personnel'),
					'add_new_item'       => __('Add New Personnel', 'gdlr-personnel'),
					'edit_item'          => __('Edit Personnel', 'gdlr-personnel'),
					'new_item'           => __('New Personnel', 'gdlr-personnel'),
					'all_items'          => __('All Personnel', 'gdlr-personnel'),
					'view_item'          => __('View Personnel', 'gdlr-personnel'),
					'search_items'       => __('Search Personnel', 'gdlr-personnel'),
					'not_found'          => __('No personnel found', 'gdlr-personnel'),
					'not_found_in_trash' => __('No personnel found in Trash', 'gdlr-personnel'),
					'parent_item_colon'  => '',
					'menu_name'          => __('Personnel', 'gdlr-personnel')
				),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $personnel_slug  ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 5,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' )
			)
		);
		
		// create personnel categories
		register_taxonomy(
			'personnel_category', array("personnel"), array(
				'hierarchical' => true,
				'show_admin_column' => true,
				'label' => __('Personnel Categories', 'gdlr-personnel'), 
				'singular_label' => __('Personnel Category', 'gdlr-personnel'), 
				'rewrite' => array( 'slug' => $personnel_category_slug  )));
		register_taxonomy_for_object_type('personnel_category', 'personnel');

		// add filter to style single template
		if( defined('WP_THEME_KEY') && WP_THEME_KEY == 'goodlayers' ){
			add_filter('single_template', 'gdlr_register_personnel_template');
		}
	}
}

if( !function_exists('gdlr_register_personnel_template') ){
	function gdlr_register_personnel_template($single_template) {
		global $post;

		if ($post->post_type == 'personnel') {
			$single_template = dirname( __FILE__ ) . '/single-personnel.php';
		}
		return $single_template;	
	}
}

?>