<div id="page" class="container-fluid fix-iframe">
  <?php if ($messages): ?>
    <div class="row">
      <div class="col-md-12">
        <?php echo $messages; ?>
      </div>
    </div>
  <?php endif; ?>
  <div class="row">
    <div id="content" class="col-md-12">
      <?php echo render($page['content']); ?>
    </div>
  </div>
</div>