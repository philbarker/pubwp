<?php 
/**
 * create a custom taxonomy for subjects
 **
 * see https://codex.wordpress.org/Taxonomies for example on which this is based
 **/

defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

add_action( 'init', 'pubwp_subject_init' );
function pubwp_subject_init() {
	// create taxonomy called pubwp_subject
	register_taxonomy(
		'subjects',
		array(  'pubwp_book',  // Post types that have subject taxonomy
		        'pubwp_report',
		        'pubwp_presentation',
		        'pubwp_chapter',
			'pubwp_paper' ),
		array(
			'label'   => __( 'Subjects', 'pubwp' ),
			'rewrite' => array( 'slug' => 'subject' ),
		)
	);
}
 
