<nav id="toolbar" class="navbar navbar-inverse">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-collapse" aria-expanded="false">
        <span class="sr-only"><?php echo t("Toggle navigation"); ?></span>
      </button>
      <a class="navbar-brand" href="<?php echo url('<front>'); ?>">
        <span aria-hidden="true" class="glyphicon glyphicon-tint"></span>
        <span class="sr-only"><?php echo t("Home"); ?></span>
      </a>
    </div>
    <!-- <div class="collapse navbar-collapse" id="#nav-collapse"> -->
    <?php echo render($toolbar['toolbar_menu']); ?>
    <?php echo render($toolbar['toolbar_user']); ?>
    <!-- </div> -->
    <!--
    <?php if ($toolbar['toolbar_drawer']):?>
      <?php echo render($toolbar['toolbar_toggle']); ?>
    <?php endif; ?>
    <div class="<?php echo $toolbar['toolbar_drawer_classes']; ?>">
      <?php echo render($toolbar['toolbar_drawer']); ?>
    </div>
     -->
  </div>
</nav>