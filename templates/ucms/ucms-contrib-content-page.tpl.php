<div id="ucms-contrib-facets" class="col-md-2">
  <?php echo render($facets); ?>
</div>
<div id="ucms-contrib-results" class="col-md-8">
  <div class="row">
    <?php echo render($search); ?>
  </div>
  <div class="row">
    <?php foreach ($items as $nid => $item): ?>
      <div class="col-md-6 ucms-contrib-result" data-nid="<?php echo $nid; ?>">
        <?php echo render($item); ?>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="row">
    <?php echo render($pager); ?>
  </div>
</div>
<div id="ucms-contrib-cart" class="col-md-2">
  <?php echo render($favorites); ?>
</div>