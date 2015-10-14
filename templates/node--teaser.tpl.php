<div class="media">
  <div class="media-left pull-left">
    <?php if (!empty($content['image'])):?>
      <?php echo render($content['image']); ?>
    <?php else: ?>
      <div class="media-object">
      </div>
    <?php endif; ?>
  </div>
  <div class="media-body">
    <?php echo render($title_prefix); ?>
    <h4 class="media-heading">
      <a href="<?php echo $node_url; ?>"><?php echo $title; ?></a>
    </h4>
    <?php echo render($title_suffix); ?>
    <?php if (!empty($content['body'])): echo render($content['body']); endif; ?>
  </div>
</div>