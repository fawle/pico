<?php

foreach ($results as $mission) { ?>
<div class="mission entry-content" >
    <div><a href="<?php echo home_url() . '/mission-control/' . $mission->mission_name; ?>">
            Mission: <?php echo $mission->display_name; ?>
        </a>
    </div>
    <div>Picture</div>
    <div>Status: <?php echo $mission->status? 'Active' : 'Ended' ?> </div>

</div>

<?php } ?>