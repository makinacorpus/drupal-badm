<div class="form-inline">
  <div <?php echo drupal_attributes($attributes); ?>>
    <?php if ($label): ?>
      <?php if ($form_horizontal): ?>
        <label class="control-label col-sm-3 col-md-2 col-lg-1" for="<?php echo $id ?>"><?php echo $label; ?></label>
      <?php else: ?>
        <label class="control-label" for="<?php echo $id ?>"><?php echo $label; ?></label>
      <?php endif; ?>
    <?php endif; ?>
    <div class="input-group">
      <?php if ($prefix): ?>
        <div class="input-group-addon">
          <?php echo $prefix; ?>
        </div>
      <?php endif; ?>
      <?php echo $input; ?>
      <?php if ($suffix): ?>
        <div class="input-group-addon">
          <?php echo $suffix; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php if ($description): ?><p class="help-block"><?php echo $description; ?></p><?php endif; ?>
  </div>
</div>