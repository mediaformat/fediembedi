<!-- mastodon -->
<div class="fediembedi fediembedi-mastodon scrollable" <?php if (!empty($height)) : echo "style='height: $height;'"; endif; ?>>
  <div role="feed">
    <?php if($show_header): ?>
    <div class="account-timeline__header">
      <div class="account__header">
        <div class="account__header__image">
          <div class="account__header__info"></div>
          <?php if ($account->header): echo "<img src=" . $account->header . " loading='lazy'>"; endif; ?>
        </div>
        <div class="account__header__bar">
          <div class="account__header__tabs">
            <a href="<?php echo $account->url; ?>" class="avatar" rel="noreferrer noopener" target="_blank">
              <div class="account__avatar" style="width:90px; height: 90px; background-image: url('<?php echo $account->avatar; ?>'); background-size: cover;"></div>
            </a>
            <div class="spacer"></div>
            <div class="account__header__tabs__buttons">
              <a href="<?php echo $account->url; ?>" rel="noreferrer noopener" class="button logo-button"><?php _e('Follow', 'fediembedi'); ?></a>
            </div>
          </div>
          <div class="account__header__tabs__name">
            <h1>
              <span><?php echo apply_filters('fedi_emoji', $account->display_name, $account->emojis); ?></span>
              <small><a href="<?php echo $account->url; ?>" target="_blank" rel="noreferrer noopener"><?php echo $account->url; ?></a></small>
            </h1>
          </div>
          <div class="account__header__extra">
            <div class="account__header__bio">
              <div class="account__header__content">
                <?php echo apply_filters('fedi_emoji', $account->note, $account->emojis); ?>
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
            <div class="status__prepend">
              <?php
                if(!is_null($statut->reblog)): ?>
              <div class="status__prepend-icon-wrapper"><i role="img" class="fa fa-retweet status__prepend-icon fa-fw"></i></div>
            </div><?php
          else: echo '</div>';
            endif; ?>

            <div class="status status-public">
                <div class="status__info">
                  <a href="<?php if(is_null($statut->reblog)): echo $statut->url; else: echo $statut->reblog->url; endif; ?>" class="status__relative-time" target="_blank" rel="noopener">
                    <time datetime="<?php echo $statut->created_at; ?>"><?php
                    printf( _x( '%1$s ago', '%2$s = human-readable time difference', 'fediembedi' ),
                      human_time_diff(
                        wp_date("U", strtotime($statut->created_at))
                      )
                    );
                    ?></time>
                  </a>
                  <a href="<?php if(is_null($statut->reblog)): echo $statut->account->url; else: echo $statut->reblog->account->url; endif; ?>" class="status__display-name" rel="noopener noreferrer" target="_blank">
                    <div class="status__avatar">
                      <div class="account__avatar" style="background-image: url(<?php if(is_null($statut->reblog)): echo $statut->account->avatar; else: echo $statut->reblog->account->avatar; endif; ?>); background-size: 40px; width: 40px; height: 40px;"></div>
                    </div>
                    <span class="display-name"><?php
                    if(is_null($statut->reblog)):
                      echo apply_filters('fedi_emoji', $statut->account->display_name, $statut->account->emojis);
                    else:
                      if(empty($statut->reblog->account->display_name)):
                        echo $statut->reblog->account->username;
                      else:
                        echo apply_filters('fedi_emoji', $statut->reblog->account->display_name, $statut->reblog->account->emojis);
                      endif;
                    endif; ?></span>
                  </a>
                </div>
                <div class="status__content"><?php
                if(!is_null($statut->reblog)):
                  $statut = $statut->reblog;
                endif;
                  if(empty($statut->spoiler_text)):
                    echo apply_filters('fedi_emoji', $statut->content, $statut->emojis);
                    if(!is_null($statut->card)): ?>
                      <a href="<?php echo $statut->card->url; ?>" class="status-card compact" target="_blank" rel="noopener noreferrer">
                        <div class="status-card__image"><div class="status-card__image-image" style="background-image: url(<?php echo $statut->card->image; ?>);"></div></div>
                        <div class="status-card__content">
                          <strong class="status-card__title" title="<?php echo $statut->card->title; ?>"><?php echo htmlentities($statut->card->title); ?></strong>
                          <p class="status-card__description"><?php echo wp_trim_words(htmlentities($statut->card->description), 10); ?></p>
                          <span class="status-card__host"><?php echo $statut->card->url; ?></span>
                        </div>
                      </a>
                      <?php
                    endif;
                  else: echo '<details><summary>' . apply_filters('fedi_emoji', $statut->spoiler_text, $statut->emojis) . '</summary>'. apply_filters('fedi_emoji', $statut->content, $statut->emojis) . '</details>';
                  endif;
                  if(!empty($statut->media_attachments)):
                    foreach ($statut->media_attachments as $attachment) {
                      if (!empty($attachment->preview_url) && $attachment->type === 'image'): ?>
                        <img src="<?php echo $attachment->preview_url; ?>" class="media-gallery__item" alt="<?php echo $attachment->description; ?>" loading="lazy">
                      <?php elseif($attachment->type === 'video'): ?>
                        <video src="<?php echo $attachment->url; ?>" controls poster="<?php echo $attachment->preview_url; ?>" class='media-gallery__item' alt="<?php echo $attachment->description; ?>">
                      <?php elseif($attachment->type === 'audio'): ?>
                        <audio src="<?php echo $attachment->url; ?>" controls poster="<?php echo $attachment->preview_url; ?>" class='media-gallery__item' alt="<?php echo $attachment->description; ?>">
                      <?php endif;
                    }
                  endif;
                 ?></div>
            </div>
          </div>
        </div>
      </article>
    <?php } ?>
  </div>
</div>
<?php
