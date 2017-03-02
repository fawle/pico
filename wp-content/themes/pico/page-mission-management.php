<?php

/**
 * @TODO add proper back and front end date validation
 * @TODO add datepicker 
 * @TODO add status flag management
 */

/** @TODO shift all this into a hook at least */

if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit();
}
$userId =  get_current_user_id();

/**
 * rough and dirty save form input
 */

if ($_POST && $_POST['form_submitted']==='1')
{
    do_action('pico_process_mission');
    wp_redirect(get_permalink());
}



get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
        // Start the loop.
        while ( have_posts() ) : the_post();

            // Include the page content template.
            get_template_part( 'template-parts/content', 'page' );


        endwhile;
        ?>

        <div class="entry-content">
            <?php  //get user missions
            global $wpdb;
            /**@var wpdb  $wpdb */


            $mission = $wpdb->get_row( $wpdb->prepare(
                'SELECT * FROM wp_users 
                inner join wp_usermeta on wp_usermeta.user_id = wp_users.ID and meta_key=\'wp_user_level\' AND meta_value = 2
                left JOIN mission_details on mission_details.mission_id = wp_users.ID
                where ID = %s
               ',
                $userId
            ), OBJECT );

            if (!$mission) {
                echo '<H1>Mission not found</H1>';
                exit();
            }

            ?>
            <div class="entry-content" >
                <div>Mission: <?php echo $mission->display_name; ?></div>



                <div>Preparation Checklist managed here
                    (eventually with a calendar!)

                <?php
                $steps = $wpdb->get_results($wpdb->prepare(
                    'SELECT step_label, mission_steps.step_id, planned_for, completed_on, status  
                FROM mission_steps 
                left JOIN mission_details on mission_details.mission_id = %d
                left JOIN mission_checklist on mission_checklist.step_id = mission_steps.step_id AND mission_checklist.user_id = %d
                order by mission_steps.step_id
               ', $mission->ID,$mission->ID ));

                ?>

                    <div class="mission-picture">
                        
                        <?php if ($mission->pic_url) {
                            echo "<img src='".$mission->pic_url."' />";
                        }
                        ?>
                    </div>
                <form enctype="multipart/form-data" action="<?php echo home_url('mission-management'); ?>" method="POST">
                    <input type="hidden" name="form_submitted" id="form_submitted" value="1"/>
                    <p>
                        <label for="status"> Status:</label>
                        <select id="status" name="status">
                            <option value="">--Select--</option>
                            <option value="1" <?php if ($steps[0]->status == 1) echo 'selected';?>>Live</option>
                            <option value="0" <?php if ($steps[0]->status == 0) echo 'selected';?>>Archived</option>
                        </select>
                    </p>
                    <table>
                        <tr>
                            <td>Step</td><td>Planned For</td><td>Completed On</td>
                        </tr>

                        <?php foreach ($steps as $missionStep) { ?>
                            <tr>
                                <td><?php echo $missionStep->step_label; ?></td>
                                <td><input class="" name="planned[<?php echo $missionStep->step_id ; ?>]" type="date" value="<?php echo $missionStep->planned_for; ?>" /></td>
                                <td><input name="completed[<?php echo $missionStep->step_id ; ?>]" type="date" value="<?php echo $missionStep->completed_on; ?>" /></td>
                            </tr>
                        <?php } ?>
                    </table>


                    Upload new picture
                    <input type="file" name="m_image" id="m_image" />

                    <button type="submit" value="Update" >Update</button>
                </form>
                </div>

                <div>Mission journal: quick form to add blog posts will be here</div>
            </div>
            


        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
