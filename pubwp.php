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

