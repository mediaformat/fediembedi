<?php
define("FEDI_CONNECTED",isset($account) && $account !== null);
?>


<div class="wrap">
	<h1><?php esc_html_e( 'FediEmbedi Configuration', 'fediembedi' ); ?></h1>
	<br>
<br>
<br>
	<form method="POST">
		<?php wp_nonce_field( 'fediembedi-configuration' ); ?>
		<div style="display:<?php echo !FEDI_CONNECTED ? "block":"none"?>">
				<input type="text" class="widefat instance_url" id="instance" name="instance" size="80" value="<?php esc_url_raw( $instance, 'https' ); ?>" list="mInstances">
				<select class="widefat instance_type" id="instance_type" name="instance_type" value="<?php sanitize_key( $instance_type ); ?>">
					<option value="mastodon">Mastodon/Pleroma</option>
					<option value="pixelfed">Pixelfed</option>
				</select>
				<input class="button button-primary" type="submit" value="<?php esc_attr_e( 'Connect to your instance', 'fediembedi' ); ?>" name="save" id="save">
				<br><small><?php _e( 'The currently supported software are Mastodon, Pleroma, Pixelfed.', 'fediembedi' ); ?></small><br>
				<p><?php _e( "Don't have an account?", 'fediembedi' ); ?></p>
				<p><span class="mastodon"></span> Visit <a href="https://joinmastodon.org/" rel="noreferrer noopener" target="_blank" class="">joinmastodon.org</a> to find an instance.</p>
				<p><span class="pixelfed"></span> Visit <a href="https://pixelfed.org/join" rel="noreferrer noopener" target="_blank" class="">pixelfed.org/join</a> to find an instance.</p>
		</div>
		<div style="display:<?php echo FEDI_CONNECTED ? "block" : "none"?>">
				<div class="account">
						<a href="<?php echo $account->url ?>" target="_blank"><img class="m-avatar" src="<?php echo $account->avatar ?>"></a>
					<div class="details">
						<?php if(FEDI_CONNECTED): ?>
							<div class="connected"><?php esc_html_e( 'Connected as', 'fediembedi' ); ?>&nbsp;<?php echo $account->username ?></div>
							<a class="link" href="<?php echo $account->url ?>" target="_blank"><?php echo $account->url ?></a>

							<p><a href="<?php echo $_SERVER['REQUEST_URI'] . '&disconnect' ?>" class="button"><?php esc_html_e( 'Disconnect', 'fediembedi' ); ?></a>

						<?php else: ?>
							<div class="disconnected"><?php esc_html_e( 'Disconnected', 'fediembedi' ); ?></div>
						<?php endif ?>
					</div>
					<div class="separator"></div>
				</div>
		</div>
		<div class="clear"></div>

	</form>
</div>
