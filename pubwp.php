<?php
/*
Plugin Name: PubWP
Plugin URI: https://github.com/philbarker/pubwp
Version: 0.0
Author: Phil Barker
Author URI: http://people.pjjk.net/phil
Description: A WordPress plugin to set up custom post types and associated metadata for scholarly publications. Requires the Meta Box plugin to be activated, see https://metabox.io/ .
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

/* check that meta-box plugin is installed */
add_action( 'admin_init', 'child_plugin_has_parent_plugin' );
function child_plugin_has_parent_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'meta-box/meta-box.php' ) ) {
        add_action( 'admin_notices', 'child_plugin_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) ); 
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function child_plugin_notice(){
    ?><div class="error"><p>Sorry, but SemWP Plugin requires the <a href="https://metabox.io/">meta box plugin</a> to be installed and active.</p></div><?php
}

add_action( 'admin_head', 'edit_screen_title' );
function edit_screen_title() {
  global $title, $current_screen;
  $post_type = $current_screen->post_type;
  if ( $post_type && ( $post_type != 'post' ) ) {
    $post_type_label = get_post_type_object( $post_type )->labels->singular_name;
    $title = 'Edit ' . $post_type_label .' data ';
  }
}

function pubwp_citation( $post ) {
	$url = esc_url( get_permalink( $post->ID ) );
	$title = $post->post_title;
	$linked_title = "<a href='{$url}'>{$title}</a>";
	$author_names = pubwp_author_names( $post );
	$year = pubwp_year( $post );
	$citation = $author_names.' ('.$year.') '.$linked_title.'. ';
	if ('pubwp_book' == $post->post_type) {
		$citation = $citation.' '.pubwp_publisher( $post );
	} elseif ('pubwp_report' == $post->post_type) {
		$citation = $citation.' ('.pubwp_report_info( $post ).').';
	} elseif ('pubwp_presentation' == $post->post_type) {
		$citation = $citation.' '.pubwp_presentation_info( $post ).'.';
	}
	if ( pubwp_linked_doi( $post ) ) {
		$citation = $citation.'<br />DOI: '.pubwp_linked_doi( $post );
	}
	if ( pubwp_linked_uri( $post ) ) {
		foreach ( pubwp_linked_uri( $post ) as $linked_url ) {
			$citation = $citation.'<br />URI: '.$linked_url;
		}
	}
	return $citation;

}

function pubwp_by_type ( $atts ) {
	$query = array();
	$args = array('_builtin' => False,
				  'exclude_from_search' => False);
	$custom_post_types = get_post_types( $args, 'objects', 'and' );
	
	$query = array( 'posts_per_page' => -1 );
	foreach ($custom_post_types as $custom_post_type) {
		echo "<h4>{$custom_post_type->label}</h4>";
		$query['post_type'] = $custom_post_type->name;
		$posts = get_posts( $query );
		echo '<p>';
		foreach ($posts as $post) {
			$url = esc_url( get_permalink( $post->ID ) );
			$title = $post->post_title;
			echo pubwp_citation( $post );
		}
		echo '</p>';
	}
}
add_shortcode( 'pubs-by-type', 'pubwp_by_type' );

$pubwp_dir = plugin_dir_path( __FILE__ );
include_once( $pubwp_dir.'inc/personmeta.php' );
include_once( $pubwp_dir.'inc/organizationmeta.php' );
include_once( $pubwp_dir.'inc/bookmeta.php' );
include_once( $pubwp_dir.'inc/chaptermeta.php' );
include_once( $pubwp_dir.'inc/reportmeta.php' );
include_once( $pubwp_dir.'inc/presentationmeta.php' );
include_once( $pubwp_dir.'inc/papermeta.php' );
include_once( $pubwp_dir.'inc/subjecttaxon.php' );
include_once( $pubwp_dir.'inc/licencetaxon.php' );
include_once( $pubwp_dir.'inc/commonmeta.php' );

