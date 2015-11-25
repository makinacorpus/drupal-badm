<?php
/**
 * @file
 * Toolbar specific overrides.
 *
 * Please note this specific toolbar looses capacity of hosting a dropdown
 * panel using a caret (for example, the shortcuts module).
 */

/**
 * Overrides theme_links().
 */
function badm_links__toolbar_user(&$variables) {
  global $language_url;

  $links = $variables['links'];
  $output = '';

  $title = t("Account");

  if (!empty($links)) {
    $output = <<<EOT
<ul class="nav navbar-nav navbar-right">
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
      {$title} <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
EOT;
    foreach ($links as $link) {

      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page())) && (empty($link['language']) || $link['language']->language == $language_url->language)) {
        $output .= '<li class="active">';
      } else {
        $output .= '<li>';
      }

      if (isset($link['href'])) {
        // Title we got here is wrong.
        $link['title'] = str_replace(['<span class="element-invisible">'], ['<span class="sr-only">'], $link['title']);
        $output .= l($link['title'], $link['href'], $link);
      }
      $output .= '</li>';
    }
    $output .= '</ul></li></ul>';
  }

  return $output;
}

/**
 * Overrides theme_links().
 */
function badm_links__toolbar_menu(&$variables) {
  global $language_url;

  $links = $variables['links'];
  $output = '';

  //$title = t("Administration");

  if (!empty($links)) {
    /*
    $output = <<<EOT
<ul class="nav navbar-nav">
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
      {$title} <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
EOT;
    */
    $output = '<ul class="nav navbar-nav">';
    foreach ($links as $link) {

      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page())) && (empty($link['language']) || $link['language']->language == $language_url->language)) {
        $output .= '<li class="active">';
      } else {
        $output .= '<li>';
      }

      if (isset($link['href'])) {
        // Title we got here is wrong.
        $link['title'] = str_replace(['<span class="element-invisible">'], ['<span class="sr-only">'], $link['title']);
        $output .= l($link['title'], $link['href'], $link);
      }
      $output .= '</li>';
    }
    //$output .= '</ul></li></ul>';
    $output .= '</ul>';
  }

  return $output;
}
