<?php 
/**
 * create a custom taxonomy for licences
 **
 * see https://codex.wordpress.org/Taxonomies for example on which this is based
 **/

defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

add_action( 'init', 'pubwp_licence_init' );
function pubwp_licence_init() {
	// create taxonomy called pubwp_licence
	register_taxonomy(
		'licences',
		array(
			'pubwp_book', // Post types that have licence taxonomy
			'pubwp_report',
			'pubwp_presentation',
			'pubwp_chapter',
			'pubwp_paper'
		),
		array(
			'label'   => __( 'Licences', 'pubwp' ),
			'rewrite' => array( 'slug' => 'licence' )
		)
	);
}
