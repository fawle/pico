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
$userId = get_current_user_id();

/**
 * rough and dirty save form input
 */

if ($_POST && isset($_POST['form_submitted']) && $_POST['form_submitted'] === '1') {
    do_action('pico_process_mission');
    wp_redirect(get_permalink());
}

if ($_POST && isset($_POST['post_submitted']) && $_POST['post_submitted'] === '1') {
    do_action('pico_process_post');
    wp_redirect(get_permalink());
}

//todo delete action

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main plugged" role="main">
        <?php
        // Start the loop.
        while (have_posts()) : the_post();

            // Include the page content template.
            get_template_part('template-parts/content', 'page');


        endwhile;
        ?>

        <div class="entry-content">
            <?php //get user missions
            global $wpdb;
            /**@var wpdb $wpdb */


            $mission = $wpdb->get_row($wpdb->prepare(
                'SELECT * FROM wp_users 
                inner join wp_usermeta on wp_usermeta.user_id = wp_users.ID and meta_key=\'wp_user_level\' AND meta_value = 2
                left JOIN mission_details on mission_details.mission_id = wp_users.ID
                where ID = %s
               ',
                $userId
            ), OBJECT);

            if (!$mission) {
                echo '<H1>Mission not found</H1>';
                exit();
            }

            ?>
            <div class="entry-content">


                <div>

                    <?php
                    $steps = $wpdb->get_results($wpdb->prepare(
                        'SELECT step_label, mission_steps.step_id, planned_for, completed_on, status  
                FROM mission_steps 
                left JOIN mission_details on mission_details.mission_id = %d
                left JOIN mission_checklist on mission_checklist.step_id = mission_steps.step_id AND mission_checklist.user_id = %d
                order by mission_steps.step_id
               ', $mission->ID, $mission->ID));

                    ?>


                    <form enctype="multipart/form-data" action="<?php echo home_url('mission-management'); ?>"
                          method="POST">
                        <input type="hidden" name="form_submitted" id="form_submitted" value="1"/>

                        <table>
                            <tr>
                                <td>Call sign:</td>
                                <td><strong><?php echo $mission->user_login; ?></strong></td>
                                <td>(Cannot be changed)</td>
                            </tr>
                            <tr>
                                <td>Step</td>
                                <td>Planned For</td>
                                <td>Completed On</td>
                            </tr>

                            <?php foreach ($steps as $missionStep) { ?>
                                <tr>
                                    <td><?php echo $missionStep->step_label; ?>:</td>
                                    <td><input class="" name="planned[<?php echo $missionStep->step_id; ?>]" type="date"
                                               value="<?php echo $missionStep->planned_for; ?>"/></td>
                                    <td><input name="completed[<?php echo $missionStep->step_id; ?>]" type="date"
                                               value="<?php echo $missionStep->completed_on; ?>"/></td>
                                </tr>
                            <?php } ?>


                            <tr>
                                <td><label for="status"> Status:</label></td>
                                <td colspan="2"><select id="status" name="status">
                                        <option value="">--Select--</option>
                                        <option value="1" <?php if ($steps[0]->status == 1) echo 'selected'; ?>>Live
                                        </option>
                                        <option value="0" <?php if ($steps[0]->status == 0) echo 'selected'; ?>>
                                            Archived
                                        </option>
                                    </select></td>
                            </tr>

                            <tr>
                                <td>Current picture shown on all missions page:</td>

                                <td>
                                    <div class="mission-picture">
                                        <?php if ($mission->pic_url) {
                                            echo "<img width='100px' height='100px' src='" . $mission->pic_url . "' />";
                                        }
                                        ?></div>
                                </td>
                                <td> Upload new picture:
                                    <br/>
                                    <input type="file" name="m_image" id="m_image"/></td>

                            </tr>


                        </table>

                        <button type="submit" value="Update">Update</button>
                    </form>
                </div>

                <div style="padding: 25px 0 0 0"><h1>Mission journal: </h1>

                    <form enctype="multipart/form-data" action="<?php echo home_url('mission-management'); ?>"
                             method="POST">
                        <input type="hidden" name="post_submitted" id="post_submitted" value="1"/>
                        <div class="post_title">
                            <label class="prompt" for="post_title" id="title_prompt_text">Title</label>
                            <input type="text" name="post_title" id="title" autocomplete="off"/>
                        </div>
                        
                        <div class="post_image" style="padding: 5px">
                            <input type="file" name="post_image" id="post_image"/>
                        </div>
                        <div class="post_content">
                            <label class="prompt" for="content" id="content_prompt_text">Content</label>
                        <textarea name="post_content" id="post_content" rows="3" cols="15"
                                  aria-autocomplete="none" style="overflow-y: auto; overflow-x: hidden;"></textarea>
                        </div>


                        <div class="post_submit" style="padding: 15px 0 0 0;">
                            <input type="submit" name="save" id="save-post" class="button button-primary" value="Save post" />
                        </div>
                    </form>

                </div>


                <div style="padding: 20px 0 0 0">

                    <?php


                    $query = new WP_Query( 'posts_per_page=-1&author='.$userId );

                    if( $query->have_posts() ) : ?>
                        <?php while( $query->have_posts() ) : $query->the_post();
                            //var_dump($post);
                            echo '<h4>'.$post->post_title.'</h4>';
                            if(has_post_thumbnail()){
                                echo "<div>";
                                the_post_thumbnail();
                                echo "</div>";
                            }
                            echo $post->post_content.'<br/>';
                            echo $post->post_date.'<br/>';?>
                            <a href="<?php echo home_url(); ?>/mission-management/?delete=<?php echo $post->ID; ?>">Delete</a>
                            <a href="<?php echo home_url(); ?>/mission-postedit/?edit=<?php echo $post->ID; ?>">Edit</a>

                    <?php
                        endwhile;
                    endif;
                    wp_reset_postdata(); ?>
                </div>
            </div>


        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
