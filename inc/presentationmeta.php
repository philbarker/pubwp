<?php

/**
 * create a custom post type for presentations & register meta boxes for 
 * conference paper metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

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
		'rewrite' => array('slug' => 'presentations'),
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
				'id'    => "{$prefix}dates",
				'desc'  => __( 'date range for conference', 'pubwp' ),
				'type'  => 'text',
				'clone' => false
			)
		)
	);
	return $meta_boxes;
}

function pubwp_print_meeting_info( ) {
	$prefix = '_pubwp_presentation_';
	$args = array( 'type' => 'text' );
	$conference_name = rwmb_meta( "{$prefix}conference_name", $args );
	$conference_location = rwmb_meta( "{$prefix}conference_location", $args );
	$conference_dates = rwmb_meta( "{$prefix}dates", $args );

	if ( empty( $conference_name ) ) {
			$conference_name = False; 
		} else {
			$conference_name = esc_html( $conference_name );
		}
	if ( empty( $conference_location ) ) {
			$conference_location = False; 
		} else {
			$conference_location = esc_html( $conference_location );
		}
	if ( empty( $conference_dates ) ) {
			$conference_dates = False; 
		} else {
			$conference_dates = esc_html( $conference_dates );
		}

	if ($conference_name) {
		echo "<span property='recordedAt' typeof='Event'>
		<span property='name'>{$conference_name}</span>";
		if ($conference_location) { 
			echo ", <span property='location' typeof='Place'><span property='name'>{$conference_location}</span></span> ";
		}
		if ($conference_dates) {
			echo " ({$conference_dates}).";
		}
	echo "</span>";
	}	
}

function pubwp_presentation_info( $post ) {
	$prefix = '_pubwp_presentation_';
	$args = array( 'type' => 'text' );
	$conference_name = rwmb_meta( "{$prefix}conference_name", $args, $post->ID );
	$conference_location = rwmb_meta( "{$prefix}conference_location", $args, $post->ID );
	$conference_dates = rwmb_meta( "{$prefix}dates", $args, $post->ID );
	$presentation_info = '';
	
	if ( ! empty( $conference_name ) ) {
			$presentation_info = $presentation_info.'Presented at '.$conference_name;
	}
	if ( ! empty( $conference_location ) ) {
			$presentation_info = $presentation_info.', '.$conference_location;
	}
	if ( ! empty( $conference_dates ) ) {
		$presentation_info = $presentation_info.' '.$conference_dates;
	}
	if (! empty( $presentation_info ) ) {
		return esc_html( $presentation_info );
	} else {
		return false;
	}	
}

