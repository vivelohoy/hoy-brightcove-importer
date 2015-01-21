<?php
/*
 * Plugin Name: Hoy Brightcove Importer Plugin
 * Plugin URI: http://vivelohoy.com/
 * Description: Imports Brightcove videos as individual posts.
 * Version: 1.0.0
 * Author: Nick Bennett
 * Author URI: http://twitter.com/yoyoohrho
 * License: MIT
 */

/*

PPPPPP  RRRRRR  EEEEEEE        RRRRRR  EEEEEEE  QQQQQ
PP   PP RR   RR EE             RR   RR EE      QQ   QQ
PPPPPP  RRRRRR  EEEEE   _____  RRRRRR  EEEEE   QQ   QQ
PP      RR  RR  EE             RR  RR  EE      QQ  QQ
PP      RR   RR EEEEEEE        RR   RR EEEEEEE  QQQQ Q


*/

require_once( 'lib/class-tgm-plugin-activation.php' );

add_action( 'tgmpa_register', 'hoy_brightcove_importer_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function hoy_brightcove_importer_register_required_plugins() {

    $plugins = array(

        array(
            'name'               => 'WP Brightcove Shortcode Plugin', // The plugin name.
            'slug'               => 'wp-brightcove-shortcode', // The plugin slug (typically the folder name).
            'source'             => 'https://github.com/vivelohoy/wp-brightcove-shortcode/archive/0.1.0.zip', // The plugin source.
            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            'external_url'       => 'https://github.com/vivelohoy/wp-brightcove-shortcode', // If set, overrides default API URL and points to an external URL.
        ),

    );

    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'tgmpa' ),
            'menu_title'                      => __( 'Install Plugins', 'tgmpa' ),
            'installing'                      => __( 'Installing Plugin: %s', 'tgmpa' ), // %s = plugin name.
            'oops'                            => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'tgmpa' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'tgmpa' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );

    tgmpa( $plugins, $config );

}

/*
 * Global variables
 */

$options = array();

$tags_to_categories = array(
        'chicago' => 'Chicago',
        'deportes' => 'Deportes',
        'entretenimiento' => 'Entretenimiento',
        'inmigracion' => 'Inmigración',
        'nacion y mundo' => 'Nación y Mundo',
        'salud y vida' => 'Salud y Vida',
        'tecnologia' => 'Tecnología',
        'documental' =>'Documental',
        'contenido humano' => 'Contenido Humano',
        'lo mas visto en la red' => 'Lo mas visto en la red'
    );

$default_ready_to_publish_tag = 'listo';

$brightcove_embed_defaults = array(
        'width'         => 853,
        'height'        => 480,
        'id'            => false,
        'player_id'     => 2027711527001,
        'player_key'    => 'AQ~~,AAAB2Ejp1kE~,qYgZ7QVyRmCflxEtsSSb7N6jXd3aEUNg'
    );

/*

This autoimport frequency is the keyword used by wp-cron to indicate how
frequently it should be run. This is one of a limited set of keywords:
hourly, twicedaily, and daily.

More information here:
https://developer.wordpress.org/plugins/cron/understanding-wp-cron-scheduling/

*/
$default_autoimport_frequency = 'hourly';


