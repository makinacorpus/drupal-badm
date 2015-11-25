<?php if (isset($messages)) { print $messages; } ?>
<div id="media-browser-page-wrapper">
  <div id="media-browser-page">
    <?php echo render($title_prefix); ?>
    <?php echo render($title_suffix); ?>
    <div id="media-browser-tabset">
      <div id="branding" class="clearfix">
        <div id="media-tabs-wrapper">
          <?php print render($page['content']['system_main']['tabset']['tabs']); ?>
        </div>
      </div>
      <?php print render($page['content']['system_main']['tabset']['panes']); ?>
    </div>
  </div>
</div>

<script language="javascript">
  var dialogContent = jQuery('#media-browser-page');
</script>

<?php
  hide($page['content']['system_main']['tabset']);
  hide($page['content']['system_main']['title']);
  print render($page['content']);
?>