<?php

/**
 * create a custom post type for book & register meta boxes for book metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

// create a custom post type for books & register meta boxes for book metadata
// see https://codex.wordpress.org/Function_Reference/register_post_type
// hook it up to init so that it gets called good and early

add_action( 'init', 'pubwp_create_book_type' );
function pubwp_create_book_type() {
	register_post_type( 'pubwp_book',
		array(
			'labels' => array(
				'name' => __( 'Books', 'pubwp' ),
				'singular_name' => __( 'Book', 'pubwp' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'books'), 
			'supports' => array('title' ,'revisions', 'thumbnails'),
			'menu_icon' => 'dashicons-book-alt',
			'query_var' => 'publication'
		)
	);
}

// commonmeta.php provides a metabox for pubwp_book which includes
// url, author, publication_date

// Registering meta boxes for book-specific properties
// More info @ http://metabox.io/docs/registering-meta-boxes/
add_filter( 'rwmb_meta_boxes', 'pubwp_register_book_meta_boxes' );
function pubwp_register_book_meta_boxes( $meta_boxes ) {
	// @param array $meta_boxes List of meta boxes
	// @return array
	$prefix = '_pubwp_book_';  // prefix of meta keys keys hidden


	$meta_boxes[] = array(
		'id'		 => 'pubwp_book_info',  // Meta box id
		// Meta box title - Will appear at the drag and drop handle bar. Required.
		'title'	  => __( 'Book information', 'pubwp' ),
		'post_types' => array( 'pubwp_book' ),// Post types that have this metabox
		'context'	=> 'normal',			  // Where the meta box appear
		'priority'   => 'low',			      // Order of meta box
		'autosave'   => true,				  // Auto save

		// List of meta fields
		'fields'	 => array(
			// date published, as date picker
			array(
				'name'        => __( 'Publisher', 'pubwp' ),
				'id'          => "{$prefix}publisher",
				'type'		  => 'post',
				'post_type'   => 'pubwp_organization',
				'field_type'  => 'select_advanced',
				'placeholder' => __( 'Select a publisher', 'pubwp' ),
			),
			array(
				'name'  => __( 'ISBN', 'pubwp' ),
				'id'    => "{$prefix}isbn",
				'desc'  => __( 'ISBN for the book (you may repeat for different formats but cannot specify which goes with which format, sorry)', 'pubwp' ),
				'type'  => 'text',
				'clone' => true
			)
		)
	);
	return $meta_boxes;
}

function pubwp_print_isbn( $br=False ) {
	$id = '_pubwp_book_isbn'; # field id of ISBN
	$args = array( 'type' => 'text');       # type of field
	$isbns = rwmb_meta($id, $args);
	if ( empty( $isbns ) ) {
		return; # no isbn, no problem
	} else {
		foreach ($isbns as $isbn) {
			$isbn = esc_html( $isbn );
			echo "ISBN: <span property='isbn'>{$isbn}</span>";
			if ($br)
				echo '<br />';
		}
	}
}

function pubwp_isbns( $post ) {
	$id = '_pubwp_book_isbn'; # field id of ISBN
	$args = array( 'type' => 'text'); # type of field
	$isbns = rwmb_meta($id, $args, $post->ID);
	if ( empty( $isbns ) ) {
		return false; # no isbn, no problem
	} else {
		return $isbns;
	}
}

function pubwp_print_publisher( ) {
	$id = '_pubwp_book_publisher'; # field id of authors
	$args = array('type' => 'post');  # type of field
	$publisher = rwmb_meta($id, $args);
	if ( empty( $publisher ) ) {
		return; # no publisher info, no problem
	} else {
		echo "Published by: <span property='publisher' typeof='Organization'>";
	 	pubwp_print_organization_info( $publisher );		
		echo "</span>";
	}
 }
 
function pubwp_publisher( $post ) {
	$id = '_pubwp_book_publisher'; # field id of authors
	$args = array('type' => 'post');  # type of field
	$publisher = rwmb_meta($id, $args, $post->ID);
	if ( empty( $publisher )) {
		return false; # no publisher info, no problem
	} else {
	 	return pubwp_organization_info( $publisher );
	}
 }
 