if( !defined( 'HOY_BRIGHTCOVE_IMPORTER_DIR' ) ) {
    define('HOY_BRIGHTCOVE_IMPORTER_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'HOY_BRIGHTCOVE_IMPORTER_URL' ) ) {
    define('HOY_BRIGHTCOVE_IMPORTER_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}


/*

IIIII NN   NN TTTTTTT EEEEEEE RRRRRR  FFFFFFF   AAA    CCCCC  EEEEEEE
 III  NNN  NN   TTT   EE      RR   RR FF       AAAAA  CC    C EE
 III  NN N NN   TTT   EEEEE   RRRRRR  FFFF    AA   AA CC      EEEEE
 III  NN  NNN   TTT   EE      RR  RR  FF      AAAAAAA CC    C EE
IIIII NN   NN   TTT   EEEEEEE RR   RR FF      AA   AA  CCCCC  EEEEEEE


*/

/*
 * Add a link to our plugin in the admin menu
 * under Settings > Hoy Brightcove Importer
 */

function hoy_brightcove_importer_menu() {

    add_options_page(
        'Hoy Brightcove Importer Plugin',
        'Hoy Brightcove Importer',
        'manage_options',
        'hoy-brightcove-importer',
        'hoy_brightcove_importer_options_page'
    );

}
add_action( 'admin_menu', 'hoy_brightcove_importer_menu' );


function hoy_brightcove_importer_options_page() {

    if( !current_user_can( 'manage_options') ) {
        wp_die( 'You do not have sufficient permission to access this page.' );
    }

    global $options;
    global $display_json;
    global $default_ready_to_publish_tag;
    global $default_autoimport_frequency;

    if( isset( $_POST['hoy_brightcove_importer_reset_options_form_submitted'] ) ) {
        $hidden_field = esc_html( $_POST['hoy_brightcove_importer_reset_options_form_submitted'] );

        if( $hidden_field == 'Y' ) {
            delete_option( 'hoy_brightcove_importer' );
        }
    }
    if( isset( $_POST['hoy_brightcove_importer_api_key_form_submitted'] ) ) {
        $hidden_field = esc_html( $_POST['hoy_brightcove_importer_api_key_form_submitted'] );

        if( $hidden_field == 'Y' ) {
            $hoy_brightcove_importer_api_key = esc_html( $_POST['hoy_brightcove_importer_api_key'] );

            $options['hoy_brightcove_importer_api_key'] = $hoy_brightcove_importer_api_key;

            update_option( 'hoy_brightcove_importer', $options );
        }
    } elseif( isset( $_POST['hoy_brightcove_importer_update_videos_form_submitted'] ) ) {
        $hidden_field = esc_html( $_POST['hoy_brightcove_importer_update_videos_form_submitted'] );

        if( $hidden_field == 'Y' ) {
            hoy_brightcove_importer_fetch_new_videos();
        }
    } elseif( isset( $_POST['hoy_brightcove_importer_create_posts_form_submitted'] ) ) {
        $hidden_field = esc_html( $_POST['hoy_brightcove_importer_create_posts_form_submitted'] );

        if( $hidden_field == 'Y' ) {
            hoy_brightcove_importer_import_new_videos();
        }
    }

    $options = get_option( 'hoy_brightcove_importer' );
    if( $options != '' ) {
        if( !array_key_exists( 'hoy_brightcove_importer_imported_videos', $options ) ) {
            $options['hoy_brightcove_importer_imported_videos'] = array();
            update_option( 'hoy_brightcove_importer', $options );
        }
        if( !array_key_exists( 'hoy_brightcove_importer_new_videos', $options ) ) {
            $options['hoy_brightcove_importer_new_videos'] = array();
            update_option( 'hoy_brightcove_importer', $options );
        }
        if( !array_key_exists( 'hoy_brightcove_importer_last_updated', $options ) ) {
            $options['hoy_brightcove_importer_last_updated'] = false;
            update_option( 'hoy_brightcove_importer', $options );
        }
        if( !array_key_exists( 'hoy_brightcove_importer_last_imported', $options ) ) {
            $options['hoy_brightcove_importer_last_imported'] = false;
            update_option( 'hoy_brightcove_importer', $options );
        }
        if( !array_key_exists( 'hoy_brightcove_importer_ready_tag', $options ) ||
            '' == $options['hoy_brightcove_importer_ready_tag'] ) {
            $options['hoy_brightcove_importer_ready_tag'] = $default_ready_to_publish_tag;
            update_option( 'hoy_brightcove_importer', $options );
        }

        $hoy_brightcove_importer_api_key = $options['hoy_brightcove_importer_api_key'];
        $hoy_brightcove_importer_imported_videos = $options['hoy_brightcove_importer_imported_videos'];
        $hoy_brightcove_importer_new_videos = $options['hoy_brightcove_importer_new_videos'];
        $hoy_brightcove_importer_last_updated = $options['hoy_brightcove_importer_last_updated'];
        $hoy_brightcove_importer_last_imported = $options['hoy_brightcove_importer_last_imported'];
        $hoy_brightcove_importer_ready_tag = $options['hoy_brightcove_importer_ready_tag'];
    }


    require( 'inc/options-page-wrapper.php' );

}

/*

BBBBB   RRRRRR  IIIII   GGGG  HH   HH TTTTTTT  CCCCC   OOOOO  VV     VV EEEEEEE
BB   B  RR   RR  III   GG  GG HH   HH   TTT   CC    C OO   OO VV     VV EE
BBBBBB  RRRRRR   III  GG      HHHHHHH   TTT   CC      OO   OO  VV   VV  EEEEE
BB   BB RR  RR   III  GG   GG HH   HH   TTT   CC    C OO   OO   VV VV   EE
BBBBBB  RR   RR IIIII  GGGGGG HH   HH   TTT    CCCCC   OOOO0     VVV    EEEEEEE


*/

function hoy_brightcove_importer_get_videos( $hoy_brightcove_importer_api_key ) {

    function get_paged_api_results( $hoy_brightcove_importer_api_key, $page_number ) {
        $options = get_option( 'hoy_brightcove_importer' );

        $query_string = http_build_query( array(
            'command' => 'search_videos',
            'token' => $hoy_brightcove_importer_api_key,
            'page_size' => 50,
            'page_number' => $page_number,
            'get_item_count' => 'true',
            'any' => 'tag:' . $options['hoy_brightcove_importer_ready_tag']
            ) );
        $json_feed_url = 'http://api.brightcove.com/services/library?' . $query_string;
        $args = array( 'timeout' => 120 );

        $json_feed = wp_remote_get( $json_feed_url, $args );

        $results = json_decode( $json_feed['body'] );

        $items = array();
        // We are given an array of objects, let's convert it into an array of arrays
        foreach( $results->{'items'} as $index => $item ) {
            $items[] = (array) $item;
        }

        return $items;

    }

    $all_videos = array();

    for( $page_number = 0; ; $page_number++ ) {
        $videos = get_paged_api_results( $hoy_brightcove_importer_api_key, $page_number );
        if( count($videos) == 0 ) {
            break;
        }
        $all_videos = array_merge( $all_videos, $videos );
    }

    return $all_videos;

}

function hoy_brightcove_importer_fetch_new_videos() {

    $options = get_option( 'hoy_brightcove_importer' );
    $api_key = $options['hoy_brightcove_importer_api_key'];
    $all_videos = hoy_brightcove_importer_get_videos( $api_key );

    $imported_videos = $options['hoy_brightcove_importer_imported_videos'];
    $imported_video_ids = array();
    $new_videos = array();
    // Generate list of video IDs that have already been turned into posts,
    // so we don't import something twice
    foreach( $imported_videos as $index => $imported_video ) {
        $video_id = $imported_video['video']['id'];
        $imported_video_ids[] = $video_id;
    }
    foreach( $all_videos as $index => $video ) {
        $video_id = $video['id'];
        // If the video hasn't previously been imported, queue it up
        if( !in_array( $video_id, $imported_video_ids ) ) {
            // This video has not yet been imported
            $new_videos[] = $video;
        }
    }

    $options['hoy_brightcove_importer_new_videos'] = $new_videos;
    $options['hoy_brightcove_importer_last_updated'] = time();

    update_option( 'hoy_brightcove_importer', $options );

}


/*

IIIII MM    MM PPPPPP   OOOOO  RRRRRR  TTTTTTT
 III  MMM  MMM PP   PP OO   OO RR   RR   TTT
 III  MM MM MM PPPPPP  OO   OO RRRRRR    TTT
 III  MM    MM PP      OO   OO RR  RR    TTT
IIIII MM    MM PP       OOOO0  RR   RR   TTT


*/

function hoy_brightcove_importer_attach_image_to_post( $image_url, $post_id ) {
    $upload_dir = wp_upload_dir();
    $response = wp_remote_get( $image_url );
    $image_data = $response['body'];
    $filename_chunks = explode( '?', basename( $image_url ) );
    $filename = $filename_chunks[0];

    if( wp_mkdir_p( $upload_dir['path'] ) ) {
        $file = $upload_dir['path'] . '/' . $filename;
    }
    else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }

    file_put_contents( $file, $image_data );

    $wp_filetype = wp_check_filetype( $filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    return $attach_id;
}


function hoy_brightcove_importer_import_video( $video ) {
    /*
    
    $video contains data on the video directly from the Brightcove Media API

    @returns: an array containing information about the new post, at least with an 'id' key with the value
        of the new post ID.

    */
    global $tags_to_categories;

    $options = get_option( 'hoy_brightcove_importer' );

    $defaults = array(
        'width'             => 853,
        'height'            => 480,
        'category'          => 'Video',
        'author_username'   => 'brightcove',
        'status'            => 'draft',
        'post_type'         => 'post',
        'format'            => 'video'
    );

    $video_id = $video['id'];

    $post_title = sprintf( 'Video: %1$s',
                            wp_strip_all_tags( $video['name'] ) );

    $post_excerpt = $video['shortDescription'];

    $post_content = sprintf( '[brightcove id="%1$s" width="%2$s" height="%3$s"]',
                $video_id,
                $defaults['width'],
                $defaults['height'] );

    $tags_input = $video['tags'];
    // Filter out the tag indicating the item is ready to publish
    $tags_input = array_merge( array_diff( $tags_input, array( $options['hoy_brightcove_importer_ready_tag'] ) ) );

    $video_cat = get_term_by( 'name', $defaults['category'], 'category' );
    $post_category = array( $video_cat->term_id );

/*
Inserting additional categories based on Brightcove tags. This is not how it works exactly, since
wp_insert_post expects the 'post_category' field to be an array of category IDs not category names.

http://codex.wordpress.org/Function_Reference/wp_insert_post
http://codex.wordpress.org/Function_Reference/wp_set_post_terms

    // Additional WordPress categories can be specified in the Brightcove tags
    foreach( $tags_input as $index => $tag ) {
        $tag_lowercase = strtolower( $tag );
        foreach( $tags_to_categories as $brightcove_tag => $wp_category ) {
            if( $tag == $brightcove_tag ) {
                $post_category[] = $wp_category;
            }
        }
    }
*/

    $video_user = get_user_by( 'login', $defaults['author_username'] );
    $post_author = $video_user->ID;

    $post_data = array(
        'post_title'    => $post_title,
        'post_excerpt'  => $post_excerpt,
        'post_content'  => $post_content,
        'post_status'   => $defaults['status'],
        'post_type'     => $defaults['post_type'],
        'post_author'   => $post_author,
        'post_category' => $post_category,
        'tags_input'    => $tags_input,
    );

    $error_obj = NULL;
    $post_id = wp_insert_post( $post_data, $error_obj );

    if ( $post_id > 0 ) {
        $thumbnail_attach_id = hoy_brightcove_importer_attach_image_to_post( $video['thumbnailURL'], $post_id );
        $video_still_attach_id = hoy_brightcove_importer_attach_image_to_post( $video['videoStillURL'], $post_id );

        set_post_thumbnail( $post_id, $video_still_attach_id );

        update_post_meta( $post_id, '_brightcove_video_id', $video_id );

        set_post_format( $post_id, $defaults['format'] );
    } 
    else {
        return false;
    }

    $new_post = array( 'id' => $post_id );
    return $new_post;

}

function hoy_brightcove_importer_import_new_videos() {

    $options = get_option( 'hoy_brightcove_importer' );
    $imported_videos = $options['hoy_brightcove_importer_imported_videos'];
    $new_videos = $options['hoy_brightcove_importer_new_videos'];

    // For each video in the queue of fresh videos that have not yet been imported,
    // create a post and then add the video to the list of imported videos
    foreach( $new_videos as $index => $new_video ) {
        $new_post = hoy_brightcove_importer_import_video( $new_video );

        // Store the original video info from the Brightcove API along with information
        // on the corresponding new post
        $imported_video = array( 'video' => $new_video, 'post' => $new_post );
        $imported_videos[] = $imported_video;
    }

    $options['hoy_brightcove_importer_imported_videos'] = $imported_videos;
    $options['hoy_brightcove_importer_new_videos'] = array();
    $options['hoy_brightcove_importer_last_imported'] = time();

    update_option( 'hoy_brightcove_importer', $options );

}

/*

 SSSSS   CCCCC  HH   HH EEEEEEE DDDDD   UU   UU LL      IIIII NN   NN   GGGG
SS      CC    C HH   HH EE      DD  DD  UU   UU LL       III  NNN  NN  GG  GG
 SSSSS  CC      HHHHHHH EEEEE   DD   DD UU   UU LL       III  NN N NN GG
     SS CC    C HH   HH EE      DD   DD UU   UU LL       III  NN  NNN GG   GG
 SSSSS   CCCCC  HH   HH EEEEEEE DDDDDD   UUUUU  LLLLLLL IIIII NN   NN  GGGGGG


ww      ww pp pp            cccc rr rr   oooo  nn nnn
ww      ww ppp  pp _____  cc     rrr  r oo  oo nnn  nn
 ww ww ww  pppppp         cc     rr     oo  oo nn   nn
  ww  ww   pp              ccccc rr      oooo  nn   nn
           pp

*/
function hoy_brightcove_importer_cron_exec() {

    hoy_brightcove_importer_fetch_new_videos();
    hoy_brightcove_importer_import_new_videos();

}
add_action( 'hoy_brightcove_importer_cron_hook', 'hoy_brightcove_importer_cron_exec' );

/*

Scheduling this to be done on a regular basis with wp-cron.
https://developer.wordpress.org/plugins/cron/understanding-wp-cron-scheduling/

*/
if( !wp_next_scheduled( 'hoy_brightcove_importer_cron_hook' ) ) {
    wp_schedule_event( time(), $default_autoimport_frequency, 'hoy_brightcove_importer_cron_hook' );
}

register_deactivation_hook( __FILE__, 'hoy_brightcove_importer_deactivate' );
function hoy_brightcove_importer_deactivate() {
    $timestamp = wp_next_scheduled( 'hoy_brightcove_importer_cron_hook' );
    wp_unschedule_event( $timestamp, 'hoy_brightcove_importer_cron_hook' );
}

/*

IIIII NN   NN IIIII TTTTTTT
 III  NNN  NN  III    TTT
 III  NN N NN  III    TTT
 III  NN  NNN  III    TTT
IIIII NN   NN IIIII   TTT


*/

function hoy_brightcove_importer_styles() {

    wp_enqueue_style( 'hoy_brightcove_importer_styles', HOY_BRIGHTCOVE_IMPORTER_URL . 'css/hoy-brightcove-importer.css', array(),
        filemtime( HOY_BRIGHTCOVE_IMPORTER_DIR . '/css/hoy-brightcove-importer.css' ) );

    // DataTable jQuery Plugin
    wp_enqueue_script( 'hoy_brightcove_importer_datatable_script', HOY_BRIGHTCOVE_IMPORTER_URL . 'js/jquery.dataTables.min.js', array('jquery'), '1.10.4' );
    wp_enqueue_style( 'hoy_brightcove_importer_datatable_style', HOY_BRIGHTCOVE_IMPORTER_URL . 'css/jquery.dataTables.min.css', array(), '1.10.4' );

}
add_action( 'admin_head', 'hoy_brightcove_importer_styles' );


function hoy_brightcove_importer_init() {

    load_plugin_textdomain( 'hoy-brightcove-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}
add_action( 'init', 'hoy_brightcove_importer_init' );

?>