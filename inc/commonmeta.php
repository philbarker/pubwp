<?php

/**
 * register meta boxes for metadata that are common to several publication types
 **
 * provides a metabox for pubwp_book which includes
 * _pubwp_common_ title, url, author, publication_date
 * requires: meta box plugin http://metabox.io/
 *
 **/

// Registering meta boxes for commonly used properties
// More info @ http://metabox.io/docs/registering-meta-boxes/
add_filter( 'rwmb_meta_boxes', 'pubwp_register_common_meta_boxes' );
function pubwp_register_common_meta_boxes( $meta_boxes ) {
    // @param array $meta_boxes List of meta boxes
    // @return array
	$prefix = '_pubwp_common_';  // prefix of meta keys keys hidden

	$meta_boxes[] = array(
		'id'         => 'pubwp_common_info',  // Meta box id
		// Meta box title - Will appear at the drag and drop handle bar. Required.
        'title'      => __( 'General information', 'pubwp' ),
		'post_types' => array( 'pubwp_book',  // Post types that have this metabox
		                       'pubwp_report' ),
		'context'    => 'normal',             // Where the meta box appear
		'priority'   => 'high',               // Order of meta box
		'autosave'   => true,                 // Auto save

		// List of meta fields
		'fields'     => array(
			// date published, as date picker
			array(
				'name'       => __( 'Date published', 'pubwp' ),
				'id'         => "{$prefix}date_published",
				'type'       => 'date',
				// jQuery date picker options. See here http://api.jqueryui.com/datepicker
				'js_options' => array(
					'appendText'      => __( '(yyyy-mm-dd)', 'pubwp' ),
					'dateFormat'      => __( 'yyyy-mm-dd', 'pubwp' ),
					'changeMonth'     => true,
					'changeYear'      => true,
					'showButtonPanel' => false,
				)
			),
			// Author, as post of type Person
			array(
				'name'  => __( 'Author Link', 'pubwp' ),
				'id'    => "{$prefix}author_person",
				'desc'  => __( 'Link to Person information for author', 'pubwp' ),
				'type'  => 'post',
				'post_type'   => 'pubwp_person',
				'field_type'  => 'select_advanced',
				'placeholder' => __( 'Select an author', 'pubwp' ),
				'clone' => true
			),		
			// URI, a text field
			array(
				'name'  => __( 'URI', 'pubwp' ),
				'id'    => "{$prefix}uri",
				'desc'  => __( 'A URI that identifies the work, e.g. based on a DOI', 'pubwp' ),
				'type'  => 'url',
				'clone' => true
			),
			// Peer reviewed, a yes/no check box
			array(
				'name'  => __( 'Peer reviewd', 'pubwp' ),
				'id'    => "{$prefix}peer_reviewed",
				'desc'  => __( 'Was the publication peer reviewed', 'pubwp' ),
				'type'  => 'radio',
				'options' => array(
				    'yes' => 'yes',
				    'no'  => 'no'
				    ),
				'clone' => false
			),
		),
	);
	return $meta_boxes;
}

