<nav id="toolbar" class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-collapse" aria-expanded="false">
        <span class="sr-only"><?php echo t("Toggle navigation"); ?></span>
      </button>
      <a class="navbar-brand" href="<?php echo url('admin'); ?>">
        <span aria-hidden="true" class="glyphicon glyphicon-cog"></span>
        <span class="sr-only"><?php echo t("Administration"); ?></span>
      </a>
    </div>
    <?php echo render($toolbar['toolbar_menu']); ?>
  </div>
</nav>