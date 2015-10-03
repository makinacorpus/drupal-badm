<?php

/**
 * Forms alteration.
 */
require_once __DIR__ . '/templates/form/template.php';

/**
 * Implements hook_preprocess_page().
 */
function badm_preprocess_page(&$page) {
  // Force some JS inclusion.
  $opts = ['preprocess' => true, 'every_page' => true];
  $path = drupal_get_path('theme', 'badm');
  /*
  drupal_add_js($path . '/bootstrap/js/alert.js', $opts);
  drupal_add_js($path . '/bootstrap/js/button.js', $opts);
  drupal_add_js($path . '/bootstrap/js/collapse.js', $opts);
  drupal_add_js($path . '/bootstrap/js/dropdown.js', $opts);
  drupal_add_js($path . '/bootstrap/js/modal.js', $opts);
  drupal_add_js($path . '/bootstrap/js/tab.js', $opts);
  drupal_add_js($path . '/bootstrap/js/tooltip.js', $opts);
  drupal_add_js($path . '/bootstrap/js/popover.js', $opts);
   */
}

/**
 * Overrides theme_menu_local_task().
 */
function badm_menu_local_task($variables) {
  $link = $variables['element']['#link'];
  $link_text = $link['title'];
  if (!empty($variables['element']['#active'])) {
    $active = '<span class="sr-only">' . t('(active tab)') . '</span>';
    if (empty($link['localized_options']['html'])) {
      $link['title'] = check_plain($link['title']);
    }
    $link['localized_options']['html'] = TRUE;
    $link_text = t('!local-task-title!active', ['!local-task-title' => $link['title'], '!active' => $active]);
  }
  return '<li role="presentation"' . (!empty($variables['element']['#active']) ? ' class="active"' : '') . '>' . l($link_text, $link['href'], $link['localized_options']) . "</li>\n";
}

/**
 * Overrides theme_menu_local_tasks().
 */
function badm_menu_local_tasks(&$variables) {
  $output = '';
  if (!empty($variables['primary'])) {
    $variables['primary']['#prefix'] = '<ul class="nav nav-tabs">';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['primary']);
  }
  if (!empty($variables['secondary'])) {
    $variables['secondary']['#prefix'] = '<ul class="nav nav-tabs">';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['secondary']);
  }
  return $output;
}

/**
 * Overrides theme_menu_local_action().
 */
function badm_menu_local_action($variables) {
  $link = $variables['element']['#link'];
  // $output = '<li role="presentation">';
  if (!empty($link['localized_options']['html'])) {
    $title = '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' . $link['title'];
  } else {
    $title = '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' . check_plain($link['title']);
  }
  $link['localized_options']['html'] = true;
  if (isset($link['href'])) {
    $link['localized_options']['attributes']['class'][] = 'btn';
    $link['localized_options']['attributes']['class'][] = 'btn-info';
    $output = l($title, $link['href'], $link['localized_options']);
  } else {
    $output = $title;
  }
  // $output .= "</li>";
  return $output;
}

/**
 * Overrides theme_breadcrumb().
 */
function badm_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  if (!empty($breadcrumb)) {
    $output = '<h2 class="sr-only">' . t('You are here') . '</h2>';
    if (!empty($breadcrumb)) {
      // Remove first item and set the Home glyphicon, for fun.
      $front = url('<front>');
      array_shift($breadcrumb);
      array_unshift($breadcrumb, <<<EOT
<a href="$front">
  <span aria-hidden="true" class="glyphicon glyphicon-home"></span>
  <span class="sr-only"><?php echo t("Home"); ? ></span>
</a>
EOT
      );
      $links = '<li>' . implode('</li><li>', $breadcrumb) . '</li>';
    } else {
      $links = '';
    }
    $output .= '<ol class="breadcrumb">' . $links  . '<li class="active">' . drupal_get_title() . '</li></ol>';
    return $output;
  }
}

/**
 * Overrides theme_status_messages().
 */
