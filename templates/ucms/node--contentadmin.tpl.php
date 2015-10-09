<?php echo render($title_prefix); ?>
  <div class="title"><a href="<?php echo $node_url; ?>"><?php echo $title; ?></a></div>
<?php echo render($title_suffix); ?>
<?php
  hide($content['comments']);
  hide($content['links']);
  echo render($content);
?>