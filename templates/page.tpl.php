<div id="page" class="container">

  <div class="row">
    <div class="col-md-12">
      <?php echo $breadcrumb; ?>
      <?php echo render($title_prefix); ?>
      <?php echo render($title_suffix); ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">

      <?php if (!empty($tabs)): ?>
        <?php echo render($tabs); ?>
      <?php endif; ?>

      <?php if ($action_links): ?>
        <div class="pull-right">
          <?php echo render($action_links); ?>
        </div>
      <?php endif; ?>

      <?php if ($messages): ?>
        <?php echo $messages; ?>
      <?php endif; ?>

      <?php if ($page['help']): ?>
        <div class="panel panel-default">
          <div class="panel-body">
            <?php echo render($page['help']); ?>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <div class="row">
    <?php if (!empty($sidear_first)): ?>
      <div id="sidebar" class="col-md-3">
        <?php echo render($sidear_first); ?>
      </div>
      <div id="content" class="col-md-9">
        <a id="main-content" class="sr-only"></a>
        <?php echo render($page['content']); ?>
      </div>
    <?php else: ?>
      <div id="main" class="col-md-12">
        <a id="main-content" class="sr-only"></a>
        <?php echo render($page['content']); ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<div class="container">
  <div id="footer" class="row">
    <?php echo $feed_icons; ?>
  </div>
</div>