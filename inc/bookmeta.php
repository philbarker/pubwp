<?php

/**
 * create a custom post type for person & register meta boxes for person metadata
 **
 * requires: meta box plugin http://metabox.io/
 *
 **/

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
      'rewrite' => array('slug' => 'book'),
      'supports' => array('title' ,'revisions' )
    )
  );
}

// commonmeta.php provides a metabox for pubwp_book which includes
// url, author, publication_date

