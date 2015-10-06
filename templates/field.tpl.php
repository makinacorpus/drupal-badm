<?php if (!$label_hidden): ?>
  <span class="field-label"<?php echo $title_attributes; ?>><?php echo $label ?>:&nbsp;</span>
<?php endif; ?>
<?php foreach ($items as $delta => $item): ?>
  <?php echo render($item); ?>
<?php endforeach; ?>