<?php if ( $type == 'plugin' ) :?>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
	<form name="filejet_activate" action="<?php echo esc_url( Filejet_Admin::get_page_url() ); ?>" method="POST">
		<div class="filejet_activate">
            <div class="img-wrapper">
            <img src="<?php echo esc_url( plugins_url( '../images/rocket.svg', __FILE__ ) ); ?>" alt="Rocket">
            </div>
            <div class="wrapper">
            <div class="fj_description"><?php _e('<strong>You have made a wise decision</strong> - configure FileJet to speed up your site now!', 'filejet');?></div>
			<div class="fj_button_container">
				<div class="fj_button_border">
					<input type="submit" class="fj_button" value="<?php esc_attr_e( 'Set up your FileJet account', 'filejet' ); ?>" />
				</div>
			</div>
            </div>
		</div>
	</form>
</div>
<?php elseif ( $type == 'service-disruption' ) :?>
<div class="filejet-alert filejet-critical">
	<h3 class="filejet-key-status failed"><?php esc_html_e("Your site can&#8217;t connect to the FileJet servers.", 'filejet'); ?></h3>
</div>
<?php elseif ( $type == 'service-ok'&& Filejet::get_api_key() ) :?>
<div class="filejet-alert filejet-active">
	<h3 class="filejet-key-status"><?php esc_html_e("FileJet setup and running.", 'filejet'); ?></h3>
	<p class="filejet-description"><?php printf( __('Check your savings on the stats page.', 'filejet')); ?></p>
</div>
<div class="filejet-alert filejet-success">
    <h3><span class="dashicons dashicons-yes"></span> Your storage ID : <?= Filejet::get_storage_id() ?></h3>
</div>
<?php  endif;?>
