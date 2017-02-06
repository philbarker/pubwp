<?php

/**
 * create a custom post type for person & register meta boxes for person metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

// create a custom post type for person & register meta boxes for person metadata
// see https://codex.wordpress.org/Function_Reference/register_post_type
// hook it up to init so that it gets called good and early
add_action( 'init', 'pubwp_create_person_type' );
function pubwp_create_person_type() {
  register_post_type( 'pubwp_person',
    array(
      'labels' => array(
        'name' => __( 'People', 'pubwp' ),
        'singular_name' => __( 'Person', 'pubwp' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'person'),
      'supports' => array('revisions' )
    )
  );
}


// Registering meta boxes for schema:Person properties
// More info @ http://metabox.io/docs/registering-meta-boxes/
add_filter( 'rwmb_meta_boxes', 'pubwp_register_person_meta_boxes' );
function pubwp_register_person_meta_boxes( $meta_boxes ) {
    // @param array $meta_boxes List of meta boxes
    // @return array
	$prefix = '_pubwp_person_';  // prefix of meta keys keys hidden

	$meta_boxes[] = array(
		'id'         => 'pubwp_person_info',  // Meta box id
		// Meta box title - Will appear at the drag and drop handle bar. Required.
        'title'      => __( 'Information about a person, e.g. the name of an author', 'pubwp' ),
		'post_types' => array('pubwp_person' ),     // Post types that have this metabox
		'context'    => 'normal',             // Where the meta box appear
		'priority'   => 'high',               // Order of meta box
		'autosave'   => true,                 // Auto save

		// List of meta fields
		'fields'     => array(
			// given name(s), a text field
			array(
				'name'  => __( 'Given name(s)', 'pubwp' ),
				'id'    => "{$prefix}given_name",
				'desc'  => __( 'The given name(s) of the person. See also family name and display name', 'pubwp' ),
				'type'  => 'text',
			),
			// family name, a text field
			array(
				'name'  => __( 'Family name', 'pubwp' ), // Field name - Will be used as label				
				'id'    => "{$prefix}family_name", // Field ID, i.e. the meta key
				// Field description (optional)
				'desc'  => __( 'The family name of the person. See also given name and display name', 'pubwp' ),
				'type'  => 'text',
			),
			// display name, a text field
			array(
				'name'  => __( 'Display name', 'pubwp' ),
				'id'    => "{$prefix}display_name",
				'desc'  => __( 'The name to be displayed if different to GivenName FamilyName', 'pubwp' ),
				'type'  => 'text',
			),
			// URI, a text field
			array(
				'name'  => __( 'URI', 'pubwp' ),
				'id'    => "{$prefix}uri",
				'desc'  => __( 'A URI that identifies the person, e.g. their ORCID ID', 'pubwp' ),
				'type'  => 'url',
				'clone' => true,
			),
		),
	);
	return $meta_boxes;
}


// Person custom post type does not support title, but do need a title to display
// in selection boxes when linking to an person from another post.
add_filter( 'wp_insert_post_data', 'pubwp_modify_person_title', 99, 1 );
function pubwp_modify_person_title( $data ) {
	$prefix = '_pubwp_person_';
	$display_name = '';
	$given_name = '';
	$family_name = '';
	if ( isset($_POST['post_type'])  && ('pubwp_person' == $_POST['post_type'])) {
		if (isset($_POST["{$prefix}display_name"]))
			$display_name = $_POST["{$prefix}display_name"];
		if (isset($_POST["{$prefix}given_name"]))
			$given_name = $_POST["{$prefix}given_name"];
		if (isset($_POST["{$prefix}family_name"]))
			$family_name = $_POST["{$prefix}family_name"];
		if ($display_name != '') {
			$data['post_title'] = $display_name;
		} elseif (($given_name != '') ||  ($family_name != '')) {
			$data['post_title'] = $given_name.' '.$family_name;
		} else {
			$data['post_title'] = 'Any mouse';
		}
	}
	return $data;
}

// Person custom post type does not support title, so need to display other useful
// info in admin post list pages. 
// see https://www.smashingmagazine.com/2013/12/modifying-admin-post-lists-in-wordpress/
add_filter('manage_pubwp_person_posts_columns', 'pubwp_person_table_head');
function pubwp_person_table_head( $defaults ) {
    $prefix = '_pubwp_person_';
    $defaults["{$prefix}given_name"]  = __('Given name', 'pubwp' );
    $defaults["{$prefix}family_name"]  = __('Family name', 'pubwp' );
    $defaults["{$prefix}display_name"]  = __('Display name', 'pubwp' );
    return $defaults;
}

add_action( 'manage_pubwp_person_posts_custom_column', 'pubwp_person_table_content', 10, 2 );
function pubwp_person_table_content( $column_name, $post_id ) {
	$prefix = '_pubwp_person_';  // prefix of meta keys keys hidden
    if ( $column_name == "{$prefix}given_name" ) {
		echo rwmb_meta( "{$prefix}given_name" );
    }
    if ( $column_name == "{$prefix}family_name" ) {
		echo rwmb_meta( "{$prefix}family_name" );
    }
    if ( $column_name == "{$prefix}display_name" ) {
		echo rwmb_meta( "{$prefix}display_name" );
    }
}

function pubwp_print_person_fullname( $id ) {
// Prints a persons full name using display name is present, or GivenName FamilyName if not.
// Wraps name in schema.org property terms name, givenName, familyName
    $prefix = '_pubwp_person_';
    $args = array();
    $family_name = esc_html( rwmb_meta( "{$prefix}family_name", $args, $post_id = $id) );
    $given_name = esc_html( rwmb_meta( "{$prefix}given_name", $args, $post_id = $id) );
    $display_name = esc_html( rwmb_meta( "{$prefix}display_name", $args, $post_id = $id) );

    if ( $display_name ) {
		echo '<span property ="name">'.$display_name.'</span>';
	} elseif ( $family_name || $given_name ) {
		echo '<span property ="name">';
		echo '<span property ="givenName">'.$given_name.'</span> ';
		echo '<span property ="familyName">'.$family_name.'</span>';
		echo '</span>';
    } else {
		echo 'anon';
	}
}

function pubwp_print_person_uri_as_link( ) {
    if ( rwmb_meta( '_pubwp_person_uri' ) ) {
         echo sprintf('<link property="url" href="%s" />', rwmb_meta( '_pubwp_person_uri' ) );
    } else {
		echo ' ';
	}
}

