<!-- mastodon -->
<div class="scrollable" style="height: <?php echo $height; ?>;">
  <div role="feed">
    <?php if($show_header): ?>
    <div class="account-timeline__header">
      <div class="account__header">
        <div class="account__header__image">
          <div class="account__header__info"></div>
          <?php if ($status[0]->account->header): echo "<img src=" . $status[0]->account->header . " loading='lazy'>"; endif; ?>
        </div>
        <div class="account__header__bar">
          <div class="account__header__tabs">
            <a href="<?php echo $status[0]->account->url; ?>" class="avatar" rel="noreferrer noopener" target="_blank">
              <div class="account__avatar" style="width:90px; height: 90px; background-image: url('<?php echo $status[0]->account->avatar; ?>'); background-size: cover;"></div>
            </a>
            <div class="spacer"></div>
            <div class="account__header__tabs__buttons">
              <a href="<?php echo $status[0]->account->url; ?>" rel="noreferrer noopener" class="button logo-button"><?php _e('Follow', 'fediembedi'); ?></a>
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
                    <span class="display-name"><?php if(is_null($statut->reblog)): echo $statut->account->display_name; else: echo $statut->reblog->account->display_name; endif; ?></span>
                  </a>
                </div>
                <div class="status__content"><?php
                if(!is_null($statut->reblog)):
                  $statut = $statut->reblog;
                endif;
                  if(empty($statut->spoiler_text)):
                    echo $statut->content;
                    if(!is_null($statut->card)): ?>
                      <a href="<?php echo $statut->card->url; ?>" class="status-card compact" target="_blank" rel="noopener noreferrer">
                        <div class="status-card__image"><div class="status-card__image-image" style="background-image: url(<?php echo $statut->card->image; ?>);"></div></div>
                        <div class="status-card__content">
                          <strong class="status-card__title" title="<?php echo $statut->card->title; ?>"><?php echo htmlentities($statut->card->title); ?></strong>
                          <p class="status-card__description"><?php echo wp_trim_words(htmlentities($statut->card->description)); ?></p>
                          <span class="status-card__host"><?php echo $statut->card->url; ?></span>
                        </div>
                      </a>
                      <?php
                    endif;
                  else: echo '<details><summary>' . $statut->spoiler_text . '</summary>'. $statut->content . '</details>';
                  endif;
                  if(!empty($statut->media_attachments)):
                    foreach ($statut->media_attachments as $attachment) {
                      if (!empty($attachment->preview_url) && $attachment->type === 'image'):
                        echo "<img src='" . $attachment->preview_url . "' class='media-gallery__item' alt='" . $attachment->description . "' loading='lazy'>";
                      elseif($attachment->type === 'video'):
                        echo "<video src=" . $attachment->url . " controls poster='" . $attachment->preview_url . "' class='media-gallery__item' alt=" . $attachment->description . ">";
                      elseif($attachment->type === 'audio'):
                        echo "<audio src=" . $attachment->url . " controls poster='" . $attachment->preview_url . "' class='media-gallery__item' alt=" . $attachment->description . ">";
                      endif;
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
