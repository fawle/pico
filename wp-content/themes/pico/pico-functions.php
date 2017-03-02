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

        //copying from stackexchange
        // Get the type of the uploaded file. This is returned as "type/extension"
        $arrFileType = wp_check_filetype(basename($_FILES['m_image']['name']));
        $uploadedFileType = $arrFileType['type'];
        // Set an array containing a list of acceptable formats
        $allowedFileTypes = array('image/jpg','image/jpeg','image/gif','image/png');

        if(!in_array($uploadedFileType, $allowedFileTypes)) {
            //@TODO throw exception instead
            $uploadFeedback = 'Please upload only image files (jpg, gif or png).';;
        } else {


            // Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
            // Options array for the wp_handle_upload function. 'test_upload' => false
            $uploadOverrides = array( 'test_form' => false );
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            $uploadedFile = wp_handle_upload($_FILES['m_image'], $uploadOverrides);

            //save into mission_details
            if (!isset($uploadedFile['url'])) {
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

    }

    return true;
}
add_action ('pico_process_mission', 'wp_pico_process_mission');
