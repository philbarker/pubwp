<?php

/**
 * create a custom post type for journal paper & register meta boxes for paper metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

// create a custom post type for papers & register meta boxes for paper metadata
// see https://codex.wordpress.org/Function_Reference/register_post_type
// hook it up to init so that it gets called good and early

add_action( 'init', 'pubwp_create_paper_type' );
function pubwp_create_paper_type() {
	register_post_type( 'pubwp_paper',
		array(
			'labels' => array(
				'name' => __( 'Papers', 'pubwp' ),
				'singular_name' => __( 'Papers', 'pubwp' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'publications'),
			'supports' => array('title' ,'revisions', 'thumbnail' ),
			'menu_icon' => 'dashicons-media-text',
			'pubwp_type' => 'publication'
		)
	);
}

// commonmeta.php provides a metabox for pubwp_paper which includes
// url, author, publication_date

// Registering meta boxes for paper-specific properties
// More info @ http://metabox.io/docs/registering-meta-boxes/
add_filter( 'rwmb_meta_boxes', 'pubwp_register_paper_meta_boxes' );
function pubwp_register_paper_meta_boxes( $meta_boxes ) {
	// @param array $meta_boxes List of meta boxes
	// @return array
	$prefix = '_pubwp_paper_';  // prefix of meta keys keys hidden


	$meta_boxes[] = array(
		'id'         => 'pubwp_paper_info',  // Meta box id
		// Meta box title - Will appear at the drag and drop handle bar. Required.
		'title'      => __( 'Journal paper information', 'pubwp' ),
		'post_types' => array( 'pubwp_paper' ),// Post types that have this metabox
		'context'    => 'normal',             // Where the meta box appear
		'priority'   => 'low',               // Order of meta box
		'autosave'   => true,                 // Auto save

		// List of meta fields
		'fields'     => array(
			// Journal title as TEXT
			array(
				'name'  => __( 'Journal title', 'pubwp' ),
				'id'    => "{$prefix}journal_title",
				'type'  => 'text'
			),
			// Journal vol number as NUMBER
			array(
				'name'  => __( 'Journal volume', 'pubwp' ),
				'id'    => "{$prefix}journal_volumen",
				'type'  => 'number'
			),
			// Journal issue number as NUMBER
			array(
				'name'  => __( 'Journal issue', 'pubwp' ),
				'id'    => "{$prefix}journal_issue",
				'type'  => 'number'
			),
			// Journal page from as NUMBER
			array(
				'name'  => __( 'Journal page number, from', 'pubwp' ),
				'id'    => "{$prefix}journal_page_from",
				'type'  => 'number'
			),
			// Journal page to as NUMBER
			array(
				'name'  => __( 'Journal page number, to', 'pubwp' ),
				'id'    => "{$prefix}journal_page_to",
				'type'  => 'number'
			)
		)
	);
	return $meta_boxes;
}


/***
 * prints the details of a journal issue that a paper is part of
 **/
function pubwp_print_journal_issue_details( ) {
	$prefix = '_pubwp_paper_';
	$text_args = array('type' => 'text');
	$num_args = array('type' => 'number');
	$ja = rwmb_meta("{$prefix}journal_title", $text_args ); # Journal name
	$is = rwmb_meta("{$prefix}journal_issue", $text_args); # Issue number 
	$vl = rwmb_meta("{$prefix}journal_volumen", $num_args); # Volume number
	$sp = rwmb_meta("{$prefix}journal_page_from", $num_args); # Start page
	$ep = rwmb_meta("{$prefix}journal_page_to", $num_args); # End page
	
	if ( empty( $ja ) ) {
		$ja = false;
	} else {
		$ja = esc_html( $ja );
	}
	if ( empty( $is ) ) {
		$is = false;
	} else {
		$is = esc_html( $is );
	}
	if ( empty( $vl ) ) {
		$vl = false;
	} else {
		$vl = esc_html( $vl );
	}
	if ( empty( $sp ) ) {
		$sp = false;
	} else {
		$sp = esc_html( $sp );
	}
	if ( empty( $ep ) ) {
		$ep = false;
	} else {
		$ep = esc_html( $ep );
	}
		
	if ($ja) {
		echo "Ref: <span property='isPartOf' typeof='PublicationIssue'>
				<span property='name'>{$ja}</span>, ";
		if ($vl)
			echo "vol. <span property='volumeNumber'>{$vl}</span> ";
		if ($is)
			echo "iss. <span property='issueNumber'>{$is}</span>";
		if ($sp && $ep) {
			echo " pp. <span property='pageStart'>{$sp}</span>
					- <span property='pageEnd'>{$ep}</span>";
		} elseif ($sp) {
			echo " p. <span property='pageStart'>{$sp}</span>";
		}
		echo ".</span>";
	} else {
		//not much we can do with no journal name -- shouldn't happen!
		echo('No journal data.'); //for debug only
	}
}

function pubwp_journal_info( $post ) {
	$prefix = '_pubwp_paper_';
	$text_args = array('type' => 'text');
	$num_args = array('type' => 'number');
	$ja = rwmb_meta("{$prefix}journal_title", $text_args, $post->ID ); # Journal name
	$is = rwmb_meta("{$prefix}journal_issue", $text_args, $post->ID ); # Issue number 
	$vl = rwmb_meta("{$prefix}journal_volumen", $num_args, $post->ID ); # Volume number
	$sp = rwmb_meta("{$prefix}journal_page_from", $num_args, $post->ID ); # Start page
	$ep = rwmb_meta("{$prefix}journal_page_to", $num_args, $post->ID ); # End page
	$info = '';
	
	if ( empty( $ja ) ) {
		$ja = false;
	} else {
		$ja = esc_html( $ja );
	}
	if ( empty( $is ) ) {
		$is = false;
	} else {
		$is = esc_html( $is );
	}
	if ( empty( $vl ) ) {
		$vl = false;
	} else {
		$vl = esc_html( $vl );
	}
	if ( empty( $sp ) ) {
		$sp = false;
	} else {
		$sp = esc_html( $sp );
	}
	if ( empty( $ep ) ) {
		$ep = false;
	} else {
		$ep = esc_html( $ep );
	}

	if ($ja) {
		$info = "<span class='pubwp-ref-journal'>".$ja."</span>";
		if ($vl)
			$info = $info.', '."<span class='pubwp-ref-vol'>".$vl."</span>";
		if ($is)
			$info = $info.'('.$is.')';
		if ($sp && $ep) {
			$info = $info.", pp. {$sp}-{$ep}";
		} elseif ($sp) {
			$info = $info.", p. {$sp}";
		}
		return $info;
	} else {
		//not much we can do with no journal name -- shouldn't happen!
		return false; //for debug only
	}
}

