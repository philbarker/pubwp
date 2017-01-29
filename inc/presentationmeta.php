<?php

/**
 * create a custom post type for presentations & register meta boxes for 
 * conference paper metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

// create a custom post type for presentations & register meta boxes for 
// conference paper metadata
// see https://codex.wordpress.org/Function_Reference/register_post_type
// hook it up to init so that it gets called good and early

add_action( 'init', 'pubwp_create_presentation_type' );
function pubwp_create_presentation_type() {
  register_post_type( 'pubwp_presentation',
    array(
      'labels' => array(
        'name' => __( 'Presentations', 'pubwp' ),
        'singular_name' => __( 'Presentation', 'pubwp' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'presentation'),
      'supports' => array('title' ,'revisions' )
    )
  );
}

// commonmeta.php provides a metabox for pubwp_presentation which includes
// url, author, publication_date

// Registering meta boxes for presentation-specific properties
// More info @ http://metabox.io/docs/registering-meta-boxes/
add_filter( 'rwmb_meta_boxes', 'pubwp_register_presentation_meta_boxes' );
function pubwp_register_presentation_meta_boxes( $meta_boxes ) {
    // @param array $meta_boxes List of meta boxes
    // @return array
	$prefix = '_pubwp_presentation_';  // prefix of meta keys keys hidden


	$meta_boxes[] = array(
		'id'         => 'pubwp_presentation_info',  // Meta box id
		// Meta box title - Will appear at the drag and drop handle bar. Required.
        'title'      => __( 'Presentation information', 'pubwp' ),
		'post_types' => array( 'pubwp_presentation' ),// Post types that have this metabox
		'context'    => 'normal',             // Where the meta box appear
		'priority'   => 'low',               // Order of meta box
		'autosave'   => true,               // Auto save

		// List of meta fields
		'fields'     => array(
			// conference name as TEXT
			array(
				'name'       => __( 'Conference or meeting name', 'pubwp' ),
				'id'         => "{$prefix}conference_name",
				'type'  => 'text'
			),
			// conference location as TEXT
			array(
				'name'       => __( 'Conference or meeting location', 'pubwp' ),
				'id'         => "{$prefix}conference_location",
				'type'  => 'text'
			),
			array(
				'name'  => __( 'dates', 'pubwp' ),
				'id'    => "{$prefix}code",
				'desc'  => __( 'date range for conference', 'pubwp' ),
				'type'  => 'text',
				'clone' => false
			)
		)
	);
	return $meta_boxes;
}

