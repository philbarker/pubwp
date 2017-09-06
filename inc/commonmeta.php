<?php

/**
 * register meta boxes for metadata that are common to several publication types
 **
 * provides a metabox for pubwp_book which includes
 * _pubwp_common_ title, url, author, publication_date
 * requires: meta box plugin http://metabox.io/
 *
 **/

defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

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

// set the date of the post to the date of publication of resource.
add_filter( 'wp_insert_post_data', 'pubwp_modify_post_date', 99, 2 );
function pubwp_modify_post_date( $data, $postarr ) {
	$args = array('_builtin' => False);
	$publication_day = '01';
	$publication_month = '01';
	$publication_year = '1970';
	$publication_date = '1970-01-01';
	$custom_post_types = get_post_types( $args, 'names', 'and' );
	if ( isset($_POST['post_type'])  && (in_array( $_POST['post_type'], $custom_post_types))) {
		$id = '_pubwp_common_date_published'; # field id of pub date
		if (isset($_POST[$id]))
			$publication_date = $_POST[$id];
		if (!empty($publication_date) ) {
			$date_arr = explode( '-', $publication_date);
			if (count($date_arr) > 2) {
				$publication_day = $date_arr[2];
			} else {
				$publication_day = '01';
			}
			if (count($date_arr) > 1) {
				$publication_month = $date_arr[1];
			} else {
				$publication_month = '01';
			}
			if (count($date_arr) > 0) {
				$publication_year = $date_arr[0];
			} else {
				$publication_year = '1970';
			}
			$publication_date = "{$publication_year}-{$publication_month}-{$publication_day}";
			if ( wp_checkdate( $publication_month, $publication_day, $publication_year, $publication_date ) ) {
				$data['post_date'] = $publication_date;
			} else {
				$data['post_date'] = '1970-01-01';
			}
#		} else {
#				$data['post_date'] = '1970-01-01';
		}
	}
	return $data;
}

