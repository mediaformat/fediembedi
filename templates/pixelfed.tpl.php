<!-- pixelfed -->
<div class="fediembedi fediembedi-pixelfed scrollable" <?php if ( !empty( $height ) ) : echo "style='height: $height;'"; endif; ?>>
  <div role="feed" class="embed-card pixelfed">
    <div class="pixelfed-inner card status-card-embed card-md-rounded-0 border">
      <?php if( $show_header ): ?>
      <div class="pixelfed-header card-header d-inline-flex align-items-center justify-content-between bg-white">
        <div class="pixelfed-account">
          <img src="<?php echo $account->avatar; ?>" height="32px" width="32px" style="border-radius: 32px;">
          <a href="<?php echo $account->url; ?>" class="username font-weight-bold pl-2 text-dark" rel="noreferrer noopener" target="_blank"><?php echo $account->username; ?></a>
        </div>
        <div class="pixelfed-instance">
          <a class="small font-weight-bold text-muted pr-1" href="<?php echo $instance_url; ?>"><?php echo parse_url($instance_url, PHP_URL_HOST); ?></a>
          <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/pixelfed.svg';?>" width="26px" loading="lazy">
        </div>
      </div>
      <?php endif; ?>
      <div class="pixelfed-body card-body pb-1">
        <div class="pixelfed-images row mt-4 mb-1 px-1">
          <?php foreach ( $status as $statut ) { ?>
            <article class="col-4 mt-2 px-0"><?php
              $attachment = $statut->media_attachments[0];
                if ( !empty($attachment->preview_url ) && $attachment->type === 'image'): ?>
                  <a href="<?php echo $statut->url; ?>" class="card info-overlay card-md-border-0 px-1 shadow-none" target="_blank" rel="noopener">
                    <div class="square">
                      <div style='background-image: url(<?php echo $attachment->preview_url; ?>);' class='square-content' alt='<?php $attachment->description; ?>'></div>
                      <div class="info-text-overlay"></div>
                    </div>
                  </a><?php
                elseif( $attachment->type === 'video' ): ?>
                  <video src="<?php echo $attachment->url; ?>" controls poster="<?php echo $attachment->preview_url; ?>" class='media-gallery__item' alt="<?php echo $attachment->description; ?>">
                <?php endif; ?>
            </article>
          <?php } ?>
        </div>
      </div>
      <div class="pixelfed-footer card-footer bg-white">
        <div class="text-center mb-0">
          <a href="<?php echo $account->url; ?>" class="font-weight-bold" target="_blank" rel="noreferrer noopener"><?php _e('View More Posts', 'fediembedi'); ?></a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 