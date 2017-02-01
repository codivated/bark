<?php
/**
 * Post types & taxonomies for Bark_Logger.
 *
 * @package bark
 */

namespace Bark;

/**
 * Register bark levels.
 */
function register_levels() {
	register_taxonomy( 'bark-level', 'cdv8_bark', array(
		'label'              => __( 'Levels', 'bark' ),
		'hierarchical'       => true,
		'publicly_queryable' => false,
	) );
}
add_action( 'init', __NAMESPACE__ . '\\register_levels' );

/**
 * Setup Bark_Logger post types.
 */
function setup_post_type() {
	$labels = array(
		'name'                  => _x( 'Barks', 'Post type general name', 'bark' ),
		'singular_name'         => _x( 'Bark', 'Post type singular name', 'bark' ),
		'menu_name'             => _x( 'Barks', 'Admin Menu text', 'bark' ),
		'name_admin_bar'        => _x( 'Bark', 'Add New on Toolbar', 'bark' ),
		'add_new'               => __( 'Add New', 'bark' ),
		'add_new_item'          => __( 'Add New Bark', 'bark' ),
		'new_item'              => __( 'New Bark', 'bark' ),
		'edit_item'             => __( 'Edit Bark', 'bark' ),
		'view_item'             => __( 'View Bark', 'bark' ),
		'all_items'             => __( 'All Barks', 'bark' ),
		'search_items'          => __( 'Search Barks', 'bark' ),
		'parent_item_colon'     => __( 'Parent Barks:', 'bark' ),
		'not_found'             => __( 'No barks found.', 'bark' ),
		'not_found_in_trash'    => __( 'No barks found in Trash.', 'bark' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => array( 'slug' => 'bark' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author' ),
	);

	register_post_type( 'cdv8_bark', $args );
}
add_action( 'init', __NAMESPACE__ . '\\setup_post_type' );

function admin_column_content( $column_name, $post_id ) {
	$post = get_post( $post_id );
	$decoded_content = json_decode( $post->post_content );
	$levels = wp_get_post_terms( $post_id, 'bark-level' );

	if ( 'bark_file' === $column_name ) {
		echo esc_html( $decoded_content->file );
	}

	if ( 'bark_line' === $column_name ) {
		echo esc_html( $decoded_content->line );
	}

	if ( 'bark_level' === $column_name ) {
		echo $levels[0]->name;
	}
}
add_action( 'manage_cdv8_bark_posts_custom_column', __NAMESPACE__ . '\\admin_column_content', 10, 2 );

function set_admin_column_order() {
	return array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Message', 'bark' ),
		'bark_level' => __( 'Level', 'bark' ),
		'bark_file' => __( 'File', 'bark' ),
		'bark_line' => __( 'Line', 'bark' ),
		'date' => __( 'Logged', 'bark' ),
	);
}
add_filter( 'manage_cdv8_bark_posts_columns' , __NAMESPACE__ . '\\set_admin_column_order' );



