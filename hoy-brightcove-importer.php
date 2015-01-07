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
 * Global variables
 */

$options = array();
$display_json = true;
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

if( !defined( 'HOY_BRIGHTCOVE_IMPORTER_DIR' ) ) {
    define('HOY_BRIGHTCOVE_IMPORTER_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'HOY_BRIGHTCOVE_IMPORTER_URL' ) ) {
    define('HOY_BRIGHTCOVE_IMPORTER_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}


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

/*
Experimenting here with the WP Settings API
http://ottopress.com/2009/wordpress-settings-api-tutorial/


function hoy_brightcove_importer_options_page() {
?>
<div>
    <h2>Hoy Brightcove Importer Plugin</h2>
    Options related to the Hoy Brightcove Importer plugin.
    <form action="options.php" method="post">
        <?php settings_fields( 'plugin_options' ); ?>
        <?php do_settings_sections( 'plugin' ); ?>

        <input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
    </form>
</div>
<?php
}

add_action( 'admin_init', 'hoy_brightcove_importer_admin_init' );
function hoy_brightcove_importer_admin_init() {
    register_setting( 'plugin_options', 'plugin_options', 'plugin_options_validate' );
    add_settings_section( 'plugin_main', 'Main Settings', 'plugin_section_text', 'plugin' );
    add_settings_field( 'plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main' );
}

function plugin_section_text() {
    echo '<p>' . __( 'Main description of this section here.' ) . '</p>';
}

*/

function hoy_brightcove_importer_options_page() {

    if( !current_user_can( 'manage_options') ) {
        wp_die( 'You do not have sufficient permission to access this page.' );
    }

    global $options;
    global $display_json;

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

        $hoy_brightcove_importer_api_key = $options['hoy_brightcove_importer_api_key'];
        $hoy_brightcove_importer_imported_videos = $options['hoy_brightcove_importer_imported_videos'];
        $hoy_brightcove_importer_new_videos = $options['hoy_brightcove_importer_new_videos'];
        $hoy_brightcove_importer_last_updated = $options['hoy_brightcove_importer_last_updated'];
        $hoy_brightcove_importer_last_imported = $options['hoy_brightcove_importer_last_imported'];
    }


    require( 'inc/options-page-wrapper.php' );

}


function hoy_brightcove_importer_get_videos( $hoy_brightcove_importer_api_key ) {

    function get_paged_api_results( $hoy_brightcove_importer_api_key, $page_number ) {
        $query_string = http_build_query( array(
            'command' => 'search_videos',
            'token' => $hoy_brightcove_importer_api_key,
            'page_size' => 50,
            'page_number' => $page_number,
            'get_item_count' => 'true',
            'any' => 'tag:ready'
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


function brightcove_video_shortcode( $atts ) {
    if( array_key_exists( 'id', $atts ) ) {
        $atts = shortcode_atts( 
                array( 
                    'width'     => 853,
                    'height'    => 480,
                    'id'        => false
                ),
                $atts );

        $context = Timber::get_context();
        $options = array(   'VIDEO_WIDTH'       => $atts['width'],
                            'VIDEO_HEIGHT'      => $atts['height'],
                            'VIDEO_ID'          => $atts['id'] );
        $context = array_merge( $context, $options );

        return Timber::compile('inc/default-post-template.twig', $context);
    } else {
        return '';
    }
}
add_shortcode( 'brightcove', 'brightcove_video_shortcode' );


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

    $defaults = array(
        'width'             => 853,
        'height'            => 480,
        'category'          => 'Video',
        'author_username'   => 'brightcove',
        'status'            => 'draft',
        'post_type'         => 'post'
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

    $video_cat = get_term_by( 'name', $defaults['category'], 'category' );
    $post_category = array( $video_cat->term_id );

    // Additional WordPress categories can be specified in the Brightcove tags
    foreach( $tags_input as $index => $tag ) {
        $tag_lowercase = strtolower( $tag );
        foreach( $tags_to_categories as $brightcove_tag => $wp_category ) {
            if( $tag == $brightcove_tag ) {
                $post_category[] = $wp_category;
            }
        }
    }

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


function hoy_brightcove_importer_cron_exec() {

    hoy_brightcove_importer_fetch_new_videos();
//    hoy_brightcove_importer_import_new_videos();

}
add_action( 'hoy_brightcove_importer_cron_hook', 'hoy_brightcove_importer_cron_exec' );
if( !wp_next_scheduled( 'hoy_brightcove_importer_cron_hook' ) ) {
    wp_schedule_event( time(), 'hourly', 'hoy_brightcove_importer_cron_hook' );
}

register_deactivation_hook( __FILE__, 'hoy_brightcove_importer_deactivate' );
function hoy_brightcove_importer_deactivate() {
    $timestamp = wp_next_scheduled( 'hoy_brightcove_importer_cron_hook' );
    wp_unschedule_event( $timestamp, 'hoy_brightcove_importer_cron_hook' );
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

}
add_action( 'init', 'hoy_brightcove_importer_init' );

?>