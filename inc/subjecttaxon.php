<?php 
/**
 * create a custom taxonomy for subjects
 **
 * see https://codex.wordpress.org/Taxonomies for example on which this is based
 **/

add_action( 'init', 'pubwp_subject_init' );
function pubwp_subject_init() {
	// create taxonomy called pubwp_subject
	register_taxonomy(
		'subjects',
		'post',
		array(
			'label'   => __( 'Subjects', 'pubwp' ),
			'rewrite' => array( 'slug' => 'subject' ),
		)
	);
}
 
