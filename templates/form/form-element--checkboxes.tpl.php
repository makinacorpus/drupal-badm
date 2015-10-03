<fieldset <?php echo drupal_attributes($attributes); ?>>
  <?php if ($label_show && $label): ?><label for="<?php echo $id ?>"><?php echo $label; ?></label><?php endif; ?>
  <?php if ($prefix): ?><?php echo $prefix; ?><?php endif; ?>
  <?php echo $input; ?>
  <?php if ($suffix): ?><?php echo $suffix; ?><?php endif; ?>
  <?php if ($description): ?><p class="help-block"><?php echo $description; ?></p><?php endif; ?>
</fieldset>
