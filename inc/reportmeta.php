<?php

/**
 * create a custom post type for report & register meta boxes for report metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

// create a custom post type for reports & register meta boxes for report metadata
// see https://codex.wordpress.org/Function_Reference/register_post_type
// hook it up to init so that it gets called good and early

add_action( 'init', 'pubwp_create_report_type' );
function pubwp_create_report_type() {
  register_post_type( 'pubwp_report',
    array(
      'labels' => array(
        'name' => __( 'Reports', 'pubwp' ),
        'singular_name' => __( 'Reports', 'pubwp' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'report'),
      'supports' => array('title' ,'revisions' )
    )
  );
}

// commonmeta.php provides a metabox for pubwp_report which includes
// url, author, publication_date

// Registering meta boxes for report-specific properties
// More info @ http://metabox.io/docs/registering-meta-boxes/
add_filter( 'rwmb_meta_boxes', 'pubwp_register_report_meta_boxes' );
function pubwp_register_report_meta_boxes( $meta_boxes ) {
    // @param array $meta_boxes List of meta boxes
    // @return array
	$prefix = '_pubwp_report_';  // prefix of meta keys keys hidden


	$meta_boxes[] = array(
		'id'         => 'pubwp_report_info',  // Meta box id
		// Meta box title - Will appear at the drag and drop handle bar. Required.
        'title'      => __( 'Report information', 'pubwp' ),
		'post_types' => array( 'pubwp_report' ),// Post types that have this metabox
		'context'    => 'normal',             // Where the meta box appear
		'priority'   => 'low',               // Order of meta box
		'autosave'   => true,                 // Auto save

		// List of meta fields
		'fields'     => array(
			// date published, as date picker
			array(
				'name'       => __( 'Publisher', 'pubwp' ),
				'id'         => "{$prefix}publisher",
				'type'  => 'post',
				'post_type'   => 'pubwp_organization',
				'field_type'  => 'select_advanced',
				'placeholder' => __( 'Select a publisher / commissioning organization', 'pubwp' ),
			),
			array(
				'name'  => __( 'Series', 'pubwp' ),
				'id'    => "{$prefix}series",
				'desc'  => __( 'Name of the series of which this report is part', 'pubwp' ),
				'type'  => 'text',
				'clone' => false
			),
			array(
				'name'  => __( 'Code', 'pubwp' ),
				'id'    => "{$prefix}code",
				'desc'  => __( 'Code or identifier for the report', 'pubwp' ),
				'type'  => 'text',
				'clone' => false
			)
		)
	);
	return $meta_boxes;
}

function pubwp_print_report_series( ) {
	$id = '_pubwp_report_series'; # field id of series name
	$type = 'type = text';       # type of field
	if ( empty( rwmb_meta( $id, $type ) ) ) {
		return; # no code, no problem.
	} else {
		echo rwmb_meta( $id, $type );
	}
}

function pubwp_print_report_publisher( ) {
	$id = '_pubwp_report_publisher'; # field id of authors
	$type = 'type = post';               # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		return; # no publisher info, no problem
	} else {
		$publisher = rwmb_meta($id, $type);
		echo "published by: <span property='publisher' typeof='Organization'>";
	 	pubwp_print_organization_info( $publisher );		
		echo "</span>";
	}

}

function pubwp_print_report_code( ) {
	$id = '_pubwp_report_code'; # field id of series name
	$type = 'type = text';       # type of field
	if ( empty( rwmb_meta( $id, $type ) ) ) {
		return; # no code, no problem.
	} else {
		echo rwmb_meta( $id, $type );
	}
}


