<?php
$stats = Filejet_Admin::get_statistics_data();
$breakdown = $stats['breakdown'];
$currentPeriod = \DateTime::createFromFormat('Y-n', "{$stats['year']}-{$stats['month']}");
?>
<h3>Stats <?= $currentPeriod->format('F Y') ?></h3>
<ul>
    <li><img src="<?php echo esc_url( plugins_url( '../images/master-images.svg', __FILE__ ) ); ?>" alt=""> Master accessed <?= $breakdown['masterImageAccessed'] ?></li>
    <li><img src="<?php echo esc_url( plugins_url( '../images/renders.svg', __FILE__ ) ); ?>" alt=""> Mutations <?= $breakdown['mutationAccessed'] ?></li>
    <li><img src="<?php echo esc_url( plugins_url( '../images/total-requests.svg', __FILE__ ) ); ?>" alt=""> Bandwidth <?= Filejet_Admin::format_bytes($breakdown['bandwidth']) ?></li>
    <li><img src="<?php echo esc_url( plugins_url( '../images/avg-response.svg', __FILE__ ) ); ?>" alt=""> Avg. response (ms) <?= round($breakdown['averageResponseTime']) ?> ms</li>
</ul>
