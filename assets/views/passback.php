<form name="filejet_activate" action="https://app.filejet.io/get/" method="POST" target="_blank">
	here
	<input type="hidden" name="passback_url" value="<?php echo esc_url( Fielejet_Admin::get_page_url() ); ?>"/>
	<input type="hidden" name="blog" value="<?php echo esc_url( get_option( 'home' ) ); ?>"/>
	<input type="hidden" name="redirect" value="<?php echo isset( $redirect ) ? $redirect : 'plugin-signup'; ?>"/>
	<input type="submit" class="<?php echo isset( $classes ) && count( $classes ) > 0 ? implode( ' ', $classes ) : 'filejet-button filejet-is-primary';?>" value="<?php echo esc_attr( $text ); ?>"/>
	here
</form>