<?php
get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
        // Start the loop.
        while ( have_posts() ) : the_post();

            // Include the page content template.
            get_template_part( 'template-parts/content');


        endwhile;
        ?>

        <div class="entry-content">
            <?php  //get user missions
            global $wpdb;
            /**@var wpdb  $wpdb */
            $userID =  get_current_user_id();
            $results = $wpdb->get_results( "SELECT user_login as mission_name, status, display_name FROM wp_users 
              inner join wp_usermeta on wp_usermeta.user_id = wp_users.ID and meta_key='wp_user_level' AND meta_value = 2
              left JOIN mission_details on mission_details.mission_id = wp_users.ID
              order by user_registered desc LIMIT 100", OBJECT );

            include(locate_template( 'template-parts/mission.php'));

            // End of the loop. ?>


        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
