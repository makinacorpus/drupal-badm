<?php if ($total_count): ?>
  <div class="text">
    (<?php echo t("<strong>@a</strong> of @b", array(
      '@a' => $total_count,
      '@b' => $real_total,
    )); ?>)
  </div>
<?php endif; ?>
<?php if (empty($list)): ?>
  <div class="text">
    <?php echo t("You have no messages."); ?>
  </div>
<?php else: ?>
  <?php foreach ($list as $item): ?>
    <?php if ($item['link']): ?><a href="<?php echo $item['link']; ?>"><?php endif; ?>
    <div class="media notification notification-<?php echo $item['type']; ?><?php if ($item['unread']): echo ' notification-unread'; endif; ?>">
      <div class="media-left">
        <?php echo render($item['image']); ?>
      </div>
      <div class="media-body">
        <!-- <h4 class="media-heading">Media heading</h4> -->
        <?php echo $item['text']; ?>
        <span class="time">
          <?php echo format_interval(time() - $item['time']); ?>
        </span>
      </div>
    </div>
    <?php if ($item['link']): ?></a><?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>