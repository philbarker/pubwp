<?php

/**
 * create a custom post type for chapter & register meta boxes for chapter metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

// create a custom post type for chapters & register meta boxes for chapter metadata
// see https://codex.wordpress.org/Function_Reference/register_post_type
// hook it up to init so that it gets called good and early

add_action( 'init', 'pubwp_create_chapter_type' );
function pubwp_create_chapter_type() {
	register_post_type( 'pubwp_chapter',
		array(
			'labels' => array(
				'name' => __( 'Chapters', 'pubwp' ),
				'singular_name' => __( 'Chapters', 'pubwp' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'chapters'),
			'supports' => array('title' ,'revisions', 'thumbnail' ),
			'menu_icon' => plugins_url( 'pubwp' ) . '/inc/icons/book-chap.svg',
			'query_var' => 'publication'
			)			
		);
}

// commonmeta.php provides a metabox for pubwp_chapter which includes
// url, author, publication_date

// Registering meta boxes for chapter-specific properties
// More info @ http://metabox.io/docs/registering-meta-boxes/
add_filter( 'rwmb_meta_boxes', 'pubwp_register_chapter_meta_boxes' );
function pubwp_register_chapter_meta_boxes( $meta_boxes ) {
	// @param array $meta_boxes List of meta boxes
	// @return array
	$prefix = '_pubwp_chapter_';  // prefix of meta keys keys hidden


	$meta_boxes[] = array(
		'id'		 => 'pubwp_chapter_info',  // Meta box id
		// Meta box title - Will appear at the drag and drop handle bar. Required.
		'title'		 => __( 'Information about the book containing this chapter', 'pubwp' ),
		'post_types' => array( 'pubwp_chapter' ),// Post types that have this metabox
		'context'	 => 'normal',             // Where the meta box appear
		'priority'   => 'low',               // Order of meta box
		'autosave'   => true,                 // Auto save

		// List of meta fields
		'fields'     => array(
			// title of book, as text
			array(
				'name'  => __( 'Title', 'pubwp' ),
				'id'    => "{$prefix}title",
				'desc'  => __( 'Title of the book of which this chapter is part', 'pubwp' ),
				'type'  => 'text',
				'clone' => false
			),
			// Editor(s), of book of type Person
			array(
				'name'  => __( 'Editor (link)', 'pubwp' ),
				'id'    => "{$prefix}editor_person",
				'desc'  => __( 'Link to Person information for editor', 'pubwp' ),
				'type'  => 'post',
				'post_type'   => 'pubwp_person',
				'field_type'  => 'select_advanced',
				'placeholder' => __( 'Select an editor', 'pubwp' ),
				'clone' => true
			),
			array(
				'name'  => __( 'Publisher', 'pubwp' ),
				'id'    => "{$prefix}publisher",
				'type'  => 'post',
				'post_type'   => 'pubwp_organization',
				'field_type'  => 'select_advanced',
				'placeholder' => __( 'Publisher of the book', 'pubwp' ),
			),
			array(
				'name'  => __( 'ISBN', 'pubwp' ),
				'id'    => "{$prefix}isbn",
				'desc'  => __( 'ISBN for the book (you may repeat for different formats but cannot specify which goes with which format, sorry)', 'pubwp' ),
				'type'  => 'text',
				'clone' => true,
			),			
			array(
				'name'  => __( 'DOI', 'pubwp' ),
				'id'    => "{$prefix}doi",
				'desc'  => __( 'DOI for the book (you may repeat for different formats but cannot specify which goes with which format, sorry)', 'pubwp' ),
				'type'  => 'text',
				'clone' => false,
			),
			array(
				'name'  => __( 'URL', 'pubwp' ),
				'id'    => "{$prefix}url",
				'desc'  => __( 'URL for the book (you may repeat for different formats but cannot specify which goes with which format, sorry)', 'pubwp' ),
				'type'  => 'url',
				'clone' => true,
			)
		)
	);
	return $meta_boxes;
}


function pubwp_print_bookchap_title( ) {
	$id = '_pubwp_chapter_title'; # field id of abstract
	$args = array('type' => 'text'); # type of field
	$chapter_title = rwmb_meta( $id, $args );
	if ( empty( $chapter_title ) ) {
		echo "untitled";
		return;
	} else {
		$chapter_title = esc_html( $chapter_title );
		echo "<span property='name'>".$chapter_title."</span>";
	}
}

function pubwp_bookchap_title( $post ) {
	$id = '_pubwp_chapter_title'; # field id of abstract
	$args = array('type' => 'text'); # type of field
	$title = rwmb_meta( $id, $args, $post->ID );
	if ( empty( $title ) ) {
		return "untitled";
	} else {
		return esc_html($title);
	}

}

function pubwp_print_bookchap_editors( ) {
	$id = '_pubwp_chapter_editor_person'; # field id of authors
	$type = 'type = post';               # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		//not much we can do with no authors -- shouldn't happen!
		echo('What, no editors?'); //for debug only
		return;
	} else {
		$editors = rwmb_meta($id, $type);
		$len = count($editors);
		$i = 0;
		foreach ($editors as $editor) {
			$i = $i+1;
			echo '<span property="creator" typeof="person">';
			pubwp_print_person_fullname( $editor );
			echo '</span>';
			if ($i < ($len - 1) ) {
				echo ', ';
			} elseif ($i == ($len - 1) ) {
				echo ' and ';
			}
		}
	}
}

function pubwp_bookchap_editors( $post ) {
	$id = '_pubwp_chapter_editor_person'; # field id of authors
	$type = 'type = post';               # type of field
	$editor_arr = array( );
	$editor_arr = rwmb_meta($id, $type, $post->ID);
	$editors = '';
	if ( empty( $editor_arr ) ) {
		//not much we can do with no authors -- shouldn't happen!
		echo('What, no editors?'); //for debug only
		return false;
	} else {
		$len = count($editor_arr);
		$i = 0;
		foreach ($editor_arr as $editor) {
			$i = $i+1;
			$editors = $editors.pubwp_person_fullname( $editor );
			if ($i < ($len - 1) ) {
				$editors = $editors.', ';
			} elseif ($i == ($len - 1) ) {
				$editors = $editors.' and ';
			}
		}
		return esc_html( $editors );
	}
}

function pubwp_print_bookchap_publisher( ) {
	$id = '_pubwp_chapter_publisher'; # field id of authors
	$type = 'type = post';               # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		return; # no publisher info, no problem
	} else {
		$publisher = rwmb_meta($id, $type);
		echo "Published by: <span property='publisher' typeof='Organization'>";
	 	pubwp_print_organization_info( $publisher );
		echo "</span>";
	}
 }

function pubwp_bookchap_publisher( $post ) {
	$id = '_pubwp_chapter_publisher'; # field id of authors
	$type = 'type = post';               # type of field
	$publisher = rwmb_meta($id, $type, $post->ID);
	if ( empty( $publisher ) ) {
		return false; # no publisher info, no problem
	} else {
	 	return pubwp_organization_info( $publisher );
	}
 }
 
function pubwp_print_bookchap_isbn( $br=False ) {
	$id = '_pubwp_chapter_isbn'; # field id of ISBN
	$type = 'type = text';       # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		return; # no isbn, no problem
	} else {
		$isbns = rwmb_meta($id, $type);
		foreach ($isbns as $isbn) {
			$isbn = esc_html( $isbn );
			echo "ISBN: <span property='isbn'>{$isbn}</span>";
			if ($br)
				echo '</br>';
		}
	}
}

function pubwp_print_bookchap_doi( ) {
	$id = '_pubwp_chapter_doi';  # field id of doi
	$type = 'type = text';       # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		return; # no Doi, no problem
	} else {
		$doi = esc_attr( rwmb_meta($id, $type) );
		echo "DOI: <a property='sameAs' href='http://dx.doi.org/{$doi}'>{$doi}</a>";
	}
}

function pubwp_print_bookchap_url( $br=False ) {
	$id = '_pubwp_chapter_url'; # field id of uri
	$type = 'type = url';       # type of field
	if ( empty( rwmb_meta($id, $type) ) ) {
		return; # no URL, no problem (local copy only)
	} else {
		$uri_arr = rwmb_meta( $id, $type );
		foreach ($uri_arr as $uri) {
			esc_url( $uri );
			echo "URL: <a property='url' href='{$uri}'>{$uri}</a> ";
			if ($br)
				echo "<br />";
		}
	}
}

function pubwp_chapter_info( $post ) {
	$info = '';
	if ( pubwp_bookchap_editors( $post ) ) {
		$info = $info.pubwp_bookchap_editors( $post ).' (Eds.) ';
	}
	$info = $info.pubwp_bookchap_title( $post ).'. ';
	if ( pubwp_bookchap_publisher( $post ) ) {
		$info = $info.pubwp_bookchap_publisher( $post );
	}
	return esc_html( $info );
}


