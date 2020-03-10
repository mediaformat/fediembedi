<!-- pixelfed -->
<div class="scrollable" style="height: <?php echo $height; ?>;">
  <div role="feed" class="embed-card pixelfed">
    <div class="pixelfed-inner card status-card-embed card-md-rounded-0 border">
      <?php if($show_header): ?>
      <div class="pixelfed-header card-header d-inline-flex align-items-center justify-content-between bg-white">
        <div class="pixelfed-account">
          <img src="<?php echo $account->avatar; ?>" height="32px" width="32px" style="border-radius: 32px;">
          <a href="<?php echo $account->url; ?>" class="username font-weight-bold pl-2 text-dark" rel="noreferrer noopener" target="_blank"><?php echo $account->username; ?></a>
        </div>
        <div class="pixelfed-instance">
          <a class="small font-weight-bold text-muted pr-1" href="<?php echo $instance_url; ?>"><?php echo parse_url($instance_url, PHP_URL_HOST); ?></a>
          <img src="<?php echo plugin_dir_url( __FILE__ ) . '../img/pixelfed.svg';?>" width="26px" loading="lazy">
        </div>
      </div>
      <?php endif; ?>
      <div class="pixelfed-body card-body pb-1">
        <div class="pixelfed-meta d-flex justify-content-between align-items-center">
          <div class="text-center">
            <div class="mb-0 font-weight-bold prettyCount"><?php echo $account->statuses_count; ?></div>
            <div class="mb-0 text-muted text-uppercase small font-weight-bold"><?php _e('Posts', 'fediembedi'); ?></div>
          </div>
          <div class="text-center">
            <div class="mb-0 font-weight-bold prettyCount"><?php echo $account->followers_count; ?></div>
            <div class="mb-0 text-muted text-uppercase small font-weight-bold"><?php _e('Followers', 'fediembedi'); ?></div>
          </div>
          <div class="text-center">
            <div class="mb-0 font-weight-bold prettyCount"><?php echo $account->following_count; ?></div>
            <div class="mb-0 text-muted text-uppercase small font-weight-bold"><?php _e('Following', 'fediembedi'); ?></div>
          </div>
          <div class="text-center">
            <div class="mb-0">
              <a href="<?php echo $instance_url . '/i/intent/follow?user='. $account->acct; ?>" class="pixelfed-follow btn btn-primary btn-sm py-1 px-4 text-uppercase font-weight-bold" target="_blank"><?php _e('Follow', 'fediembedi'); ?></a>
            </div>
          </div>
        </div>
        <div class="pixelfed-images row mt-4 mb-1 px-1">
          <?php foreach ($status as $statut) { ?>
            <article class="col-4 mt-2 px-0"><?php
                if (!empty($statut->media_attachments[0]->preview_url) && $statut->media_attachments[0]->type === 'image'): ?>
                  <a href="<?php echo $statut->url; ?>" class="card info-overlay card-md-border-0 px-1 shadow-none" target="_blank" rel="noopener">
                    <div class="square">
                      <div style='background-image: url(<?php echo $statut->media_attachments[0]->preview_url; ?>);' class='square-content' alt='<?php $statut->media_attachments[0]->description; ?>'></div>
                      <div class="info-text-overlay"></div>
                    </div>
                  </a><?php
                elseif($statut->media_attachments[0]->type === 'video'):
                  echo "<video src=" . $attachment->url . " controls poster='" . $statut->media_attachments[0]->preview_url . "' class='media-gallery__item' alt=" . $statut->media_attachments[0]->description . ">";
                endif; ?>
            </article>
          <?php } ?>
        </div>
      </div>
      <div class="pixelfed-footer card-footer bg-white">
        <div class="text-center mb-0">
          <a href="<?php echo $status[0]->account->url; ?>" class="font-weight-bold" target="_blank" rel="noreferrer noopener"><?php _e('View More Posts', 'fediembedi'); ?></a>
        </div>
      </div>
    </div>
  </div>
</div>
