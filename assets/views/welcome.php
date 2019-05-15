<div id="filejet-plugin-container">
	<div class="filejet-header">
		<img class="filejet-header__logo" src="<?php echo esc_url( plugins_url( '../images/logo-filejet.svg', __FILE__ ) ); ?>" alt="Filejet" />
	</div>
	<div class="filejet-lower">
		<?php Filejet_Admin::display_status(); ?>
		
		<div class="filejet-box">
			<h2><?php esc_html_e( 'Speed up your website!', 'filejet' ); ?></h2>
			<p><?php esc_html_e( 'Select one of the options below to get started.', 'filejet' ); ?></p>
		</div>
		<div class="filejet-boxes">
			<?php if ( ! Filejet::predefined_credentials() ) { ?>
				<?php if ( $filejet_user && in_array( $filejet_user->status, array( 'active', 'active-dunning', 'no-sub', 'missing', 'cancelled', 'suspended' ) ) ) { ?>
					<div class="filejet-box">
						<h3><?php esc_html_e( 'Activate Filejet' , 'filejet' );?></h3>
						<div class="filejet-right">
							<?php Filejet::view( 'passback', array( 'text' => __( 'Get your API key' , 'filejet' ), 'classes' => array( 'filejet-button', 'filejet-is-primary' ) ) ); ?>
						</div>
						<p><?php esc_html_e( 'Log in or sign up now.', 'filejet' ); ?></p>
					</div>
				<?php } ?>
				<div class="filejet-box">
					<h3><?php esc_html_e( 'Enter your FileJet credentials', 'filejet' ); ?></h3>
					<p><?php esc_html_e( 'If you already have your credentials, enter it bellow.', 'filejet' ); ?></p>
					<form action="<?php echo esc_url( Filejet_Admin::get_page_url() ); ?>" method="post" class="filejet_form">
						<?php wp_nonce_field( Filejet_Admin::NONCE ) ?>
						<input type="hidden" name="action" value="enter-key">
						<p style="width: 100%; display: flex; box-sizing: border-box;">
                            <label for="storageId">Storage ID</label>
							<input id="storageId" name="storageId" type="text" size="6" value="" class="regular-text code" style="flex-grow: 0.6; margin-right: 1rem;">
                        </p>
                        <p style="width: 100%; display: flex; box-sizing: border-box;">
                            <label for="key">API key</label>
							<input id="key" name="key" type="text" size="32" value="" class="regular-text code" style="flex-grow: 2; margin-right: 1rem;">
                        </p>
                        <p style="width: 100%; display: flex; box-sizing: border-box;">
                            <label for="secret">Secret key</label>
							<input id="secret" name="secret" type="text" size="64" value="" class="regular-text code" style="flex-grow: 2; margin-right: 1rem;">
						</p>
						<p style="">
							<input type="submit" name="submit" id="submit" class="filejet-button" value="<?php esc_attr_e( 'Connect with credentials', 'filejet' );?>">
						</p>
					</form>
				</div>

                <br>
                <div class="filejet-box">
                    <h3>Do not have a FileJet account yet?</h3>
                    <p>
                        <?php
                        global $wp;
                        $redirect = admin_url() . 'admin.php?page=' . FILEJET_PLUGIN_BASENAME;
                        ?>
                        <br>
                        <a href="https://app.filejet.io/auth/sign-up?redirect_url=<?= urlencode($redirect) ?>" target="_blank" class="fj_button">Register now</a>
                    </p>
                </div>
			<?php } else { ?>
				<div class="filejet-box">
					<h2><?php esc_html_e( 'Manual Configuration', 'filejet' ); ?></h2>
					<p><?php echo sprintf( esc_html__( 'An Filejet API key has been defined in the %s file for this site.', 'filejet' ), '<code>wp-config.php</code>' ); ?></p>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
