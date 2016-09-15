<?php
if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit();
}
$userId =  get_current_user_id();
/**
 * rough and dirty save form input
 */

for ($i=1; $i<=count($_POST['planned']); $i++) {
    //convert to date
    $planned = (!$_POST['planned'][$i] || $_POST['planned'][$i] === '0000-00-00') ? '0000-00-00' : date('Y-m-d', strtotime($_POST['planned'][$i])) ;
    $completed = (!$_POST['completed'][$i] || $_POST['completed'][$i] === '0000-00-00') ? '0000-00-00' : date('Y-m-d', strtotime($_POST['completed'][$i]));
    $wpdb->get_results($wpdb->prepare('
      INSERT INTO mission_checklist (user_id, step_id, planned_for, completed_on)
      VALUES(%d, %d, %s, %s ) ON DUPLICATE KEY UPDATE    
      planned_for = %s, completed_on = %s',
      $userId, $i, $planned, $completed, $planned, $completed)
    );
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
                <div>Status will be managed here</div>

                <div>Preparation Checklist will be managed here
                    (eventually with a calendar!)

                <?php
                $steps = $wpdb->get_results($wpdb->prepare(
                    'SELECT * FROM mission_steps 
                left JOIN mission_checklist on mission_checklist.step_id = mission_steps.step_id AND mission_checklist.user_id = %d
                order by mission_steps.step_id
               ', $mission->ID));

                ?>
                <form action="<?php echo home_url('mission-management'); ?>" method="POST">
                    <table>
                        <tr>
                            <td>Step</td><td>Planned For</td><td>Completed On</td>
                        </tr>

                        <?php foreach ($steps as $missionStep) { ?>
                            <tr>
                                <td><?php echo $missionStep->step_label; ?></td>
                                <td><input name="planned[<?php echo $missionStep->step_id ; ?>]" type="text" value="<?php echo $missionStep->planned_for; ?>" /></td>
                                <td><input name="completed[<?php echo $missionStep->step_id ; ?>]" type="text" value="<?php echo $missionStep->completed_on; ?>" /></td>
                            </tr>
                        <?php } ?>
                    </table>
                    <button type="submit" value="Update" >Update</button>
                </form>
                </div>

                <div>Mission journal: quick form to add blog posts will be here</div>
            </div>
            


        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
