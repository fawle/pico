<?php

$missionId = filter_var($wp_query->query_vars['mission'], FILTER_SANITIZE_STRING);

if (! $missionId) {
    wp_redirect(home_url().'/all-missions');
    exit();
}

get_header(); ?>

<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDEYRnigI-5bwe9t4ulawHTGSrVywEMf4Q&callback=initMap"></script>

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
                <div>Flight map goes here

                    <div id="map"></div>


                </div>
            </div>
            

        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->


<?php get_footer(); ?>
