<?php
define("FEDI_CONNECTED",isset($account) && $account !== null);
define("FEDI_MSTDN_CONNECTED",isset($mastodon_account) && $mastodon_account !== null);
define("FEDI_PXLFD_CONNECTED",isset($pixelfed_account) && $pixelfed_account !== null);
?>
<div class="wrap">
	<h1><?php esc_html_e( 'FediEmbedi Configuration', 'fediembedi' ); ?></h1>
	<p><?php _e( 'The currently supported software are Mastodon, Pleroma, Pixelfed, PeerTube.', 'fediembedi' ); ?></p>
	<form method="POST">
		<?php wp_nonce_field( 'fediembedi-configuration' ); ?>

		<div style="display:<?php echo !FEDI_MSTDN_CONNECTED ? "block":"none"?>">
				<p><span class="mastodon"></span>
					<input type="hidden" name="instance_type" value="mastodon">
					<input type="text" class="widefat instance_url" id="mastodon_instance" name="instance" size="60" value="<?php esc_url_raw( $mastodon_instance, 'https' ); ?>">
					<input class="button button-primary" type="submit" value="<?php _e( 'Connect to your instance to enable the widget', 'fediembedi' ); ?>" name="save" id="save_mastodon"><br></p>
					<p><?php _e( "Don't have an account?", 'fediembedi' ); ?> Visit <a href="https://joinmastodon.org/" rel="noreferrer noopener" target="_blank" class="">joinmastodon.org</a> to find an instance.</p>
		</div>
		<div style="display:<?php echo FEDI_MSTDN_CONNECTED ? "block" : "none"?>">
				<div class="account">
						<a href="<?php echo $mastodon_account->url ?>" target="_blank"><img class="m-avatar" src="<?php echo $mastodon_account->avatar ?>"><span class="mastodon"></span></a>
					<div class="details">
						<?php if(FEDI_MSTDN_CONNECTED): ?>
							<div class="connected"><?php echo $mastodon_account->username ?></div>
							<a class="link" href="<?php echo $mastodon_account->url ?>" target="_blank"><?php echo $mastodon_account->url ?></a>

							<p><a href="<?php echo $_SERVER['REQUEST_URI'] . '&fediembedi-disconnect=mastodon' ?>" class="button"><?php esc_html_e( 'Disconnect', 'fediembedi' ); ?></a>

						<?php else: ?>
							<div class="disconnected"><?php esc_html_e( 'Disconnected', 'fediembedi' ); ?></div>
						<?php endif ?>
					</div>
					<div class="separator"></div>
				</div><div class="clear"></div>
		</div>
	</form>
	<form method="POST">
		<?php wp_nonce_field( 'fediembedi-configuration' ); ?>
		<div style="display:<?php echo !FEDI_PXLFD_CONNECTED ? "block":"none"?>">
				<p><span class="pixelfed"></span>
					<input type="hidden" name="instance_type" value="pixelfed">
					<input type="text" class="widefat instance_url" id="pixlefed_instance" name="instance" size="60" value="<?php esc_url_raw( $pixlefed_instance, 'https' ); ?>">
					<input class="button button-primary" type="submit" value="<?php _e( 'Connect to your instance to enable the widget', 'fediembedi' ); ?>" name="save" id="save_pixelfed"></p>
					<p><?php _e( "Don't have an account?", 'fediembedi' ); ?> Visit <a href="https://pixelfed.org/join" rel="noreferrer noopener" target="_blank" class="">pixelfed.org/join</a> to find an instance.</p>
		</div>
		<div style="display:<?php echo FEDI_PXLFD_CONNECTED ? "block" : "none"?>">
				<div class="account">
						<a href="<?php echo $pixelfed_account->url ?>" target="_blank"><img class="m-avatar" src="<?php echo $pixelfed_account->avatar ?>"><span class="pixelfed"></span></a>
					<div class="details">
						<?php if(FEDI_PXLFD_CONNECTED): ?>
							<div class="connected"><?php echo $pixelfed_account->username ?></div>
							<a class="link" href="<?php echo $pixelfed_account->url ?>" target="_blank"><?php echo $pixelfed_account->url ?></a>

							<p><a href="<?php echo $_SERVER['REQUEST_URI'] . '&fediembedi-disconnect=pixelfed' ?>" class="button"><?php esc_html_e( 'Disconnect', 'fediembedi' ); ?></a>

						<?php else: ?>
							<div class="disconnected"><?php esc_html_e( 'Disconnected', 'fediembedi' ); ?></div>
						<?php endif ?>
					</div>
					<div class="separator"></div>
				</div><div class="clear"></div>
		</div>
		<div>
				<p><span class="peertube"></span> Widget ready!
					 Visit <a href="https://joinpeertube.org" rel="noreferrer noopener" target="_blank" class="">joinpeertube.org</a> to find an instance.</p>
		</div>
		<div class="clear"></div>

	</form>
</div>
