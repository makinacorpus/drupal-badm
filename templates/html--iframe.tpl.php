<!DOCTYPE html>
<html lang="<?php echo $language->language; ?>" version="XHTML+RDFa 1.0" dir="<?php echo $language->dir; ?>" class="fix-iframe">
<head profile="<?php echo $grddl_profile; ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php echo $head; ?>
<title><?php echo $head_title; ?></title>
<?php echo $styles; ?>
<?php echo $scripts; ?>
</head>
<body<?php print $attributes;?>>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
</body>
</html>