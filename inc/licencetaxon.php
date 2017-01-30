<?php 
/**
 * create a custom taxonomy for licences
 **
 * see https://codex.wordpress.org/Taxonomies for example on which this is based
 **/

add_action( 'init', 'pubwp_licence_init' );
function pubwp_licence_init() {
	// create taxonomy called pubwp_subject
	register_taxonomy(
		'licences',
		array(  'pubwp_book',  // Post types that have subject taxonomy
		        'pubwp_report',
		        'pubwp_presentation',
		        'pubwp_chapter',
			'pubwp_paper' ),
		array(
			'label'   => __( 'Licences', 'pubwp' ),
			'rewrite' => array( 'slug' => 'licence' ),
		)
	);
}

add_action( 'after_setup_theme', 'pubwp_add_licences' );
function pubwp_licence_init() {
	//add various essential licences
	wp_instert_term ( 'CC:BY',       'licences', 
			array( 'description'=> 'Creative Commons Attribution',
		);
	wp_instert_term ( 'CC:0',        'licences',  
			array( 'description'=> 'Creative Commons No rights reserved',
		);
	wp_instert_term ( 'CC:BY-SA',    'licences'  
			array( 'description'=> 'Creative Commons Attribution and Share Alike',
		);
	wp_instert_term ( 'CC:BY-NC',    'licences',  
			array( 'description'=> 'Creative Commons Attribution and No Commercial use',
		);
	wp_instert_term ( 'CC:BY-ND',    'licences',  
			array( 'description'=> 'Creative Commons Attribution and No Derivatives',
		);
	wp_instert_term ( 'CC:BY-NC-SA', 'licences',  
			array( 'description'=> 'Creative Commons Attribution, non-commercial and no derivatives',
		);
	wp_instert_term ( 'CC:BY-NC-ND', 'licences',  
			array( 'description'=> 'Creative Commons Attribution, non-commercial and share alike',
		);
	wp_instert_term ( 'Reserved',    'licences',  
			array( 'description'=> 'All rights reserved',
		);
}
