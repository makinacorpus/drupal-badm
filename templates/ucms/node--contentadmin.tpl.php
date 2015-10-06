<?php echo render($title_prefix); ?>
  <h2<?php echo $title_attributes; ?>><a href="<?php echo $node_url; ?>"><?php echo $title; ?></a></h2>
<?php echo render($title_suffix); ?>
<?php
  hide($content['comments']);
  hide($content['links']);
  echo render($content);
?>