<div<?php echo drupal_attributes($attributes); ?>>

  <?php if ($label && $label_show): ?>
    <label class="control-label col-sm-3 col-md-2 col-lg-1" for="<?php echo $id ?>"><?php echo $label; ?></label>
    <div class="col-sm-9 col-md-10 col-lg-11">
  <?php else: ?>
    <?php if ($label): ?>
      <label class="sr-only" for="<?php echo $id ?>"><?php echo $label; ?></label>
    <?php endif; ?>
    <div class="col-sm-offset-3 col-sm-9 col-md-offset-2 col-md-10 col-lg-offset-1 col-lg-11">
  <?php endif; ?>

    <?php if ($prefix): ?><?php echo $prefix; ?><?php endif; ?>
    <?php echo $input; ?>
    <?php if ($suffix): ?><?php echo $suffix; ?><?php endif; ?>
    <?php if ($description): ?><p class="help-block"><?php echo $description; ?></p><?php endif; ?>
  </div>
</div>