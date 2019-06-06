<?php
$stats = Filejet_Admin::get_statistics_data();
if($stats !== null):
$breakdown = $stats['breakdown'];
$currentPeriod = \DateTime::createFromFormat('Y-n', "{$stats['year']}-{$stats['month']}");
?>
<h3>Stats > <?= $currentPeriod->format('F Y') ?></h3>
<ul>
    <li>
        <div class="stats-item">
            <img src="<?php echo esc_url(plugins_url('../images/master-images.svg', __FILE__)); ?>" alt="">
            <span>
               <strong><?= $breakdown['masterImageAccessed'] ?></strong>
               <strong>Master accessed</strong>
              </span>
        </div>
    </li>
    <li>
        <div class="stats-item">
        <img src="<?php echo esc_url(plugins_url('../images/renders.svg', __FILE__)); ?>" alt="">
        <span>
            <strong><?= $breakdown['mutationAccessed'] ?></strong>
        <strong>Mutations</strong>
            </span>
        </div>
    </li>
    <li>
        <div class="stats-item">
        <img src="<?php echo esc_url(plugins_url('../images/total-requests.svg', __FILE__)); ?>" alt="">
        <span>
            <strong><?= Filejet_Admin::format_bytes($breakdown['bandwidth']) ?></strong>
                <strong>Bandwidth</strong>
            </span>
        </div>
    </li>
    <li>
        <div class="stats-item">
        <img src="<?php echo esc_url(plugins_url('../images/avg-response.svg', __FILE__)); ?>" alt="">
        <span>
        <strong><?= round($breakdown['averageResponseTime']) ?> ms</strong>
        <strong>Avg. response (ms)</strong>
        </span>
        </div>
    </li>
</ul>
<?php
else:
?>
    <form name="filejet_activate" action="<?php echo esc_url( Filejet_Admin::get_page_url() ); ?>" method="POST">
                <div class="fj_description"><?php _e('<strong>You have made a wise decision</strong> - configure FileJet to speed up your site now!', 'filejet');?></div><br><br>
                <div class="fj_button_container">
                    <div class="fj_button_border">
                        <input type="submit" class="fj_button" value="<?php esc_attr_e( 'Set up your FileJet account', 'filejet' ); ?>" />
                    </div>
                </div>
    </form>
<?php
    endif;
    ?>
