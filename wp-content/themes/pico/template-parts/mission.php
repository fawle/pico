<?php

foreach ($results as $mission) { ?>
<div class="mission entry-content" >
    <div><a href="<?php echo home_url() . '/mission-control/' . $mission->mission_name; ?>">
            Mission: <?php echo $mission->display_name; ?>
        </a>
    </div>
    <div>
        <?php

            if ($mission->pic_url) {
                $src = $mission->pic_url;
            } else {
                if ($mission->status) {
                    $src = get_stylesheet_directory_uri() . '/images/placeholder-active.png';
                } else {
                    $src = get_stylesheet_directory_uri() . '/images/placeholder-ended.png';
                }

            }

        ?>
        <img src="<?php echo $src; ?>"
             name="<?php echo $mission->mission_name;?>"
             alt="<?php echo $mission->mission_name;?>"
             height="100"
             width="100"
             class="mission-image"
        >

    </div>
    <div>Status: <?php echo $mission->status? 'Active' : 'Ended' ?> </div>

</div>

<?php } ?>