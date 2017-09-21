<?php


if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit();
}
$userId = get_current_user_id();



if (!isset($_GET['edit']) || !(int)$_GET['edit']) {
    wp_redirect(home_url('/'));
    exit();
}

//todo save action


get_header();
$query = new WP_Query( 'posts_per_page=-1&author='.$userId.'&p='. (int)$_GET['edit']);

if( $query->have_posts() ) : ?>
    <?php while( $query->have_posts() ) : $query->the_post();



?>

<div style="padding: 25px 0 0 0"><h1>Mission journal: </h1>

                    <form enctype="multipart/form-data" action="<?php echo home_url('mission-postedit'); ?>"
                             method="POST">
                        <input type="hidden" name="post_submitted" id="post_submitted" value="1"/>
                        <div class="post_title">
                            <label class="prompt" for="post_title" id="title_prompt_text">Title</label>
                            <input type="text" name="post_title" id="title" autocomplete="off" value="<?php echo $post->post_title ?>"/>
                        </div>
                        <?php if(has_post_thumbnail()){
                        echo "<div style=\"padding: 5px 0 0 0 \">";
                            the_post_thumbnail();
                            echo "</div>";
                        } ?>
                        <div class="post_image" style="padding: 5px 0 0 0">
                            <input type="file" name="post_image" id="post_image"/>
                        </div>
                        <div class="post_content">
                            <label class="prompt" for="content" id="content_prompt_text">Content</label>
                        <textarea name="post_content" id="post_content" rows="3" cols="15"
                                  aria-autocomplete="none" style="overflow-y: auto; overflow-x: hidden;"><?php echo $post->post_content ?></textarea>
                        </div>


                        <div class="post_submit" style="padding: 15px 0 0 0;">
                            <input type="submit" name="save" id="save-post" class="button button-primary" value="Save post" />
                        </div>
                    </form>

                </div>

<?php

    endwhile;
endif;
wp_reset_postdata(); ?>