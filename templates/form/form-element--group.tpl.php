<div class="form-inline">
  <div <?php echo drupal_attributes($attributes); ?>>
    <?php if ($label): ?>
      <label class="control-label" for="<?php echo $id ?>"><?php echo $label; ?></label>
    <?php endif; ?>
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