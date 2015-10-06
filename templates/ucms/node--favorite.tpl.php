<?php echo render($title_prefix); ?>
  <span class="title"><a href="<?php echo $node_url; ?>"><?php echo $title; ?></a></span>
<?php echo render($title_suffix); ?>
<?php
  hide($content['comments']);
  hide($content['links']);
  echo render($content);
?>