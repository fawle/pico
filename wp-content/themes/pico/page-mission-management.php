<?php
if (!is_user_logged_in()) {
    wp_redirect(home_url());
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

            $userId =  get_current_user_id();
            $mission = $wpdb->get_row( $wpdb->prepare(
                'SELECT * FROM wp_users 
                inner join wp_usermeta on wp_usermeta.user_id = wp_users.ID and meta_key=\'wp_user_level\' AND meta_value = 2
                left JOIN mission_details on mission_details.mission_id = wp_users.ID
                left join mission_checklist on mission_checklist.user_id = wp_users.ID
                where ID = %d',
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
                <div>Preparation Checkboxes will be managed here</div>
                <div>Mission journal: quick form to add blog posts will be here</div>
            </div>
            


        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
