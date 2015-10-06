<?php echo render($title_prefix); ?>
<?php if ($block->subject): ?>
<h2><?php echo $block->subject ?></h2>
<?php endif;?>
<?php echo render($title_suffix); ?>
<?php echo $content ?>