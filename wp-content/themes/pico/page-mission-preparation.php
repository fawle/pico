<?php

/**
 * @TODO confirm data formats
 */

$missionId = filter_var($wp_query->query_vars['mission'], FILTER_SANITIZE_STRING);

if (! $missionId) {
    wp_redirect(home_url().'/all-missions');
    exit();
}

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main plugged" role="main">
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
            $mission = $wpdb->get_results( $wpdb->prepare(
                'SELECT * FROM wp_users 
                inner join wp_usermeta on wp_usermeta.user_id = wp_users.ID and meta_key=\'wp_user_level\' AND meta_value = 2
                left join mission_details on mission_details.mission_id = wp_users.ID
                left join mission_checklist on mission_checklist.user_id = wp_users.ID
                left join mission_steps on mission_steps.step_id = mission_checklist.step_id
                where user_login = %s
                order by mission_steps.step_id',
                $missionId
            ) );

            if (!$mission) {
                echo '<H1>Mission not found</H1>';
                exit();
            }
            $missionId = $mission[0]->ID;
            ?>
            <div class="entry-content" >
                <div>Mission: <?php echo $mission[0]->display_name; ?></div>
                <div>Checklist: checklist is displayed here</div>
                <?php if (count($mission) > 1) {
                ?>
                <table>
                    <tr>
                        <td>Step</td><td>Planned For</td><td>Completed On</td>
                    </tr>
            
                <?php

                foreach ($mission as $missionStep) { ?>
                    <tr>
                        <td><?php echo $missionStep->step_id .'. '. $missionStep->step_label; ?></td>
                        <td><?php echo strtotime($missionStep->planned_for) > 0 ? date('d M Y', strtotime($missionStep->planned_for)) : 'n/a'; ?></td>
                        <td><?php echo strtotime($missionStep->completed_on) > 0 ? date('d M Y', strtotime($missionStep->completed_on)) : 'n/a'; ?></td>

                    </tr>
                <?php } ?> 
                </table>
                <?php } ?>
            </div>

            <h2>Mission Journal:</h2>
            <?php


            $query = new WP_Query( 'posts_per_page=-1&author='.$missionId );
 
            if( $query->have_posts() ) : ?>
                <?php while( $query->have_posts() ) : $query->the_post();
                    echo '<h4>'.$post->post_title.'</h4>';
                    if(has_post_thumbnail()){
                        echo "<div>";
                        the_post_thumbnail();
                        echo "</div>";
                    }
                    the_content();
                    echo $post->post_date.'<br/>';
                
                endwhile;
            endif;
            wp_reset_postdata(); ?>

        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
