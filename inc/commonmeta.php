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
		                       'pubwp_report',
		                       'pubwp_presentation',
		                       'pubwp_chapter',
				       'pubwp_paper' ),
		'context'    => 'normal',             // Where the meta box appear
		'priority'   => 'high',               // Order of meta box
		'autosave'   => true,                 // Auto save

		// List of meta fields

		'fields'     => array(
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
			// URI, a text field
			array(
				'name'  => __( 'URI', 'pubwp' ),
				'id'    => "{$prefix}uri",
				'desc'  => __( 'A URI that identifies the work', 'pubwp' ),
				'type'  => 'url',
				'clone' => true
			),
			// DOI, a text field
			array(
				'name'  => __( 'Doi', 'pubwp' ),
				'id'    => "{$prefix}doi",
				'desc'  => __( 'DOI of the work', 'pubwp' ),
				'type'  => 'text',
				'clone' => false
			),
			// Abstract, as post of type WYSIWYG
			array(
				'name'  => __( 'Abstract', 'pubwp' ),
				'id'    => "{$prefix}abstract",
				'desc'  => __( 'Abstract', 'pubwp' ),
				'type'  => 'WYSIWYG',
				'clone' => false
			),
			// Local copy file upload
			array(
				'name'  => __( 'Local copy', 'pubwp' ),
				'id'    => "{$prefix}local_copy",
				'desc'  => __( 'Upload a local copy', 'pubwp' ),
				'type'  => 'file_upload',
				'clone' => false
			),
			// Local copy file version
			array(
				'name'  => __( 'Version', 'pubwp' ),
				'id'    => "{$prefix}local_version",
				'desc'  => __( 'Version of local copy, e.g. authors final draft', 'pubwp' ),
				'type'  => 'text',
				'clone' => false
			),
			// Local copy licence
			array(
				'name'  => __( 'Licence', 'pubwp' ),
				'id'    => "{$prefix}local_licence",
				'desc'  => __( 'Licence of local copy, e.g. CC:BY', 'pubwp' ),
				'type'  => 'text',
				'clone' => false
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

function pubwp_print_authors( ) {
	$prefix = '_pubwp_common_';
	if ( empty( rwmb_meta("{$prefix}author_person", 'type = post') ) ) {
		//not much we can do with no authors -- shouldn't happen!
		echo('What, no authors?'); //for debug only
		return;
	} else {
		$authors = rwmb_meta("{$prefix}author_person", 'type = post');
		$len = count($authors);
		$i = 0;
		foreach ($authors as $author) {
			$i = $i+1;
			echo '<span property="author">';
			pubwp_print_person_fullname( $author );
			echo '</span>';
			if ($i < ($len - 1) ) {
				echo ', ';
			} elseif ($i == ($len - 1) ) {
				echo ' and ';
			}
		}
	}
}

function pubwp_print_date_published( ) {
	$prefix = '_pubwp_common_';
	if ( empty( rwmb_meta("{$prefix}date_published", 'type = date') ) ) {
		//not much we can do with no authors -- shouldn't happen!
		echo('What, no publication date?'); //for debug only
		return;
	} else {
		$publication_date = rwmb_meta("{$prefix}date_published", 'type = date');
		$publication_year = esc_html( explode( '-', $publication_date )[0] );
		$publication_date = esc_html( $publication_date );
		echo "<time datetime='{$publication_date}' property='datePublished'>{$publication_year}</time>";
	}
}

function pubwp_print_doi( ) {
	$prefix = '_pubwp_common_';
	if ( empty( rwmb_meta("{$prefix}doi", 'type = date') ) ) {
		return; # no Doi, no problem
	} else {
		$doi = esc_attr( rwmb_meta( "{$prefix}doi", 'type = text' ) );
		echo "DOI: <a property='sameAs' href='http://dx.doi.org/{$doi}'>{$doi}</a>";
	}
}
