<?php if (!$nowrapper): ?><div <?php echo drupal_attributes($attributes); ?>><?php endif; ?>
  <?php if ($label): ?>
  <?php if ($label_show): ?>
  <label for="<?php echo $id ?>"><?php echo $label; ?></label>
  <?php else: ?>
  <label class="sr-only" for="<?php echo $id ?>"><?php echo $label; ?></label>
  <?php endif; ?>
  <?php endif; ?>
  <?php if ($prefix): ?><?php echo $prefix; ?><?php endif; ?>
  <?php echo $input; ?>
  <?php if ($suffix): ?><?php echo $suffix; ?><?php endif; ?>
  <?php if ($description): ?><p class="help-block"><?php echo $description; ?></p><?php endif; ?>
<?php if (!$nowrapper): ?></div><?php endif; ?>
