<?php
$stats = Filejet_Admin::get_statistics_data();
$breakdown = $stats['breakdown'];
$currentPeriod = \DateTime::createFromFormat('Y-n', "{$stats['year']}-{$stats['month']}");
?>
<img src="<?php echo esc_url(plugins_url('../images/logo-white.svg', __FILE__)); ?>" alt="" class="logo-white">
<h3>Stats <?= $currentPeriod->format('F Y') ?></h3>
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