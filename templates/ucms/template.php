<?php

/**
 * Overrides theme_links().
 */
function badm_links__ucms_search_facet_terms($variables) {
  $links    = $variables['links'];
  $heading  = $variables['heading'];
  $output   = '';
  if (count($links) > 0) {
    if (!empty($heading)) {
      if (is_string($heading)) {
        $heading = [
          'text'  => $heading,
          'level' => 'strong',
          'class' => [],
        ];
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(['class' => $heading['class']]);
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }
    $output .= '<div class="list-group small">';
    foreach ($links as $link) {
      if (isset($link['href'])) {
        $link['attributes']['class'][] = 'list-group-item';
        $output .= l($link['title'], $link['href'], $link);
      }
    }
    $output .= '</div>';
  }
  return $output;
}

/**
 * Overrides theme_links().
 */
function badm_links__ucms_contrib_dislay_switch($variables) {
  $links = $variables['links'];
  $output = '';

  if (count($links) > 0) {
    $output .= '<div class="clearfix"><div class="btn-group">';

    foreach ($links as $key => $link) {

      $link['attributes']['class'][] = 'btn';
      $link['attributes']['class'][] = 'btn-default';

      if (isset($link['href'])) {

        switch ($key) {
          case 'grid':
            $icon = 'th-large';
            break;
          case 'list':
            $icon = 'list';
            break;
          case 'table':
            $icon = 'th-list';
            break;
          default:
            $icon = 'justify';
            break;
        }

        $attributes = drupal_attributes($link['attributes']);
        $href = url($link['href'], $link);

        $output .= <<<EOT
<a {$attributes}href="{$href}" title="{$link['title']}">
  <span class="glyphicon glyphicon-{$icon}" aria-hidden="true"></span>
  <span class="sr-only">{$link['title']}</span>
</a>
EOT;
      }
    }

    $output .= '</div></div>';
  }

  return $output;
}

function badm_links__ucms_contrib_actions__icon($variables) {
  $links    = $variables['links'];
  $heading  = $variables['heading'];
  $output   = '';
  if (count($links) > 0) {
    if (!empty($heading)) {
      if (is_string($heading)) {
        $heading = [
          'text'  => $heading,
          'level' => 'strong',
          'class' => [],
        ];
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(['class' => $heading['class']]);
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }
    $output .= '<div class="list-group small">';
    foreach ($links as $link) {
      if (isset($link['href'])) {
        $link['attributes']['class'][] = 'list-group-item';
        $output .= l($link['title'], $link['href'], $link);
      }
    }
    $output .= '</div>';
  }
  return $output;
}
