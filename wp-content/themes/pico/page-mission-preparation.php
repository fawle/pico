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
                'SELECT * FROM wp_users left join mission_checklist on mission_checklist.user_id = wp_users.ID
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
                <div>Checklist: </div>
            </div>

            <div>Mission Journal:</div>
            <?php $query = new WP_Query( 'posts_per_page=-1&author='.$mission->ID );
            if( $query->have_posts() ) : ?>
                <?php while( $query->have_posts() ) : $query->the_post();
                    echo $post->post_title.'<br/>';
                    echo $post->post_content.'<br/>';
                    echo $post->post_date.'<br/>';
                endwhile;
            endif;
            wp_reset_postdata(); ?>

        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
