<!DOCTYPE html>
<html lang="<?php echo $language->language; ?>" version="XHTML+RDFa 1.0" dir="<?php echo $language->dir; ?>">
<head profile="<?php echo $grddl_profile; ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php echo $head; ?>
<title><?php echo $head_title; ?></title>
<?php echo $styles; ?>
<?php echo $scripts; ?>
</head>
<body class="<?php echo $classes; ?>" <?php echo $attributes;?>>
  <a id="skip-link" href="#main-content" class="sr-only"><?php echo t('Skip to main content'); ?></a>
  <?php echo $page_top; ?>
  <?php echo $page; ?>
  <?php echo $page_bottom; ?>
</body>
</html>