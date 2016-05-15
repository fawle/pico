<?php
if (is_user_logged_in()) {
    wp_redirect(home_url() . '/' . 'mission-control' );
    exit();
}
get_header();
?>


<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
        <header class="entry-header">
            <h1 class="entry-title">Exploring Science, Engineering and the world</h1>	</header>


    </main><!-- .site-main -->



</div><!-- .content-area -->


<?php get_footer(); ?>
