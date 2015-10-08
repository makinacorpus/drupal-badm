<div id="ucms-cart" class="container-fluid">
  <div class="row">
    <h2><?php echo t("Your favorites"); ?></h2>

    <div id="ucms-cart-list">
      <?php foreach ($items as $nid => $item): ?>
        <div class="ucms-cart-item col-md-6" data-nid="<?php echo render($nid); ?>">
          <?php echo render($item); ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<div id="ucms-cart-trash">
  <span class="glyphicon glyphicon-trash"></span>
</div>
