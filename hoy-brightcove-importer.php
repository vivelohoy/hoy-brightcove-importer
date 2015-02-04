<?php
/*
 * Plugin Name: Hoy Brightcove Importer Plugin
 * Plugin URI: http://vivelohoy.com/
 * Description: Imports Brightcove videos as individual posts.
 * Version: 1.2.0
 * Author: Nick Bennett
 * Author URI: http://twitter.com/yoyoohrho
 * License: MIT
 */


/*

DDDDD   EEEEEEE FFFFFFF IIIII NN   NN EEEEEEE  SSSSS
DD  DD  EE      FF       III  NNN  NN EE      SS
DD   DD EEEEE   FFFF     III  NN N NN EEEEE    SSSSS
DD   DD EE      FF       III  NN  NNN EE           SS
DDDDDD  EEEEEEE FF      IIIII NN   NN EEEEEEE  SSSSS

*/

if( !defined( 'HOY_BRIGHTCOVE_IMPORTER_DIR' ) ) {
    define('HOY_BRIGHTCOVE_IMPORTER_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'HOY_BRIGHTCOVE_IMPORTER_URL' ) ) {
    define('HOY_BRIGHTCOVE_IMPORTER_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}

/*

IIIII NN   NN IIIII TTTTTTT
 III  NNN  NN  III    TTT
 III  NN N NN  III    TTT
 III  NN  NNN  III    TTT
IIIII NN   NN IIIII   TTT

*/

register_uninstall_hook( __FILE__, 'hoy_brightcove_importer_uninstall' );
register_deactivation_hook( __FILE__, 'hoy_brightcove_importer_deactivation' );
register_activation_hook( __FILE__, 'hoy_brightcove_importer_activation' );
add_action( 'admin_menu', 'hoy_brightcove_importer_menu' );


function hoy_brightcove_importer_uninstall() {
    delete_option( 'hoy_brightcove_importer' );

    // Remove capabilities
    global $wp_roles;
    $wp_roles->remove_cap( 'administrator', 'manage_brightcove_importer_options' );
    $wp_roles->remove_cap( 'editor', 'manage_brightcove_importer_options' );
}

function hoy_brightcove_importer_deactivation() {
//    delete_option( 'hoy_brightcove_importer' );

    // Remove capabilities
    global $wp_roles;
    $wp_roles->remove_cap( 'administrator', 'manage_brightcove_importer_options' );
    $wp_roles->remove_cap( 'editor', 'manage_brightcove_importer_options' );
}

function hoy_brightcove_importer_activation() {
    $defaults = array(
        'hoy_brightcove_importer_api_key'           => '',
        'hoy_brightcove_importer_imported_videos'   => array(),
        'hoy_brightcove_importer_new_videos'        => array(),
        'hoy_brightcove_importer_last_updated'      => false,
        'hoy_brightcove_importer_last_imported'     => false,
        'hoy_brightcove_importer_ready_tag'         => 'listo',
        'base_category'                             => 'Video',
        'tag_to_category_map'                       => array(
                'Chicago e Illinois'            => 'Chicago e Illinois',
                'Chicago'                       => 'Chicago e Illinois',
                'Nación y Mundo'                => 'Nación y Mundo',
                'Deportes'                      => 'Deportes',
                'Entretenimiento'               => 'Entretenimiento',
                'Inmigración'                   => 'Inmigración',
                'Economía'                      => 'Economía',
                'Salud y Vida'                  => 'Salud y Vida',
                'Tecnología'                    => 'Tecnología',
                'Documental'                    => 'Documental',
                'Historias de contenido Humano' => 'Historias de contenido Humano',
                'Contenido Humano'              => 'Historias de contenido Humano'
            )
    );

    // Define default option settings
    if( !is_array( get_option( 'hoy_brightcove_importer' ) ) ) {
        delete_option( 'hoy_brightcove_importer' );
        update_option( 'hoy_brightcove_importer', $defaults );
    }
    // Check that all default keys exist in the existing options, in case there have been
    // changes since the last activation and we don't want to lose the settings we have
    $options = get_option( 'hoy_brightcove_importer' );
    foreach( $defaults as $key => $value ) {
        if( !array_key_exists( $key, $options ) ) {
            $options[$key] = $value;
        }
    }

    // Check that the categories exist and create them if they don't
    // Does the base category exist?
    $base_category_id = get_term_by( 'name', $defaults['base_category'], 'category' );
    if( !$base_category_id ) {
        $base_category_id = wp_create_category( $defaults['base_category'] );
    }
    // Do the sub-categories exist?
    foreach( $defaults['tag_to_category_map'] as $tag => $category ) {
        if( !get_term_by( 'name', $category, 'category' ) ) {
            // Create category as a sub-category of the base category
            wp_create_category( $category, $base_category_id );
        }
    }

    // Add capabilities
    global $wp_roles;
    $wp_roles->add_cap( 'administrator', 'manage_brightcove_importer_options' );
    $wp_roles->add_cap( 'editor', 'manage_brightcove_importer_options' );
}

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

    // Process the interactions with the user-level admin page
    add_action( 'admin_post_process_hoy_brightcove_importer_admin', 'process_hoy_brightcove_importer_admin' );
    // Process the interactions with the admin-level options page
    add_action( 'admin_post_save_hoy_brightcove_importer_options', 'process_hoy_brightcove_importer_options' );
}
add_action( 'init', 'hoy_brightcove_importer_init' );


/*

IIIII NN   NN TTTTTTT EEEEEEE RRRRRR  FFFFFFF   AAA    CCCCC  EEEEEEE
 III  NNN  NN   TTT   EE      RR   RR FF       AAAAA  CC    C EE
 III  NN N NN   TTT   EEEEE   RRRRRR  FFFF    AA   AA CC      EEEEE
 III  NN  NNN   TTT   EE      RR  RR  FF      AAAAAAA CC    C EE
IIIII NN   NN   TTT   EEEEEEE RR   RR FF      AA   AA  CCCCC  EEEEEEE


*/


function hoy_brightcove_importer_menu() {
    add_menu_page(
        'Hoy Brightcove Importer Administration Page',
        'Hoy Brightcove Importer',
        'manage_brightcove_importer_options',
        'hoy-brightcove-importer-main-menu',
        'hoy_brightcove_importer_main'
    );

    add_submenu_page(
        'hoy-brightcove-importer-main-menu',
        'Hoy Brightcove Importer Options',
        'Options',
        'manage_brightcove_importer_options',
        'hoy-brightcove-importer-submenu',
        'hoy_brightcove_importer_submenu'
    );
}

function hoy_brightcove_importer_main() {
    if( !current_user_can( 'manage_brightcove_importer_options' ) ) {
        wp_die( 'You do not have sufficient permission to access this page.' );
    }

    $options = get_option( 'hoy_brightcove_importer' );
    date_default_timezone_set( 'America/Chicago' );
    ?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2><?php _e( 'Hoy Brightcove Media Importer Plugin', 'hoy-brightcove-importer' ); ?></h2>
        <form name="hoy_brightcove_importer_import_videos" method="post" action="admin-post.php">
            <input type="hidden" name="action" value="process_hoy_brightcove_importer_admin" />
            <input type="hidden" name="hoy_brightcove_importer_import_videos_do_it" value="y" />
            <?php wp_nonce_field( 'hoy_brightcove_importer' ); ?>
            <p>
                <input class="button-primary" type="submit" name="hoy_brightcove_importer_import_videos_submit" value="Get Videos" />
            </p>
        </form>

<?php $imported_videos = $options['hoy_brightcove_importer_imported_videos']; ?>
        <h3>
            <span><?php _e( 'Imported Brightcove Videos', 'hoy-brightcove-importer' ); ?> (<?php echo count( $imported_videos ); ?>)</span>
        </h3>
        <span><?php _e( 'Last Import:', 'hoy-brightcove-importer' ); ?> <?php
            if( $options['hoy_brightcove_importer_last_imported'] == 0) {
                _e( 'Never', 'hoy-brightcove-importer' );
            } else {
                echo date( 'r T', (int) $options['hoy_brightcove_importer_last_imported'] );
            } ?></span>
<?php if ( count( $imported_videos ) > 0 ) : ?>
            <table id="imported_videos" class="display">
                <thead>
                    <tr>
                        <th><?php _e( 'Thumbnail', 'hoy-brightcove-importer' ); ?></th>
                        <th><?php _e( 'Name', 'hoy-brightcove-importer' ); ?></th>
                        <th><?php _e( 'Tags', 'hoy-brightcove-importer' ); ?></th>
                        <th><?php _e( 'Last Modified', 'hoy-brightcove-importer' ); ?></th>
                        <th><?php _e( 'View', 'hoy-brightcove-importer' ); ?></th>
                        <th><?php _e( 'Edit', 'hoy-brightcove-importer' ); ?></th>
                    </tr>
                </thead>
                <tbody>
<?php for( $i = 0; $i < count( $imported_videos ); $i++ ): ?>
<?php 
$video = $imported_videos[$i]['video'];
$new_post = $imported_videos[$i]['post'];
?>
                    <tr>
                        <td><div style="max-width: 120px;"><img style="width: 100%;" src="<?php echo $video['thumbnailURL']; ?>"></div></td>
                        <td><?php echo $video['name']; ?></td>
                        <td><?php echo implode( ', ', $video['tags'] ); ?></td>
                        <td><?php echo date( 'c', (int) ( $video['lastModifiedDate'] / 1000.0 ) ); ?></td>
                        <td>
                            <?php if ( $new_post ) : ?>
                                <a href="<?php echo get_permalink( $new_post['id'] ); ?>"><?php _e( 'View', 'hoy-brightcove-importer' ); ?></a>
                            <?php else: ?>
                                <?php _e( 'Not imported!', 'hoy-brightcove-importer' ); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $new_post ) : ?>
                                <a href="<?php echo get_edit_post_link( $new_post['id'] ); ?>"><?php _e('Edit', 'hoy-brightcove-importer'); ?></a>
                            <?php else: ?>
                                <?php _e( 'Not imported!', 'hoy-brightcove-importer' ); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
<?php endfor; ?>
                </tbody>
            </table>
<?php endif; ?><!-- // if ( count( $hoy_brightcove_importer_imported_videos ) > 0 ) -->
</div> <!-- .wrap -->
<script>
(function($) {
    $(document).ready(function() {
        // column index 3 (4th column) is Date and we want newest on top
        $('#imported_videos').DataTable({
            "order": [ [ 3, 'desc' ] ]
        });
    });
})(jQuery);
</script>
    <?php
}

function process_hoy_brightcove_importer_admin() {
    if( !current_user_can( 'manage_brightcove_importer_options' ) ) {
        wp_die( 'You do not have sufficient permission to access this page.' );
    }

    check_admin_referer( 'hoy_brightcove_importer' );

    if( isset( $_POST['hoy_brightcove_importer_import_videos_submit'] ) ) {
        hoy_brightcove_importer_fetch_new_videos();
        hoy_brightcove_importer_import_new_videos();
    }

    wp_redirect( add_query_arg( 'page', 
                                'hoy-brightcove-importer-main-menu',
                                admin_url( 'admin.php' ) ) );

    exit;
}

function hoy_brightcove_importer_submenu() {
    if( !current_user_can( 'manage_brightcove_importer_options' ) ) {
        wp_die( 'You do not have sufficient permission to access this page.' );
    }

    $options = get_option( 'hoy_brightcove_importer' );
    ?>
<div class="wrap">
    <h3><span><?php _e( "Let's get started!", 'hoy-brightcove-importer' ); ?></span></h3>
    <div class="inside">
        <form name="hoy_brightcove_importer_api_key_form" method="post" action="admin-post.php">
            <input type="hidden" name="action" value="save_hoy_brightcove_importer_options" />
            <?php wp_nonce_field( 'hoy_brightcove_importer' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <td scope="row">
                        <label for="hoy_brightcove_importer_api_key"><?php _e( 'Brightcove Media API Key'); ?></label>
                    </td>
                    <td>
                        <input name="hoy_brightcove_importer_api_key" id="hoy_brightcove_importer_api_key" type="text" value="<?php echo $options['hoy_brightcove_importer_api_key']; ?>" class="regular-text" />
                    </td>
                </tr>
            </table>
            <p> 
                <input class="button-primary" type="submit" name="hoy_brightcove_importer_api_key_form_submit" value="<?php _e( 'Save', 'hoy-brightcove-importer' ); ?>" /> 
            </p>
        </form>
    </div> <!-- .inside -->
</div> <!-- .wrap -->
    <?php
}

function process_hoy_brightcove_importer_options() {
    if( !current_user_can( 'manage_brightcove_importer_options' ) ) {
        wp_die( 'You do not have sufficient permission to access this page.' );
    }

    check_admin_referer( 'hoy_brightcove_importer' );

    $options = get_option( 'hoy_brightcove_importer' );
    if( isset( $_POST['hoy_brightcove_importer_api_key'] ) ) {
        $options['hoy_brightcove_importer_api_key'] = sanitize_text_field( $_POST['hoy_brightcove_importer_api_key'] );
        update_option( 'hoy_brightcove_importer', $options );
    }

    wp_redirect( add_query_arg( 'page', 
                                'hoy-brightcove-importer-submenu',
                                admin_url( 'admin.php' ) ) );

    exit;
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
    $options = get_option( 'hoy_brightcove_importer' );

    $defaults = array(
        'width'             => 853,
        'height'            => 480,
        'category'          => $options['base_category'],
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

    // For each of the remaining tags, check if they match any of our pre-defined categories
    // If a tag matches, get the corresponding category and add it to the list of categories for the post
    $video_cat = get_term_by( 'name', $defaults['category'], 'category' );
    $post_category = array( $video_cat->term_id );

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


?>