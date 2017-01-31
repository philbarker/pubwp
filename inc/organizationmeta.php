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
add_filter( 'wp_insert_post_data', 'pubwp_modify_post_title', 10, 1 );
function pubwp_modify_post_title( $data ) {
	$prefix = '_pubwp_organization_';
	if ('pubwp_organization' == $data['post_type'] && rwmb_meta( "{$prefix}name" ) ) 
		$data['post_title'] = rwmb_meta( "{$prefix}name" );
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

function pubwp_print_organization_fullname( ) {
// Prints a organizations full name using display name is present, or GivenName FamilyName if not.
// Wraps name in schema.org property terms name, givenName, familyName
    $prefix = '_pubwp_organization_';
    if ( rwmb_meta( "{$prefix}name" ) ) {
		echo '<span property ="name">'.rwmb_meta( "{$prefix}name" ).'</span>';
    } else {
		echo ' ';
	}
}

function pubwp_print_organization_uri_as_link( ) {
    if ( rwmb_meta( '_pubwp_organization_uri' ) ) {
         echo sprintf('<link property="url" href="%s" />', rwmb_meta( '_pubwp_organization_uri' ) );
    } else {
		echo ' ';
	}
}
