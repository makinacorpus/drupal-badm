<div id="ucms-contrib-facets" class="col-md-2">
  <?php echo render($display); ?>
  <?php echo render($facets); ?>
</div>
<div id="ucms-contrib-results" class="col-md-10">
  <div class="row">
    <div class="col-md-12">
      <?php echo render($search); ?>
    </div>
  </div>
  <div class="row">
    <?php echo render($nodes); ?>
  </div>
  <div class="row">
    <?php echo render($pager); ?>
  </div>
</div>
<?php echo render($favorites); ?>