function badm_status_messages($variables) {
  $display = $variables['display'];
  $output = '';
  foreach (drupal_get_messages($display) as $type => $messages) {
    switch ($type) {
      case 'error':
        $class = 'alert-danger alert-dismissible';
        $icon = '<span class="glyphicon glyphicon-ok">';
        break;
      case 'warning':
        $class = 'alert-warning alert-dismissible';
        $icon = '<span class="glyphicon glyphicon-ok">';
        break;
      default:
        $class = 'alert-success alert-dismissible';
        $icon = '<span class="glyphicon glyphicon-alert">';
        break;
    }
    foreach ($messages as $message) {
      $output .= '<div class="alert ' . $class . '" role="alert">';
      $output .= $message;
      $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="' . t("Close") . '"><span aria-hidden="true">&times;</span></button>';
      $output .= '</div>';
    }
  }
  return $output;
}

/**
 * Overrides theme_admin_block().
 */
function badm_admin_block($variables) {
  $block = $variables['block'];
  $output = '';
  if (empty($block['show'])) {
    return $output;
  }
  $output = '<div class="panel panel-default admin-block">';
  if (!empty($block['title'])) {
    $output .= '<div class="panel-heading"><h3 class="panel-title">' . $block['title'] . '</h3></div>';
  }
  $output .= '<div class="panel-body">';
  if (!empty($block['content'])) {
    $output .= $block['content'];
  }
  else {
    $output .= '<p class="description">' . $block['description'] . '</p>';
  }
  return $output . '</div></div>';
}

/**
 * Overrides theme_admin_block_content().
 */
function badm_admin_block_content($variables) {
  $content = $variables['content'];
  $output = '';
  if (!empty($content)) {
    $compact = system_admin_compact_mode();
    $output .= '<dl>';
    foreach ($content as $item) {
      $output .= '<dt>' . l($item['title'], $item['href'], $item['localized_options']) . '</dt>';
      if (!$compact && isset($item['description'])) {
        $output .= '<dd>' . filter_xss_admin($item['description']) . '</dd>';
      }
    }
    $output .= '</dl>';
  }
  return $output;
}

/**
 * Overrides theme_admin_page().
 */
function badm_admin_page($variables) {
  $blocks = $variables['blocks'];

  $stripe = 0;
  $output = '';
  $container = [];

  foreach ($blocks as $block) {
    if ($block_output = theme('admin_block', ['block' => $block])) {
      if (empty($block['position'])) {
        // perform automatic striping.
        $block['position'] = ++$stripe % 2 ? 'left' : 'right';
      }
      if (!isset($container[$block['position']])) {
        $container[$block['position']] = '';
      }
      $container[$block['position']] .= $block_output;
    }
  }

  //$output .= theme('system_compact_link');
  foreach ($container as $data) {
    $output .= '<div class="col-md-6">';
    $output .= $data;
    $output .= '</div>';
  }

  return '<div class="row">' . $output . '</div>';
}

/**
 * Overrides theme_system_admin_index().
 */
function badm_system_admin_index($variables) {
  $menu_items = $variables['menu_items'];

  $container = ['left' => '', 'right' => ''];
  $flip = ['left' => 'right', 'right' => 'left'];
  $position = 'left';

  // Iterate over all modules.
  foreach ($menu_items as $module => $block) {
    list($description, $items) = $block;

    // Output links.
    if (count($items)) {
      $block = [
        'title'       => $module,
        'content'     => theme('admin_block_content', ['content' => $items]),
        'description' => t($description),
        'show'        => true,
      ];
      if ($block_output = theme('admin_block', ['block' => $block])) {
        if (!isset($block['position'])) {
          // Perform automatic striping.
          $block['position'] = $position;
          $position = $flip[$position];
        }
        $container[$block['position']] .= $block_output;
      }
    }
  }

  $output = theme('system_compact_link');
  foreach ($container as $id => $data) {
    $output .= '<div class="' . $id . ' clearfix">';
    $output .= $data;
    $output .= '</div>';
  }

  return $output;
}

/**
 * Overrides theme_status_report().
 */
