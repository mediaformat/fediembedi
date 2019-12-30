<!-- pixelfed -->
<div class="scrollable" style="height: <?php echo $height; ?>;">
  <div role="feed">
    <?php if($show_header): ?>
    <div class="account-timeline__header">
      <div class="account__header">
        <div class="account__header__bar">
          <div class="account__header__tabs">
            <a href="<?php echo $status[0]->account->url; ?>" class="avatar" rel="noreferrer noopener" target="_blank">
              <div class="account__avatar" style="margin-top: 50px; width:90px; height: 90px; background-image: url('<?php echo $status[0]->account->avatar; ?>'); background-size: cover;"></div>
            </a>
            <div class="spacer"></div>
            <div class="account__header__tabs__buttons">
              <a href="<?php echo $status[0]->account->url; ?>" rel="noreferrer noopener" class="button logo-button">Follow</a>
            </div>
          </div>
          <div class="account__header__tabs__name">
            <h1>
              <span><?php echo $status[0]->account->display_name; ?></span>
              <small><a href="" target="_blank" rel="noreferrer noopener"><?php echo $status[0]->account->url; ?></a></small>
            </h1>
          </div>
          <div class="account__header__extra">
            <div class="account__header__bio">
              <div class="account__header__content">
                <?php echo $status[0]->account->note; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <?php foreach ($status as $statut) { ?>
      <article>
        <div tabindex="-1">
          <div class="status__wrapper status__wrapper-public focusable" tabindex="0">
            <div class="status__content"><?php

              if(!empty($statut->media_attachments)):
                foreach ($statut->media_attachments as $attachment) {
                  if (!empty($attachment->preview_url) && $attachment->type === 'image'): ?>
                    <a href="<?php echo $statut->url; ?>" class="" target="_blank" rel="noopener">
                      <img src='<?php echo $attachment->preview_url; ?>' class='media-gallery__item' alt='<?php $attachment->description; ?>' loading='lazy'>
                    </a><?php
                  elseif($attachment->type === 'video'):
                    echo "<video src=" . $attachment->url . " controls poster='" . $attachment->preview_url . "' class='media-gallery__item' alt=" . $attachment->description . ">";
                  endif;
                }
              endif;
             ?></div>
          </div>
        </div>
      </article>
    <?php } ?>
  </div>
</div>
