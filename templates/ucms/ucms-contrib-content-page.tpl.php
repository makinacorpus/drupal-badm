<div id="ucms-contrib-facets" class="col-md-2">
  <?php echo render($facets); ?>
</div>
<div id="ucms-contrib-results" class="col-md-10">
  <div class="row">
    <!-- 
    <div class="col-md-6">
      HERE LATER SORTING
    </div>
     -->
    <div class="col-md-12">
      <?php echo render($search); ?>
    </div>
  </div>
  <div class="row">
    <?php foreach ($items as $nid => $item): ?>
      <div class="ucms-contrib-result col-md-3" data-nid="<?php echo $nid; ?>">
        <div class="ucms-contrib-result-inner">
          <?php echo render($item); ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="row">
    <?php echo render($pager); ?>
  </div>
</div>
<?php echo render($favorites); ?>