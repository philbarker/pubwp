<?php

/**
 * create a custom post type for organization & register meta boxes for organization metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

// create a custom post type for organization & register meta boxes for organization metadata
// see https://codex.wordpress.org/Function_Reference/register_post_type
// hook it up to init so that it gets called good and early
add_action( 'init', 'pubwp_create_organization_type' );
function pubwp_create_organization_type() {
  register_post_type( 'pubwp_organization',
    array(
      'labels' => array(
        'name' => __( 'Organizations', 'pubwp' ),
        'singular_name' => __( 'Organization', 'pubwp' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'organization'),
      'supports' => array('revisions' )
    )
  );
}


// Registering meta boxes for schema:Organization properties
// More info @ http://metabox.io/docs/registering-meta-boxes/
add_filter( 'rwmb_meta_boxes', 'pubwp_register_organization_meta_boxes' );
function pubwp_register_organization_meta_boxes( $meta_boxes ) {
    // @param array $meta_boxes List of meta boxes
    // @return array
	$prefix = '_pubwp_organization_';  // prefix of meta keys keys hidden

	$meta_boxes[] = array(
		'id'         => 'pubwp_organization_info',  // Meta box id
		// Meta box title - Will appear at the drag and drop handle bar. Required.
        'title'      => __( 'Information about a organization, e.g. a publisher or person\'s afiliation', 'pubwp' ),
		'post_types' => array('pubwp_organization' ),     // Post types that have this metabox
		'context'    => 'normal',             // Where the meta box appear
		'priority'   => 'high',               // Order of meta box
		'autosave'   => true,                 // Auto save

		// List of meta fields
		'fields'     => array(
			// given name(s), a text field
			array(
				'name'  => __( 'Name', 'pubwp' ),
				'id'    => "{$prefix}name",
				'desc'  => __( 'The name of the organization', 'pubwp' ),
				'type'  => 'text',
			),
			// URI, a text field
			array(
				'name'  => __( 'Location', 'pubwp' ),
				'id'    => "{$prefix}location",
				'desc'  => __( 'The location of the organization', 'pubwp' ),
				'type'  => 'text',
				'clone' => true,
			),
			// URI, a text field
			array(
				'name'  => __( 'URI', 'pubwp' ),
				'id'    => "{$prefix}uri",
				'desc'  => __( 'A URI that identifies the organization', 'pubwp' ),
				'type'  => 'url',
				'clone' => true,
			),
		),
	);
	return $meta_boxes;
}

// Organization custom post type does not support title, but do need a title to display
// in selection boxes when linking to an organization from another post.
add_filter( 'wp_insert_post_data', 'pubwp_modify_organization_title', 99, 1 );
function pubwp_modify_organization_title( $data ) {
	$prefix = '_pubwp_organization_';
	if ( isset($_POST['post_type'])  && ('pubwp_organization' == $_POST['post_type']) ) {
		if (isset($_POST["{$prefix}name"])) {
			$data['post_title'] = $_POST["{$prefix}name"];
		} else {
			$data['post_title'] = 'Any mouse organization';
		}
	}
	return $data;
}

// Organization custom post type does not support title, so need to display other useful
// info in admin post list pages. 
// see https://www.smashingmagazine.com/2013/12/modifying-admin-post-lists-in-wordpress/
add_filter('manage_pubwp_organization_posts_columns', 'pubwp_organization_table_head');
function pubwp_organization_table_head( $defaults ) {
    $prefix = '_pubwp_organization_';
    $defaults["{$prefix}name"]  = __('Name', 'pubwp' );
    return $defaults;
}

add_action( 'manage_pubwp_organization_posts_custom_column', 'pubwp_organization_table_content', 10, 2 );
function pubwp_organization_table_content( $column_name, $post_id ) {
	$prefix = '_pubwp_organization_';  // prefix of meta keys keys hidden
    if ( $column_name == "{$prefix}name" ) {
		echo rwmb_meta( "{$prefix}name" );
    }
}

function pubwp_print_organization_info( $id ) {
// Prints information about organization, wrapped up in RDFa typeof schema:Organization
	$prefix = '_pubwp_organization_';
	$args = array();
	$name = esc_html( rwmb_meta( "{$prefix}name", $args, $post_id = $id ) );
	$location_arr = rwmb_meta( "{$prefix}location", $args, $post_id = $id );
	$url_arr = rwmb_meta( "{$prefix}uri", $args, $post_id = $id );
	if (! $name ) {
		return; # no publisher info, no problem
	} else {
		echo "<span property ='name'>{$name}</span>, ";
		foreach ($url_arr as $url) {
			$url = esc_url( $url );
			echo "<link property='url' href='{$url}' />";
		}
		foreach ($location_arr as $location) {
			$location = esc_html( $location );
			echo "<span property = 'location' typeof='Place'><span property='name'>{$location}</span></span> ";
		}
	}
}

