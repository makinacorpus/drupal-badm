<?php

/**
 * Overrides theme_links().
 */
function badm_links__ucms_dashboard_sort($variables) {

  $links  = $variables['links'];
  // Ugly hack else the dropdown menu goes away.
  $output = '<span style="position:relative;">';
  $active = $variables['heading'];

  // Find the active link and put it atop.
  // From the code above, there is always one active link.
  foreach ($links as $link) {
    if (!empty($link['attributes']['class']) && in_array('active', $link['attributes']['class'])) {
      $active = $link['title'];
      break;
    }
  }

  if (count($links) > 0) {

    $output .= l($active . ' <span class="caret"></span>', '#', [
      'html'        => true,
      'attributes'  => [
        'class'         => ['dropdown-toggle'],
        'title'         => $variables['heading'],
        'role'          => 'button',
        'aria-haspopup' => "true",
        'aria-expanded' => "false",
        'data-toggle'   => 'dropdown',
      ],
    ]);

    $output .= '<ul class="dropdown-menu">';
    foreach ($links as $link) {
      if (isset($link['href'])) {
        $output .= '<li>' . l($link['title'], $link['href'], $link) . '</li>';
      }
    }
    $output .= '</ul>';
  }
  return $output . '</span>';
}

/**
 * Overrides theme_links().
 */
function badm_links__ucms_dashboard_filter($variables) {
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
    $output .= '<ul class="list-unstyled">';
    foreach ($links as $link) {
      if (isset($link['href'])) {
        $link['attributes']['class'][] = 'list-group-item';

        // Deal with title manually because we are going to force HTML code anyway
        if (empty($link['html'])) {
          $r_title = check_plain($link['title']);
        } else {
          $r_title = filter_xss_admin($link['title']);
        }
        $r_href = url($link['href'], $link);
        // And  we must manually handle the active class too for the checkbox.
        if (!empty($link['attributes']['class']) && in_array('active', $link['attributes']['class'])) {
          $r_c_attributes = ' checked="checked"';
        } else {
          $r_c_attributes = '';
        }
        // Also link attributes, and it should it.
        if (empty($links['attributes'])) {
          $r_attributes = '';
        } else {
          unset($links['attributes']['href']); // Just in case...
          $r_attributes = drupal_attributes($links['attributes']);
        }

        $output .= <<<EOT
<li>
  <div class="checkbox">
    <label>
      <a href="{$r_href}"{$r_attributes}>
        <input type="checkbox"{$r_c_attributes}>
        {$r_title}
      </a>
    </label>
  </div>
</li>
EOT;
      }
    }
    $output .= '</ul>';
  }
  return $output;
}

/**
 * Overrides theme_links().
 */
function badm_links__ucms_contrib_display_switch($variables) {
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

/**
 * Overrides theme_links().
 *
 * @param $variables
 * @return string
 */
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

/**
 * Alter the dashboard search form.
 *
 * @param $form
 * @param $form_state
 */
function badm_form_ucms_dashboard_search_form_alter(&$form, &$form_state) {
  $form['query']['#prefix'] = '<div class="input-group">';
  $form['query']['#theme_wrappers'] = [];
  if (isset($form['clear'])) {
    $form['clear']['#prefix'] = '<span class="input-group-btn">';
    $form['clear']['#content'] = '<span class="glyphicon glyphicon-remove"></span>';
    $form['clear']['#attributes']['class'][] = 'btn';
    $form['clear']['#attributes']['class'][] = 'btn-default';
  } else {
    $form['submit']['#prefix'] = '<span class="input-group-btn">';
  }
  $form['submit']['#content'] = '<span class="glyphicon glyphicon-search"></span>';
  $form['submit']['#suffix'] = '</span></div>';
}
