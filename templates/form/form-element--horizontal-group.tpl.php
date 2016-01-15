<div<?php echo drupal_attributes($attributes); ?>>

  <?php if ($label && $label_show): ?>
    <label class="control-label col-sm-3 col-md-3" for="<?php echo $id ?>"><?php echo $label; ?></label>
    <div class="col-sm-9 col-md-9">
  <?php else: ?>
    <?php if ($label): ?>
      <label class="sr-only" for="<?php echo $id ?>"><?php echo $label; ?></label>
    <?php endif; ?>
    <div class="col-sm-offset-3 col-sm-9 col-md-offset-2 col-md-9">
  <?php endif; ?>

    <div class="form-inline">
      <div class="input-group">
        <?php if ($prefix): ?>
          <span class="input-group-addon">
            <?php echo $prefix; ?>
          </span>
        <?php endif; ?>
        <?php echo $input; ?>
        <?php if ($suffix): ?>
          <span class="input-group-addon">
            <?php echo $suffix; ?>
          </span>
        <?php endif; ?>
      </div>
      <?php if ($description): ?><p class="help-block"><?php echo $description; ?></p><?php endif; ?>
    </div>
  </div>
</div>