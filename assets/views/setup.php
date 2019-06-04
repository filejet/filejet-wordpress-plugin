<?php
$active_tab = array_key_exists('tab', $_GET) && Filejet_Admin::tab_is_allowed($_GET['tab']) ? $_GET['tab'] : Filejet_Admin::TAB_OVERVIEW;
$year = array_key_exists('year', $_GET) ? $_GET['year'] : null;
$month = array_key_exists('month', $_GET) ? $_GET['month'] : null;
?>
<div id="filejet-plugin-container">
    <div class="filejet-header">
        <img class="filejet-header__logo"
             src="<?php echo esc_url(plugins_url('../images/logo-filejet.svg', __FILE__)); ?>" alt="Filejet"/>
    </div>
    <div class="filejet-lower">
        <div class="filejet-box">
            <?php if (Filejet::get_api_key()) { ?>
                <?php Filejet_Admin::display_status(); ?>
            <?php } ?>
            <?php if (!empty($notices)) { ?>
                <?php foreach ($notices as $notice) { ?>
                    <?php Filejet::view('notice', $notice); ?>
                <?php } ?>
            <?php } ?>

        </div>
        <br/>

        <div class="spacer-top">
            <div class="wrap filejet-wrap">
                <h2 class="nav-tab-wrapper">
                    <?php foreach (Filejet_Admin::NAV as $tab => $label): ?>
                    <a href="<?= Filejet_Admin::get_page_url_with_tab($tab) ?>"
                       class="nav-tab<?= $active_tab === $tab ? ' nav-tab-active' : '' ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </h2>

                <div class="filejet-box">

                    <?php if ($active_tab === Filejet_Admin::TAB_OVERVIEW): ?>
                        <h2>Overview</h2>
                        <p>Enter the class of your image and the manual mutation you want FileJet to use. (by default, FileJet will try to guess the mutation). For more info on the mutations available, see the <a href="https://filejet.io/api-reference" target="_blank">api reference</a></p>
                    <?php
                        $stats = Filejet_Admin::get_statistics_data($year, $month);
                        $breakdown = $stats['breakdown'];
                        $graphData = $stats['graphData'];
                        $labels = array_column($graphData['masterImageAccessed'], 'day');
                        $masterImageAccessed = array_column($graphData['masterImageAccessed'], 'value');
                        $mutationAccessed = array_column($graphData['mutationAccessed'], 'value');
                        $currentPeriod = \DateTime::createFromFormat('Y-n', "{$stats['year']}-{$stats['month']}");
                        $previousPeriod = clone $currentPeriod;
                        $nextPeriod = clone $currentPeriod;
                        $previousPeriod->modify('-1 month');
                        $nextPeriod->modify('+1 month');
                        $disableNextPeriod = (new \DateTime())->format('Yn') === $currentPeriod->format('Yn');
                        ?>
                        <h3>Stats <?= $currentPeriod->format('F Y') ?></h3>
                        <div>
                            <a href="<?= Filejet_Admin::get_page_url_with_tab($active_tab, ['year' => $previousPeriod->format('Y'), 'month' => $previousPeriod->format('n')]) ?>"><span class="dashicons dashicons-arrow-left-alt2"></span></a>
                            <?php if(false === $disableNextPeriod): ?>
                            <a href="<?= Filejet_Admin::get_page_url_with_tab($active_tab, ['year' => $nextPeriod->format('Y'), 'month' => $nextPeriod->format('n')]) ?>"><span class="dashicons dashicons-arrow-right-alt2"></span></a>
                            <?php endif; ?>
                        </div>
                        <ul>
                            <li><img src="<?php echo esc_url( plugins_url( '../images/master-images.svg', __FILE__ ) ); ?>" alt=""> Master accessed <?= $breakdown['masterImageAccessed'] ?></li>
                            <li><img src="<?php echo esc_url( plugins_url( '../images/renders.svg', __FILE__ ) ); ?>" alt=""> Mutations <?= $breakdown['mutationAccessed'] ?></li>
                            <li><img src="<?php echo esc_url( plugins_url( '../images/total-requests.svg', __FILE__ ) ); ?>" alt=""> Bandwidth <?= Filejet_Admin::format_bytes($breakdown['bandwidth']) ?></li>
                            <li><img src="<?php echo esc_url( plugins_url( '../images/avg-response.svg', __FILE__ ) ); ?>" alt=""> Avg. response (ms) <?= round($breakdown['averageResponseTime']) ?> ms</li>
                        </ul>
                    <div>
                        <canvas id="stats" width="400" height="200"></canvas>
                        <script type="text/javascript">

                            function transparentize(color, opacity) {
                                var alpha = opacity === undefined ? 0.5 : 1 - opacity;
                                return Color(color).alpha(alpha).rgbString();
                            }

                            var myChart = new Chart('stats', {
                                type: 'line',
                                data: {
                                    labels: [<?= implode(',', $labels) ?>],
                                    datasets: [
                                        {
                                        label: 'Master image accessed',
                                        legend: 'bottom',
                                        data: [<?= implode(',', $masterImageAccessed) ?>],
                                        backgroundColor: transparentize('#8854d0'),
                                        borderColor: '#8854d0',
                                        pointBackgroundColor: '#8854d0'
                                    },
                                        {
                                        label: 'Mutated image accessed',
                                        legend: 'bottom',
                                        data: [<?= implode(',', $mutationAccessed) ?>],
                                        backgroundColor: transparentize('#20bf6b'),
                                        borderColor: '#20bf6b',
                                        pointBackgroundColor: '#20bf6b'
                                    }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    tooltips: {
                                        mode: 'index',
                                    },
                                    hover: {
                                        mode: 'index'
                                    },
                                    legend: {
                                        display: true,
                                        position: 'bottom'
                                    },
                                    scales: {
                                        xAxes: [{
                                            gridLines: {
                                                display:false
                                            }
                                        }],
                                        yAxes: [{
                                            ticks: {
                                                beginAtZero: true,
                                            }
                                        }]
                                    }
                                }
                            });
                        </script>
                    </div>
                    <?php elseif ($active_tab === Filejet_Admin::TAB_MUTATIONS): ?>
                        <h2><?php esc_html_e('Mutations', 'filejet'); ?></h2>
                        <p><?php echo 'Enter the class of your image and the manual mutation you want FileJet to use. (by default, FileJet will try to guess the mutation). For more info on the mutations available, see the <a href="https://filejet.io/api-reference" target="_blank">api reference</a>' ?></p>

                        <?php if ($config = Filejet::get_mutations()) { ?>
                            <?php foreach ($config as $key => $value) { ?>
                                <form action="<?php echo esc_url(Filejet_Admin::get_page_url_with_tab($active_tab)); ?>"
                                      method="post">
                                    <?php wp_nonce_field(Filejet_Admin::NONCE) ?>
                                    <input type="hidden" name="action"
                                           value="<?php echo Filejet_Action::DELETE_MUTATION_SETTING ?>">
                                    <input type="hidden" name="class" value="<?php echo $key ?>">
                                    <p>
                                        <span style="width: 80%; display: inline-block;"><?php echo $key ?>(<?php echo $value ?>)</span>
                                        <input type="submit" name="submit" id="submit"
                                               class="filejet-button filejet-danger"
                                               value="<?php esc_attr_e('Delete', 'filejet'); ?>"></p>
                                </form>
                            <?php } ?>

                        <?php } ?>

                        <form action="<?php echo esc_url(Filejet_Admin::get_page_url_with_tab($active_tab)); ?>"
                              method="post" class="filejet_form filejet_config_form">
                            <?php wp_nonce_field(Filejet_Admin::NONCE) ?>
                            <input type="hidden" name="action"
                                   value="<?php echo Filejet_Action::ADD_MUTATION_SETTING ?>">
                            <p class="filejet-input-wrapper">
                                <label for="class">Class</label>
                                <input type="text" id="class" name="class" value="" class="regular-text code">
                            </p>
                            <p class="filejet-input-wrapper">
                                <label for="mutation">Mutation</label>
                                <input id="mutation" name="mutation" type="text" size="32" value=""
                                       class="regular-text code">
                            </p>
                            <p>
                                <input type="submit" name="submit" id="submit" class="filejet-button fj_button"
                                       value="<?php esc_attr_e('Add mutation', 'filejet'); ?>">
                            </p>
                        </form>
                    <?php elseif ($active_tab === Filejet_Admin::TAB_CONFIGURATION): ?>
                        <h2><?php esc_html_e('Ignore list', 'filejet'); ?></h2>
                        <p><?php echo esc_html__('Add classes of the images that you wish not to be handled by FileJet.', 'filejet') ?></p>
                        <?php if ($config = Filejet::get_ignored()) { ?>
                            <?php foreach ($config as $key => $value) { ?>
                                <form action="<?php echo esc_url(Filejet_Admin::get_page_url_with_tab($active_tab)); ?>"
                                      method="post">
                                    <?php wp_nonce_field(Filejet_Admin::NONCE) ?>
                                    <input type="hidden" name="action"
                                           value="<?php echo Filejet_Action::DELETE_IGNORE_SETTING ?>">
                                    <input type="hidden" name="class" value="<?php echo $key ?>">
                                    <p><span style="width: 80%;display: inline-block;"><?php echo $key ?></span>
                                        <input type="submit" name="submit" id="submit"
                                               class="filejet-button filejet-danger"
                                               value="<?php esc_attr_e('Delete', 'filejet'); ?>">
                                    </p>
                                </form>
                            <?php } ?>

                        <?php } ?>

                        <form action="<?php echo esc_url(Filejet_Admin::get_page_url_with_tab($active_tab)); ?>"
                              method="post" class="filejet_form filejet_config_form">
                            <?php wp_nonce_field(Filejet_Admin::NONCE) ?>
                            <input type="hidden" name="action"
                                   value="<?php echo Filejet_Action::ADD_IGNORE_SETTING ?>">
                            <p class="filejet-input-wrapper">
                                <label for="class">Class</label>
                                <input type="text" id="class" name="class" value="" class="regular-text code">
                            </p>
                            <p>
                                <input type="submit" name="submit" id="submit" class="filejet-button fj_button"
                                       value="<?php esc_attr_e('Add to ignore list', 'filejet'); ?>">
                            </p>
                        </form>
                    <?php elseif ($active_tab === Filejet_Admin::TAB_LAZY_LOAD): ?>
                        <h2><?php esc_html_e('Image attributes', 'filejet'); ?></h2>
                        <p>Add attributes of the images that you wish to be handled by FileJet. By default FileJet handles <code>src</code> and
                            <code>srcset</code> attributes.</p>
                        <?php if ($config = Filejet::get_lazy_loaded()) { ?>
                            <?php foreach ($config as $key => $value) { ?>
                                <form action="<?php echo esc_url(Filejet_Admin::get_page_url_with_tab($active_tab)); ?>"
                                      method="post">
                                    <?php wp_nonce_field(Filejet_Admin::NONCE) ?>
                                    <input type="hidden" name="action"
                                           value="<?php echo Filejet_Action::DELETE_LAZY_LOAD_SETTING ?>">
                                    <input type="hidden" name="class" value="<?php echo $key ?>">
                                    <p><span style="width: 80%;display: inline-block;"><?= $key ?></span>
                                        <input type="submit" name="submit" id="submit"
                                               class="filejet-button filejet-danger"
                                               value="<?php esc_attr_e('Delete', 'filejet'); ?>">
                                    </p>
                                </form>
                            <?php } ?>

                        <?php } ?>

                        <form action="<?php echo esc_url(Filejet_Admin::get_page_url_with_tab($active_tab)); ?>"
                              method="post" class="filejet_form filejet_config_form">
                            <?php wp_nonce_field(Filejet_Admin::NONCE) ?>
                            <input type="hidden" name="action"
                                   value="<?php echo Filejet_Action::ADD_LAZY_LOAD_SETTING ?>">
                            <p class="filejet-input-wrapper">
                                <label for="src">Attribute</label>
                                <input type="text" id="attribute" name="attribute" value="" class="regular-text code">
                            </p>
                            <p>
                                <input type="submit" name="submit" id="submit" class="filejet-button fj_button"
                                       value="<?php esc_attr_e('Add attribute', 'filejet'); ?>">
                            </p>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="filejet-danger-zone">
                <h2>Danger zone</h2>
                <p>To clear your FileJet credentials follow the link below</p>

                <form action="<?php echo esc_url(Filejet_Admin::get_page_url()); ?>" method="post">
                    <?php wp_nonce_field(Filejet_Admin::NONCE) ?>
                    <input type="hidden" name="action" value="enter-key">
                    <input id="storageId" name="storageId" type="hidden" value="">
                    <input id="key" name="key" type="hidden" size="32" value="">
                    <input id="secret" name="secret" type="hidden" size="64" value="">
                    <input type="submit" name="submit" id="submit" class="filejet-button filejet-danger"
                           value="<?php esc_attr_e('Clear credentials', 'filejet'); ?>">
                </form>
            </div>
        </div>
    </div>
</div>
