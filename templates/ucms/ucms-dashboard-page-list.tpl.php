<div id="ucms-contrib-facets" class="col-md-2">
  <?php echo render($displayLinks); ?>
  <?php foreach ($filters as $filter): ?>
    <?php echo render($filter); ?>
  <?php endforeach; ?>
</div>
<div id="ucms-contrib-results" class="col-md-10">
  <div class="row">
    <div class="col-md-12">
      <?php echo render($search); ?>
    </div>
  </div>
  <div class="row">
    <?php echo render($displayView); ?>
  </div>
  <div class="row">
    <?php echo render($pager); ?>
  </div>
</div>