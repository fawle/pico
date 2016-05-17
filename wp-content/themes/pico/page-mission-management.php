<?php
if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit();
}
$missionId = $_GET['id'];

if (!(int) $missionId) {
    wp_redirect(home_url().'/mission-control');
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
                'SELECT * FROM missions inner join mission_details on missions.mission_id = mission_details.mission_id WHERE user_id = %d AND missions.mission_id = %d',
                $userId,
                $missionId
            ), OBJECT );


            if (!$mission) {
                echo '<H1>Mission not found</H1>';
                exit();
            }

            ?>
            <div class="entry-content" >
                <div>Mission: <?php echo $mission->name; ?></div>
                <div>Status: <?php echo $mission->status? 'Active' : 'Ended' ?> </div>
                <div><a href="mission-preparation?id=<?php echo $mission->ID; ?>">Mission preparation</a></div>
                <div><a href="mission-flight?id=<?php echo $mission->ID; ?>">Mission flight</a></div>
                <div><a href="mission-archive?id=<?php echo $mission->ID; ?>">Mission archive</a></div>
            </div>
            
            <div>Mission journal:</div>

        </div>

    </main><!-- .site-main -->


</div><!-- .content-area -->

<?php get_footer(); ?>
