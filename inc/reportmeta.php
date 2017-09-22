<?php

/**
 * create a custom post type for report & register meta boxes for report metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

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
		'rewrite' => array('slug' => 'reports'),
		'supports' => array('title' ,'revisions', 'thumbnail' ),
		'menu_icon' => 'dashicons-media-document',
		'pubwp_type' => 'publication'
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
	$args = array('type' => 'text');       # type of field
	$series = rwmb_meta( $id, $args );
	if ( empty( $series ) ) {
		return; # no code, no problem.
	} else {
		echo esc_html( $series );
	}
}

function pubwp_print_report_publisher( ) {
	$id = '_pubwp_report_publisher'; # field id of authors
	$args = array('type' => 'post'); # type of field
	$publisher = rwmb_meta($id, $args);
	if ( empty( $publisher ) ) {
		return; # no publisher info, no problem
	} else {
		echo "Published by: <span property='publisher' typeof='Organization'>";
	 	pubwp_print_organization_info( $publisher );		
		echo "</span>";
	}

}

function pubwp_print_report_code( $before, $after) {
	$id = '_pubwp_report_code'; # field id of series name
	$args = array('type' => 'text');       # type of field
	$code = rwmb_meta( $id, $args );
	if ( empty( $code ) ) {
		return; # no code, no problem.
	} else {
		echo esc_html($before.$code.$after);
	}
}

function pubwp_report_publisher( $post ) {
	$id = '_pubwp_report_publisher'; # field id of publisher
	$args = array('type' => 'post');  # type of field
	$publisher = rwmb_meta($id, $args, $post->ID);
	if ( empty( $publisher )) {
		return false; # no publisher info, no problem
	} else {
	 	return pubwp_organization_info( $publisher );
	}
 }

function pubwp_report_info( $post ) {
	$report_info = '(';
	$post_id = $post->ID;
	$args = array('type' => 'text');       # type of field
	$series = rwmb_meta( '_pubwp_report_series', $args, $post_id);
	if ( ! empty( $series ) ) {
		 $report_info = $report_info.$series ;
	}
	$code = rwmb_meta( '_pubwp_report_code', $args, $post_id);
	if ( ! empty( $code ) ) {
		 $report_info = $report_info.' no. '.$code ;
	}
	$report_info = $report_info.').';
	$publisher = rwmb_meta( '_pubwp_report_publisher', $args, $post_id);
	if ( ! empty( $publisher ) ) {
		$report_info = $report_info.' '.pubwp_report_publisher( $post );
	}
	return esc_html($report_info);
}
