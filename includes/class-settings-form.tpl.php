<?php
define("FEDI_MSTDN_CONNECTED", isset( $mastodon_account ) && $mastodon_account !== null );
define("FEDI_PXLFD_CONNECTED", isset( $pixelfed_account ) && $pixelfed_account !== null );
?>
<div class="wrap">
	<h1><?php _e( 'FediEmbedi Configuration', 'fediembedi' ); ?></h1>
	<p><?php _e( 'The currently supported software are Mastodon, Pleroma, Pixelfed, PeerTube.', 'fediembedi' ); ?></p>
	<form method="POST">
		<?php wp_nonce_field( 'fediembedi-configuration' ); ?>

		<div style="display:<?php echo !FEDI_MSTDN_CONNECTED ? "block":"none"?>">
				<p><span class="mastodon"></span>
					<input type="hidden" name="instance_type" value="mastodon">
					<input type="text" class="widefat instance_url" id="mastodon_instance" name="instance" size="60" value="<?php !isset($mastodon_instance)?: esc_url_raw( $mastodon_instance, 'https' ); ?>" placeholder="example.social">
					<input class="button button-primary" type="submit" value="<?php _e( 'Connect to your instance to enable the widget', 'fediembedi' ); ?>" name="save" id="save_mastodon"><br></p>
					<p><?php 
						printf( 
							/* translators: joinmastodon.org */
							wp_kses( 
								__( "Don't have an account? Visit <a href='%s' target='_blank' rel='noreferrer noopener'>joinmastodon.org</a> to find an instance.", 'fediembedi' ), 
								array(  'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) 
							), 
							'https://joinmastodon.org/' 
						); ?></p>
		</div>
		<div style="display:<?php echo FEDI_MSTDN_CONNECTED ? "block" : "none"?>">
				<div class="account">
						<a href="<?php echo $mastodon_account->url ?>" target="_blank"><img class="m-avatar" src="<?php echo $mastodon_account->avatar ?>"><span class="mastodon"></span></a>
					<div class="details">
						<?php if(FEDI_MSTDN_CONNECTED): ?>
							<div class="connected"><?php echo $mastodon_account->username ?></div>
							<a class="link" href="<?php echo $mastodon_account->url ?>" target="_blank"><?php echo $mastodon_account->url ?></a>

							<p><a href="<?php echo $_SERVER['REQUEST_URI'] . '&fediembedi-disconnect=mastodon' ?>" class="button"><?php _e( 'Disconnect', 'fediembedi' ); ?></a>

						<?php else: ?>
							<div class="disconnected"><?php _e( 'Disconnected', 'fediembedi' ); ?></div>
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
					<input type="text" class="widefat instance_url" id="pixlefed_instance" name="instance" size="60" value="<?php !isset( $pixlefed_instance ) ?: esc_url_raw( $pixlefed_instance, 'https' ); ?>" placeholder="example.social">
					<input class="button button-primary" type="submit" value="<?php _e( 'Connect to your instance to enable the widget', 'fediembedi' ); ?>" name="save" id="save_pixelfed"></p>
					<p><?php 
						printf( 
							/* translators: https://pixelfed.org/join */
							wp_kses( 
								__( "Don't have an account? Visit <a href='%s' target='_blank' rel='noreferrer noopener'>pixelfed.org/join</a> to find an instance.", 'fediembedi' ), 
								array(  'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) 
							), 
							'https://pixelfed.org/join'
						); ?></p>
		</div>
		<div style="display:<?php echo FEDI_PXLFD_CONNECTED ? "block" : "none"?>">
				<div class="account">
						<a href="<?php echo $pixelfed_account->url; ?>" target="_blank"><img class="m-avatar" src="<?php echo $pixelfed_account->avatar; ?>"><span class="pixelfed"></span></a>
					<div class="details">
						<?php if(FEDI_PXLFD_CONNECTED): ?>
							<div class="connected"><?php echo $pixelfed_account->username; ?></div>
							<a class="link" href="<?php echo $pixelfed_account->url; ?>" target="_blank"><?php echo $pixelfed_account->url; ?></a>

							<p><a href="<?php echo $_SERVER['REQUEST_URI'] . '&fediembedi-disconnect=pixelfed' ?>" class="button"><?php esc_html_e( 'Disconnect', 'fediembedi' ); ?></a>

						<?php else: ?>
							<div class="disconnected"><?php esc_html_e( 'Disconnected', 'fediembedi' ); ?></div>
						<?php endif ?>
					</div>
					<div class="separator"></div>
				</div><div class="clear"></div>
		</div>
		<div>
			<p><span class="peertube"></span><?php _e('Widget ready!', 'fediembed');
						printf( 
							/* translators: https://joinpeertube.org */
							wp_kses( 
								__( "Don't have an account? Visit <a href='%s' target='_blank' rel='noreferrer noopener'>joinpeertube.org</a> to find an instance.", 'fediembedi' ), 
								array(  'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) 
							), 
							'https://joinpeertube.org'
						); ?></p>
		</div>
		<div class="clear"></div>
	</form>
	<div>
		<p><?php
				printf( 
					/* translators: https://indieweb.social/@MediaFormat */
					wp_kses( 
						__( "For news and updates follow <a href='%s' target='_blank' rel='noreferrer noopener'>indieweb.social/@MediaFormat</a> on the fediverse.", 'fediembedi' ), 
						array(  'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) 
					), 
					'https://indieweb.social/@MediaFormat'
				); ?></p>
	</div>
</div>
