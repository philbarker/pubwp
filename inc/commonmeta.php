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
	$id = '_pubwp_common_author_person'; # field id of authors
	$type = 'type = post';               # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		//not much we can do with no authors -- shouldn't happen!
		echo('What, no authors?'); //for debug only
		return;
	} else {
		$authors = rwmb_meta($id, $type);
		$len = count($authors);
		$i = 0;
		echo "By: ";
		foreach ($authors as $author) {
			$i = $i+1;
			echo '<span property="author" typeof="person">';
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
	$id = '_pubwp_common_date_published'; # field id of pub date
	$type = 'type = date';                # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		//not much we can do with no authors -- shouldn't happen!
		echo('What, no publication date?'); //for debug only
		return;
	} else {
		$publication_date = rwmb_meta($id, $type);
		$publication_year = esc_html( explode( '-', $publication_date )[0] );
		$publication_date = esc_html( $publication_date );
		echo "<time datetime='{$publication_date}' property='datePublished'>{$publication_year}</time>";
	}
}

function pubwp_print_doi( ) {
	$id = '_pubwp_common_doi';  # field id of doi
	$type = 'type = url';       # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		return; # no Doi, no problem
	} else {
		$doi = esc_attr( rwmb_meta($id, $type) );
		echo "DOI: <a property='sameAs' href='http://dx.doi.org/{$doi}'>{$doi}</a>";
	}
}

function pubwp_print_uri( $br=False ) {
	$id = '_pubwp_common_uri'; # field id of uri
	$type = 'type = url';       # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		return; # no URL, no problem (local copy only)
	} else {
		$uri_arr = rwmb_meta( $id, $type );
		foreach ($uri_arr as $uri) {
			echo "URL: <a property='url' href='{$uri}'>{$uri}</a> ";
			if ($br)
				echo "<br />";
		}
	}
}

function pubwp_print_abstract( ) {
	$id = '_pubwp_common_abstract'; # field id of abstract
	$type = 'type = WYSIWYG';       # type of field
	if ( empty( rwmb_meta( $id, $type ) ) ) {
		echo "Abstract unavailable";
		return;
	} else {
		echo "<div property='description'>".rwmb_meta( $id, $type )."</div>";
	}
}

function pubwp_print_local_info( ) {
	$lc_file_id = '_pubwp_common_local_copy';       # field id of local copy
	$lc_file_type = 'type = file_upload';           # type of field
	$lc_licence_id = '_pubwp_common_local_licence'; # field id of licence
	$lc_licence_type = 'type = text';               # type of field
	$lc_version_id = '_pubwp_common_local_version'; # field id of version
	$lc_version_type = 'type = text';               # type of field
	if ( empty( rwmb_meta( $lc_file_id, $lc_file_type) ) ) {
		echo( 'No local copy available' );
		return; # No local copy, no problem;
	} else {
		$files = rwmb_meta( $lc_file_id, $lc_file_type);
		foreach ($files as $file) {
			echo"<p property='workExample' typeOf='CreativeWork'>Local copy: ";
			if ( !empty( $file['title'] ) ) {
				$file_name = esc_html( $file['title'] );
			} elseif  ( !empty( $file['name'] ) ){
				$file_name = esc_html( $file['name'] );
			} else {
				$file_name = 'local copy';
			}
			$file_url = esc_url( $file['url']);
			echo "<a href='{$file_url}'>{$file_name}</a><br>";
			# currently not efficient to calc following info for each file as it is same
			# however looking forward need some way of giving diffent versions & licences
			if ( ! empty( rwmb_meta( $lc_licence_id, $lc_licence_type) ) ) {
				$licence = esc_html( rwmb_meta( $lc_licence_id, $lc_licence_type) );
				echo "licence: <span property='license'>{$licence}</span><br>";
			}
			if ( ! empty( rwmb_meta( $lc_version_id, $lc_version_type) ) ) {
				$version = esc_html( rwmb_meta( $lc_version_id, $lc_version_type) );
				echo "version: <span property='version'>{$version}</span><br>";
			}
			echo "</p>";
		}
	}
}

function pubwp_print_peer_reviewed( ) {
	$id = '_pubwp_common_peer_reviewed'; # field id of peer reviewed
	$type = 'type = radio';       # type of field	
	if ( ! empty( rwmb_meta( $id, $type) ) ) {
		$peer_reviewed = rwmb_meta( $id, $type);
		echo "Peer reviewed? {$peer_reviewed}.";
	}
}
