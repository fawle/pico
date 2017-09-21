<?php

/**
 * process mission update
 */
function wp_pico_process_mission()
{

    global $wpdb;
    $userId =  get_current_user_id();
    if ($_POST && ($_POST['status']) !=='') {
        $status = (int)$_POST['status'];
        $wpdb->get_results($wpdb->prepare('
          INSERT INTO mission_details (mission_id, status)
          VALUES(%d, %d) ON DUPLICATE KEY UPDATE    
          status = %d',
            $userId, $status, $status)
        );
    }

    $aPlanned = (array) $_POST['planned'];
    $aCompleted = (array) $_POST['completed'];

    if (array_filter($aPlanned)) {

        /**
         * loop through steps
         */
        for ($i = 1; $i <= count($aPlanned); $i++) {
            //convert to date
            $planned = (!$aPlanned[$i] || $aPlanned[$i] === '0000-00-00') ? '0000-00-00' : date('Y-m-d', strtotime($aPlanned[$i]));
            $completed = (!$aCompleted[$i] || $aCompleted[$i] === '0000-00-00') ? '0000-00-00' : date('Y-m-d', strtotime($aCompleted[$i]));
            $wpdb->get_results($wpdb->prepare('
          INSERT INTO mission_checklist (user_id, step_id, planned_for, completed_on)
          VALUES(%d, %d, %s, %s ) ON DUPLICATE KEY UPDATE    
          planned_for = %s, completed_on = %s',
                $userId, $i, $planned, $completed, $planned, $completed)
            );
        }
    }

    if (isset($_FILES['m_image']) && ($_FILES['m_image']['size'] > 0)) {

        $imageFile = $_FILES['m_image'];
        $uploadedFile = uploadImage($imageFile);

            if (!is_array($uploadedFile) || !isset($uploadedFile['url'])) {
                return false;
            }
        $picUrl = $uploadedFile['url'];
        $wpdb->get_results($wpdb->prepare('
          INSERT INTO mission_details (mission_id, pic_url)
          VALUES(%d, %s) ON DUPLICATE KEY UPDATE    
          pic_url = %s',
            $userId, $picUrl, $picUrl)
        );
    }

    return true;
}
add_action ('pico_process_mission', 'wp_pico_process_mission');

/**
 *
 */
function  wp_pico_process_post()
{
    $title = 'Mission update';
    $tags = [];
    $description = 'No update';

    if (isset ($_POST['post_title'])) {
        $title =  $_POST['post_title'];
    }
    if (isset ($_POST['post_content'])) {
        $description = $_POST['post_content'];
        //toDO format here - insert line breaks.
    }
    if (isset ($_POST['post_tags'])) {
        $tags = $_POST['post_tags'];
    }



    // Add the content of the form to $post as an array
    $new_post = array(
        'post_title'    => $title,
        'post_content'  => $description,
        //'post_category' => array($_POST['cat']),  // Usable for custom taxonomies too
        //'tags_input'    => array($tags),
        'post_status'   => 'publish',           // Choose: publish, preview, future, draft, etc.
        'post_type' => 'post'  //'post',page' or custom post type
    );
    $postId = wp_insert_post($new_post);

    //toDO upload featured image
    if (isset($_FILES['post_image']) && ($_FILES['post_image']['size'] > 0)) {
        $imageFile = $_FILES['post_image'];
        $uploadedFile = uploadImage($imageFile);
        $wp_filetype = wp_check_filetype(basename($uploadedFile['file']), null );
        $wp_upload_dir = wp_upload_dir();
        $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . basename( $uploadedFile['file'] ),
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($imageFile['name'])),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attachId = wp_insert_attachment( $attachment, $uploadedFile['file'], $postId);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attachId, $uploadedFile['file'] );
        wp_update_attachment_metadata( $attachId, $attach_data );
        update_post_meta($postId, '_thumbnail_id', $attachId);
    }

    //insert taxonomies
}

/**
 * @param $imageFile $_POST array
 * @return array
 */
function uploadImage($imageFile)
{
    //TODO file size check
    $arrFileType = wp_check_filetype(basename($imageFile['name']));
    $uploadedFileType = $arrFileType['type'];
    // Set an array containing a list of acceptable formats
    $allowedFileTypes = array('image/jpg','image/jpeg','image/gif','image/png');

    if(!in_array($uploadedFileType, $allowedFileTypes)) {
        //@TODO throw exception instead
        $uploadFeedback = 'Please upload only image files (jpg, gif or png).';;
    } else {

        // Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
        // Options array for the wp_handle_upload function. 'test_upload' => false
        $uploadOverrides = array('test_form' => false);
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        return wp_handle_upload($imageFile, $uploadOverrides);
    }
}

add_action ('pico_process_post', 'wp_pico_process_post');

/**
 * resize filter
 */
add_filter('wp_handle_upload', 'max_dims_for_new_uploads', 10, 2 );
function max_dims_for_new_uploads( $array, $context ) {
    // $array = array( 'file' => $new_file, 'url' => $url, 'type' => $type )
    // $context = 'upload' || 'sideload'
    $ok = array( 'image/jpeg', 'image/gif', 'image/png' );
    if ( ! in_array( $array['type'], $ok ) ) return $array;
    $editor = wp_get_image_editor( $array['file'] );
    if ( is_wp_error( $editor ) )
        return $editor;
    $editor->set_quality( 90 );
    $editor->resize( 600, 500, false ); // (int) max width, (int) max height[, (bool) crop]
    $editor->save( $array['file'] );
    return $array;
}