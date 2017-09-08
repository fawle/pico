<?php

/**
 * @TODO confirm data structure - no idea what should be here atm OR when we save it and populate the page
 */

$missionId = filter_var($wp_query->query_vars['mission'], FILTER_SANITIZE_STRING);

if (!$missionId) {
    wp_redirect(home_url().'/all-missions');
    exit();
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
                left join mission_checklist on mission_checklist.user_id = wp_users.ID
                where user_login = %s',
                $missionId
            ), OBJECT );


            if (!$mission) {
                echo '<H1>Mission not found</H1>';
                exit();
            }

            ?>
            <div class="entry-content" >
                <div>Mission: <?php echo $mission->display_name; ?></div>
                <div>Status: <?php echo $mission->status? 'Active' : 'Ended' ?> </div>
                <div>If we have archived any data from the tracker, it will appear below</div>
                <div class="">
                    Date: <?php echo date('Y.m.d', strtotime($mission->mission_date)); ?><br/>
                    Time: <?php echo date('h:m:i', strtotime($mission->mission_date)); ?><br/>
                    Lat: <?php echo $mission->latitude; ?><br/>
                    Long: <?php echo $mission->longitude; ?><br/>
                    Alt: <?php echo $mission->altitude; ?><br/>
                    Vrate: <?php echo $mission->vrate; ?><br/>
                    Hrate: <?php echo $mission->hrate; ?><br/>
                </div>
            </div>

        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