function badm_status_report($variables) {
  $requirements = $variables['requirements'];
  $severities = [
    REQUIREMENT_INFO => [
      'title' => t('Info'),
      'class' => 'info',
    ],
    REQUIREMENT_OK => [
      'title' => t('OK'),
      'class' => 'success',
    ],
    REQUIREMENT_WARNING => [
      'title' => t('Warning'),
      'class' => 'warning',
    ],
    REQUIREMENT_ERROR => [
      'title' => t('Error'),
      'class' => 'danger',
    ],
  ];
  $output = '<table class="table table-condensed">';

  foreach ($requirements as $requirement) {
    if (empty($requirement['#type'])) {
      $severity = $severities[isset($requirement['severity']) ? (int) $requirement['severity'] : REQUIREMENT_OK];
      // Output table row(s)
      if (!empty($requirement['description'])) {
        $output .= '<tr class="' . $severity['class'] . ' merge-down"><td class="status-title">' . $requirement['title'] . '</td><td class="status-value">' . $requirement['value'] . '</td></tr>';
        $output .= '<tr class="' . $severity['class'] . ' merge-up"><td colspan="2" class="status-description">' . $requirement['description'] . '</td></tr>';
      }
      else {
        $output .= '<tr class="' . $severity['class'] . '"><td class="status-title">' . $requirement['title'] . '</td><td class="status-value">' . $requirement['value'] . '</td></tr>';
      }
    }
  }

  $output .= '</table>';
  return $output;
}

/**
 * Overrides theme_table().
 */
function badm_table($variables) {

  $header = $variables['header'];
  $rows = $variables['rows'];
  $attributes = $variables['attributes'];
  $caption = $variables['caption'];
  $colgroups = $variables['colgroups'];
  $empty = $variables['empty'];

  $attributes['class'][] = 'table';
  $attributes['class'][] = 'table-striped';

  $output = '<table' . drupal_attributes($attributes) . ">\n";
  if (isset($caption)) {
    $output .= '<caption>' . $caption . "</caption>\n";
  }

  // Format the table columns:
  if (count($colgroups)) {
    foreach ($colgroups as $colgroup) {
      $attributes = array();

      // Check if we're dealing with a simple or complex column
      if (isset($colgroup['data'])) {
        foreach ($colgroup as $key => $value) {
          if ($key == 'data') {
            $cols = $value;
          } else {
            $attributes[$key] = $value;
          }
        }
      } else {
        $cols = $colgroup;
      }

      // Build colgroup
      if (is_array($cols) && count($cols)) {
        $output .= ' <colgroup' . drupal_attributes($attributes) . '>';
        $i = 0;
        foreach ($cols as $col) {
          $output .= ' <col' . drupal_attributes($col) . ' />';
        }
        $output .= " </colgroup>\n";
      } else {
        $output .= ' <colgroup' . drupal_attributes($attributes) . " />\n";
      }
    }
  }

  // Add the 'empty' row message if available.
  if (!count($rows) && $empty) {
    $header_count = 0;
    foreach ($header as $header_cell) {
      if (is_array($header_cell)) {
        $header_count += isset($header_cell['colspan']) ? $header_cell['colspan'] : 1;
      } else {
        $header_count++;
      }
    }
    $rows[] = array(array('data' => $empty, 'colspan' => $header_count, 'class' => array('empty', 'message')));
  }

  // Format the table header:
  if (count($header)) {
    $ts = tablesort_init($header);
    // HTML requires that the thead tag has tr tags in it followed by tbody
    // tags. Using ternary operator to check and see if we have any rows.
    $output .= (count($rows) ? ' <thead><tr>' : ' <tr>');
    foreach ($header as $cell) {
      $cell = tablesort_header($cell, $header, $ts);
      $output .= _theme_table_cell($cell, TRUE);
    }
    // Using ternary operator to close the tags based on whether or not there are rows
    $output .= (count($rows) ? " </tr></thead>\n" : "</tr>\n");
  } else {
    $ts = array();
  }

  // Format the table rows:
  if (count($rows)) {
    $output .= "<tbody>\n";
    foreach ($rows as $row) {
      $attributes = array();
      // Check if we're dealing with a simple or complex row
      if (isset($row['data'])) {
        foreach ($row as $key => $value) {
          if ($key == 'data') {
            $cells = $value;
          } else {
            $attributes[$key] = $value;
          }
        }
      } else {
        $cells = $row;
      }
      if (count($cells)) {
        // Build row
        $output .= ' <tr' . drupal_attributes($attributes) . '>';
        $i = 0;
        foreach ($cells as $cell) {
          $cell = tablesort_cell($cell, $header, $ts, $i++);
          $output .= _theme_table_cell($cell);
        }
        $output .= " </tr>\n";
      }
    }
    $output .= "</tbody>\n";
  }

  $output .= "</table>\n";
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
