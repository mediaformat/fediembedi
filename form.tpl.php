<?php
define("ACCOUNT_CONNECTED",isset($account) && $account !== null);
define("ADVANCED_VIEW",false);
?>


<div class="wrap">
	<h1><?php esc_html_e( 'FediEmbedi Configuration', 'fediembedi' ); ?></h1>
	<br>

	<!-- <a href="" target="_blank" class="github-icon" target="_blank">
		<svg aria-hidden="true" class="octicon octicon-mark-github" height="32" version="1.1" viewBox="0 0 16 16" width="32"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"></path></svg>
	</a>
	<a href="" target="_blank"><img src="<?php echo plugins_url( 'img/paypal.png', __FILE__ );?>" style="height:30px;"></a>
	<a href="" target="_blank"><img src="<?php echo plugins_url( 'img/patron.png', __FILE__ );?>" style="height:30px;"></a>
	<a href="" target="_blank"><img src="<?php echo plugins_url( 'img/donate.svg', __FILE__ );?>"></a> -->
<br>
<br>
	<form method="POST">
		<?php wp_nonce_field( 'fediembedi-configuration' ); ?>
		<div style="display:<?php echo !ACCOUNT_CONNECTED ? "block":"none"?>">
				<input type="text" id="instance" name="instance" size="80" value="<?php esc_attr_e( $instance ); ?>" list="mInstances">
				<input class="button button-primary" type="submit" value="<?php esc_attr_e( 'Connect to your Mastodon instance', 'fediembedi' ); ?>" name="save" id="save">
		</div>
		<div style="display:<?php echo ACCOUNT_CONNECTED ? "block" : "none"?>">
				<div class="account">
				<?php if(ACCOUNT_CONNECTED): ?>
						<a href="<?php echo $account->url ?>" target="_blank"><img class="m-avatar" src="<?php echo $account->avatar ?>"></a>
				<?php endif ?>
					<div class="details">
						<?php if(ACCOUNT_CONNECTED): ?>
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

		<?php if(ACCOUNT_CONNECTED): ?>
			<div class="clear">
				<input class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save configuration', 'fediembedi' ); ?>" name="save" id="save">
			</div>
		<?php endif ?>

	</form>
<?php
	//require("instanceList.php")
?>
</div>
