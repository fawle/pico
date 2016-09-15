<?php

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
                'SELECT user_login as mission_name, status, display_name FROM wp_users 
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
                <div><a href="<?php echo home_url() ?>/mission-preparation/<?php echo $mission->mission_name; ?>">Mission preparation</a></div>
                <div><a href="<?php echo home_url() ?>/mission-flight/<?php echo $mission->mission_name; ?>">Mission flight</a></div>
                <div><a href="<?php echo home_url() ?>/mission-archive/<?php echo $mission->mission_name; ?>">Mission archive</a></div>
            </div>

        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>