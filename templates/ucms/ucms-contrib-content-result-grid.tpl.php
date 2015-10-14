<?php foreach ($nodes as $nid => $node): ?>
  <?php if(UCMS_VIEW_MODE_CONTENTADMIN === $view_mode): ?>
    <div class="ucms-contrib-result col-md-3" data-nid="<?php echo $nid; ?>">
      <div class="ucms-contrib-result-inner">
        <?php
          $view = node_view($node, $view_mode);
          echo render($view);
        ?>
      </div>
    </div>
  <?php else: ?>
    <div class="ucms-contrib-result" data-nid="<?php echo $nid; ?>">
      <?php
        $view = node_view($node, $view_mode);
        echo render($view);
      ?>
    </div>
  <?php endif; ?>
<?php endforeach; ?>