<?php

$missionId = $_GET['id'];

if (!(int) $missionId) {
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
                where ID = %d',
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
                <div><a href="<?php echo home_url() ?>/mission-preparation?id=<?php echo $mission->ID; ?>">Mission preparation</a></div>
                <div><a href="<?php echo home_url() ?>/mission-flight?id=<?php echo $mission->ID; ?>">Mission flight</a></div>
                <div><a href="<?php echo home_url() ?>/mission-archive?id=<?php echo $mission->ID; ?>">Mission archive</a></div>
            </div>

        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
