<div id="<?php echo $block_html_id; ?>" class="<?php echo $classes; ?>"<?php echo $attributes; ?>>
  <?php echo render($title_prefix); ?>
  <?php if ($block->subject): ?>
    <h2<?php echo $title_attributes; ?>><?php echo $block->subject ?></h2>
  <?php endif;?>
  <?php echo render($title_suffix); ?>
  <div class="content"<?php echo $content_attributes; ?>>
    <?php echo $content ?>
  </div>
</div>