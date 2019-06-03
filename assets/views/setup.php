<?php
$active_tab = array_key_exists('tab', $_GET) && Filejet_Admin::tab_is_allowed($_GET['tab']) ? $_GET['tab'] : Filejet_Admin::TAB_MUTATIONS;
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
                    <a href="<?= Filejet_Admin::get_page_url_with_tab(Filejet_Admin::TAB_MUTATIONS) ?>"
                       class="nav-tab<?= $active_tab === Filejet_Admin::TAB_MUTATIONS ? ' nav-tab-active' : '' ?>">Mutations</a>
                    <a href="<?= Filejet_Admin::get_page_url_with_tab(Filejet_Admin::TAB_CONFIGURATION) ?>"
                       class="nav-tab<?= $active_tab === Filejet_Admin::TAB_CONFIGURATION ? ' nav-tab-active' : '' ?>">Ignore
                        list</a>
                    <a href="<?= Filejet_Admin::get_page_url_with_tab(Filejet_Admin::TAB_LAZY_LOAD) ?>"
                       class="nav-tab<?= $active_tab === Filejet_Admin::TAB_LAZY_LOAD ? ' nav-tab-active' : '' ?>">Lazy load</a>
                </h2>

                <div class="filejet-box">

                    <?php if ($active_tab === 'mutations'): ?>
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
                                       value="<?php esc_attr_e('Save mutation', 'filejet'); ?>">
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
                        <h2><?php esc_html_e('Lazy load', 'filejet'); ?></h2>
                        <p><?php echo esc_html__('Add attributes of the images that are lazy loaded.', 'filejet') ?></p>
                        <?php if ($config = Filejet::get_lazy_loaded()) { ?>
                            <?php foreach ($config as $key => $value) { ?>
                                <form action="<?php echo esc_url(Filejet_Admin::get_page_url_with_tab($active_tab)); ?>"
                                      method="post">
                                    <?php wp_nonce_field(Filejet_Admin::NONCE) ?>
                                    <input type="hidden" name="action"
                                           value="<?php echo Filejet_Action::DELETE_LAZY_LOAD_SETTING ?>">
                                    <input type="hidden" name="class" value="<?php echo $key ?>">
                                    <p><span style="width: 80%;display: inline-block;">
                                            <strong>src:</strong> <?= $key ?> <?= $value ? "<strong>srcset:</strong> $value" : "" ?></span>
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
                                <label for="src">src*</label>
                                <input type="text" id="src" name="src" value="" class="regular-text code">
                            </p>
                            <p class="filejet-input-wrapper">
                                <label for="srcset">srcset</label>
                                <input type="text" id="srcset" name="srcset" value="" class="regular-text code">
                            </p>
                            <p>
                                <input type="submit" name="submit" id="submit" class="filejet-button fj_button"
                                       value="<?php esc_attr_e('Add to lazy load list', 'filejet'); ?>">
                            </p>
                            <p>* required</p>
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