function pubwp_print_authors(  ) {
	$id = '_pubwp_common_author_person'; # field id of authors
	$args = array( 'type' => 'post' );   # type of field
	$authors = rwmb_meta($id, $args);
	if ( empty( $authors ) ) {
		//not much we can do with no authors -- shouldn't happen!
		echo('What, no authors?'); //for debug only
		return;
	} else {
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

function pubwp_author_names( $post ) {
	$id = '_pubwp_common_author_person'; # field id of authors
	$args = array('type' => 'post');               # type of field
	$authors = rwmb_meta($id, $args, $post_id = $post->ID);
	if ( empty( $authors ) ) {
		//not much we can do with no authors -- shouldn't happen!
		echo('What, no authors?'); //for debug only
		echo $post->name;
		return 'anon.';
	} else {
		$len = count($authors);
		$i = 0;
		$author_names = '';
		foreach ($authors as $author) {
			$i = $i+1;
			$author_names = $author_names.pubwp_person_fullname( $author );
			if ($i < ($len - 1) ) {
				$author_names = $author_names.', ';
			} elseif ($i == ($len - 1) ) {
				$author_names = $author_names.' and ';
			}
		}
	}
	return $author_names;
}

function pubwp_print_date_published( ) {
	$id = '_pubwp_common_date_published'; # field id of pub date
	$args = array('type' => 'date');      # type of field
	$publication_date = rwmb_meta($id, $args);
	if ( empty( $publication_date ) ) {
		//not much we can do with no authors -- shouldn't happen!
		echo('What, no publication date?'); //for debug only
		return;
	} else {
		$publication_year = esc_html( explode( '-', $publication_date )[0] );
		$publication_date = esc_attr( $publication_date );
		echo "<time datetime='{$publication_date}' property='datePublished'>{$publication_year}</time>";
	}
}

function pubwp_year( $post ) {
	$id = '_pubwp_common_date_published'; # field id of pub date
	$args = array('type' => 'date');      # type of field
	$publication_date = rwmb_meta($id, $args, $post_id = $post->ID );
	if ( empty( $publication_date ) ) {
		//not much we can do with no authors -- shouldn't happen!
		return 'n.d.';
	} else {
		return esc_html( explode( '-', $publication_date )[0] );
	}
}

function pubwp_print_doi( ) {
	$id = '_pubwp_common_doi';       # field id of doi
	$args = array('type' => 'text'); # type of field
	$doi = rwmb_meta($id, $args);
	if ( empty( $doi ) ) {
		return; # no Doi, no problem
	} else {
		$doi = esc_attr( $doi );
		echo "DOI: <a property='sameAs' href='http://dx.doi.org/{$doi}'>{$doi}</a>";
	}
}

function pubwp_linked_doi( $post) {
	$id = '_pubwp_common_doi';       # field id of doi
	$args = array('type' => 'text'); # type of field
	$doi = rwmb_meta($id, $args, $post->ID );
	if ( empty( $doi) ) {
		return false; # no Doi, no problem
	} else {
		$doi = esc_attr( $doi );
		return "<a href='http://dx.doi.org/{$doi}'>{$doi}</a>";
	}
}

function pubwp_print_uri( $br=False ) {
	$id = '_pubwp_common_uri'; # field id of uri
	$args = array('type' => 'url'); # type of field
	$uri_arr = rwmb_meta($id, $args);
	if ( empty( $uri_arr ) ) {
		return; # no URL, no problem (local copy only)
	} else {
		foreach ($uri_arr as $uri) {
			$uri = esc_url( $uri );
			echo "URL: <a property='url' href='{$uri}'>{$uri}</a> ";
			if ($br)
				echo "<br />";
		}
	}
}

function pubwp_linked_uri( $post ) {
	$id = '_pubwp_common_uri'; # field id of uri
	$args = array('type' => 'url'); # type of field
	$uri_arr = rwmb_meta( $id, $args, $post->ID );
	if ( empty( $uri_arr ) ) {
		return false; # no URL, no problem (local copy only)
	} else {
		$linked_uri_arr = array();
		foreach ($uri_arr as $uri) {
			$uri = esc_url( $uri );
			$linked_uri = "<a href='{$uri}'>{$uri}</a>";
			$linked_uri_arr[] = $linked_uri;
		}
	return $linked_uri_arr;
	}
}


function pubwp_print_abstract( ) {
	$id = '_pubwp_common_abstract';      # field id of abstract
	$args = array( 'type' => 'WYSIWYG'); # type of field
	$abstract = rwmb_meta( $id, $args );
	if ( empty( $abstract ) ) {
		echo "<p>Abstract unavailable</p>";
		return;
	} else {
		echo "<div property='description'>".$abstract."</div>";
	}
}

function pubwp_print_local_info( ) {
	$lc_file_id = '_pubwp_common_local_copy';       # field id of local copy
	$lc_file_args = array('type' => 'file_upload');           # type of field
	$lc_licence_id = '_pubwp_common_local_licence'; # field id of licence
	$lc_licence_args = array('type' => 'text');               # type of field
	$lc_version_id = '_pubwp_common_local_version'; # field id of version
	$lc_version_args = array('type' => 'text');               # type of field
	$files = rwmb_meta( $lc_file_id, $lc_file_args);
	if ( empty( $files ) ) {
		echo( 'No local copy available' );
		return; # No local copy, no problem;
	} else {
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
			echo "<a property ='url' href='{$file_url}'>{$file_name}</a><br>";
			# currently not efficient to calc following info for each file as it is same
			# however looking forward need some way of giving diffent versions & licences
			$licence = rwmb_meta( $lc_licence_id, $lc_licence_args);
			if ( ! empty( $licence ) ) {
				$licence = esc_html( $licence );
				echo "licence: <span property='license'>{$licence}</span><br>";
			}
			$version = rwmb_meta( $lc_version_id, $lc_version_args);
			if ( ! empty( $version ) ) {
				$version = esc_html( $version );
				echo "version: <span property='version'>{$version}</span><br>";
			}
			echo "</p>";
		}
	}
}

function pubwp_print_peer_reviewed( ) {
	$id = '_pubwp_common_peer_reviewed'; # field id of peer reviewed
	$args = array('type' => 'radio');       # type of field	
	$peer_reviewed = rwmb_meta( $id, $args);
	if ( ! empty( $peer_reviewed ) )  {
		$peer_reviewed = esc_html( $peer_reviewed );
		echo "Peer reviewed? {$peer_reviewed}.";
	}
}
