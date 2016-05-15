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

            //get user missions
            global $wpdb;
            /**@var wpdb  $wpdb */
            $results = $wpdb->get_results( 'SELECT * FROM missions WHERE user_id = 1', OBJECT );
        var_dump($results);

            // End of the loop.
        endwhile;
        ?>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
