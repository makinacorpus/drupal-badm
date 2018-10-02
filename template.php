<?php

/**
 * Forms alteration.
 */
require_once __DIR__ . '/templates/form/template.php';

/**
 * Toolbar alterations.
 */
require_once __DIR__ . '/templates/toolbar/template.php';

/**
 * Node alterations.
 */
require_once __DIR__ . '/templates/node/template.php';

/**
 * µCMS alterations.
 */
require_once __DIR__ . '/templates/ucms/template.php';

/**
 * Views, CTools, and other stupid modules.
 */
require_once __DIR__ . '/templates/stupidmodules/template.php';

/**
 * Implements hook_preprocess_HOOK().
 */
function badm_preprocess_page(&$variables) {
  // Force some JS inclusion.
  $opts = ['preprocess' => true, 'every_page' => true];
  $path = drupal_get_path('theme', 'badm');
  if (theme_get_setting('gulpifier_single_js')) {
    drupal_add_js($path . '/bootstrap/js/alert.js', $opts);
    drupal_add_js($path . '/bootstrap/js/button.js', $opts);
    drupal_add_js($path . '/bootstrap/js/collapse.js', $opts);
    drupal_add_js($path . '/bootstrap/js/dropdown.js', $opts);
    drupal_add_js($path . '/bootstrap/js/modal.js', $opts);
    drupal_add_js($path . '/bootstrap/js/tab.js', $opts);
    drupal_add_js($path . '/bootstrap/js/tooltip.js', $opts);
    drupal_add_js($path . '/bootstrap/js/popover.js', $opts);
  } else {
    drupal_add_js($path . '/bootstrap/dist/js/bootstrap.min.js', $opts);
  }
  drupal_add_js($path . '/js/jquery.dialog.js', $opts);
}

/**
 * Implements hook_library_alter().
 */
function badm_library_alter(&$libraries, $module) {
  if ('media' === $module) {
    // FUCK YOU SERIOUSLY HARD MEDIA AND DRUPAL. My JS file does override
    // whatever media does, it monkey patches a few default options, this
    // works very nicely whenever media is loaded on the page without AJAX
    // because theme JS goes loaded after module JS. What happens is when
    // the necessary JS is loaded after the page has been loaded (when using
    // AJAX in a form for example) media module overrides my JS instead of
    // the opposite. The only way to avoid that is to make sure we're being
    // loaded after media by adding ourselves in the media library definition.
    // Seriously Drupal, lost one hour to this. PHOQUE ZAT.
    $libraries['media_browser']['js'][drupal_get_path('theme', 'badm') . '/js/nique-media.js'] = ['group' => JS_DEFAULT];
  }
}

/**
 * Implements hook_element_info_alter().
 */
function badm_element_info_alter(&$type) {
  $type['udatetime']['#process'][] = 'badm_udatetime_element_process';
}

/**
 * Implements hook_preprocess_HOOK().
 */
function badm_preprocess_block(&$variables) {
  // Add some region/page specifics.
  $region = $variables['block']->region;
  if (($node = menu_get_object()) && !arg(2)) {
    $variables['theme_hook_suggestions'][] = 'block__' . $region . '__' . $node->type;
  }
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
    $title = '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . $link['title'];
  } else {
    $title = '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> ' . check_plain($link['title']);
  }
  $link['localized_options']['html'] = true;
  if (isset($link['href'])) {
    $link['localized_options']['attributes']['class'][] = 'btn';
    $link['localized_options']['attributes']['class'][] = 'btn-success';
    $output = l($title, $link['href'], $link['localized_options'] + $link);
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
      if (variable_get('badm_breadcrumb_display_home', false)) {
        // Remove first item and set the Home glyphicon, for fun.
        $front = url('<front>');
        array_unshift($breadcrumb, <<<EOT
<a href="$front">
  <span aria-hidden="true" class="glyphicon glyphicon-home"></span>
  <span class="sr-only">Home</span>
</a>
EOT
        );
      }

      $links = '<li>' . implode('</li><li>', $breadcrumb) . '</li>';
    } else {
      $links = '';
    }

    if ($withCurrent = variable_get('badm_breadcrumb_display_current', true)) {
      if (($item = menu_get_item()) && ($item['type'] == MENU_LOCAL_TASK)) {
        _menu_translate($item, $item['original_map']);
        $current = $item['title'];
      } else {
        $current = drupal_get_title();
      }
    }

    $output .= '<ol class="breadcrumb">' . $links  . ($withCurrent ? '<li class="active">' . $current . '</li>' : '') . '</ol>';
    return $output;
  }
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
 * Overrides theme_menu_link__backoffice().
 */
function badm_menu_link__backoffice($variables) {
  $element = $variables['element'];
  if ($element['#below']) {
    return '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . filter_xss($element['#title']) . ' <span class="caret"></span></a>' . drupal_render($element['#below']) . '</li>';
  } else {
    return '<li>' . l($element['#title'], $element['#href'], $element['#localized_options']) . '</li>';
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function badm_preprocess_table(&$vars) {

  // Make it bootstrap yeah!
  if (isset($vars['attributes']['class']) && is_string($vars['attributes']['class'])) {
    $vars['attributes']['class'] = array($vars['attributes']['class']);
  }
  $vars['attributes']['class'][] = 'table';
  if (empty($vars['attributes']['no_strip']) && variable_get('badm_breadcrumb_table_striped', true)) {
    $vars['attributes']['class'][] = 'table-striped';
  }
  $vars['attributes']['class'][] = 'table-condensed';

  // Count header for later, better here than over there.
  $header_count = 0;
  if (!empty($vars['header'])) {
    foreach ($vars['header'] as &$header_cell) {
      if (is_array($header_cell)) {
        $header_count += isset($header_cell['colspan']) ? $header_cell['colspan'] : 1;
      } else {
        $header_count++;
      }
    }
  }

  // Need this in template.
  $vars['header_count'] = $header_count;

  if ($header_count) {
    $vars['sortheader'] = tablesort_init($vars['header']);
  } else {
    $vars['sortheader'] = [];
  }

  if (!empty($vars['header'])) {
    foreach ($vars['header'] as &$header_cell) {
      $header_cell = tablesort_header($header_cell, $vars['header'], $vars['sortheader']);
    }
  }
  if (!empty($vars['rows'])) {
    foreach ($vars['rows'] as &$row) {
      $i = 0;
      foreach ($row as &$cell) {
        $cell = tablesort_cell($cell, $vars['header'], $vars['sortheader'], $i);
        $i++;
      }
    }
  }

  // Add the 'empty' row message if available.
  if ($vars['empty'] && empty($vars['rows'])) {
    $vars['rows'][] = [['data' => $vars['empty'], 'colspan' => $header_count, 'class' => ['empty', 'message']]];
  }

  // Manage main attributes
  if (isset($vars['colgroups'])) {
    _badm_extract_data_from_attributes($vars, 'colgroups');
  }
  if (isset($vars['header'])) {
    _badm_extract_data_from_attributes($vars, 'header');
  }
  if (isset($vars['rows'])) {
    _badm_extract_data_from_attributes($vars, 'rows');
  }

  // Manage cells attributes, because they are deeper.
  $vars['cells_attributes'] = [];
  foreach ($vars['rows'] as $row_index => &$row) {
    if (is_array($row)) {
      foreach ($row as $cell_index => &$cell) {
        $vars['cells_attributes'][$row_index][$cell_index] = [];
        if (isset($cell['data']) || (is_array($cell) && count(element_properties($cell)) == 0)) {
          $temp_real_value = '';
          foreach ($cell as $key => $value) {
            if ($key == 'data') {
              $temp_real_value = $value;
            } else {
              $vars['cells_attributes'][$row_index][$cell_index][$key] = $value;
            }
          }
          $cell = $temp_real_value;
        }
      }
    }
  }

  $vars['theme_hook_suggestions'][] = 'table';
}

/**
 * Extract 'data' key and create another variable with other attributes.
 *
 * @param $vars
 * @param $name
 */
function _badm_extract_data_from_attributes(&$vars, $name) {
  $vars[$name . '_attributes'] = [];
  foreach ($vars[$name] as $index => &$item) {
    $vars[$name . '_attributes'][$index] = [];
    if (is_array($item) && (isset($item['data']) || ($name == 'header' && count(element_properties($item)) == 0))) {
      $temp_real_value = '';
      foreach ($item as $key => $value) {
        if ($key === 'data') {
          $temp_real_value = $value;
        }
        else {
          $vars[$name . '_attributes'][$index][$key] = $value;
        }
      }
      $item = $temp_real_value;
    }
  }
}

/**
 * Overrides theme_nice_menus_build().
 */
function badm_nice_menus_build($variables) {
  $menu = $variables['menu'];
  $depth = $variables['depth'];
  $trail = $variables['trail'];
  $output = '';
  // Prepare to count the links so we can mark first, last, odd and even.
  $index = 0;
  $count = 0;
  foreach ($menu as $menu_count) {
    if ($menu_count['link']['hidden'] == 0) {
      $count++;
    }
  }
  // Get to building the menu.
  foreach ($menu as $menu_item) {
    $mlid = $menu_item['link']['mlid'];
    // Check to see if it is a visible menu item.
    if (!isset($menu_item['link']['hidden']) || $menu_item['link']['hidden'] == 0) {
      // Check our count and build first, last, odd/even classes.
      $index++;
      $first_class = $index == 1 ? ' first ' : '';
      $oddeven_class = $index % 2 == 0 ? ' even ' : ' odd ';
      $last_class = $index == $count ? ' last ' : '';
      // Build class name based on menu path
      // e.g. to give each menu item individual style.
      // Strip funny symbols.
      $clean_path = str_replace(array('http://', 'www', '<', '>', '&', '=', '?', ':', '.'), '', $menu_item['link']['href']);
      // Convert slashes to dashes.
      $clean_path = str_replace('/', '-', $clean_path);
      $class = 'menu-path-' . $clean_path;
      if ($trail && in_array($mlid, $trail)) {
        $class .= ' active-trail';
      }
      // If it has children build a nice little tree under it.
      if ((!empty($menu_item['link']['has_children'])) && (!empty($menu_item['below'])) && $depth != 0) {
        // Keep passing children into the function 'til we get them all.
        if ($menu_item['link']['depth'] <= $depth || $depth == -1) {
          $children = array(
            '#theme' => 'nice_menus_build',
            '#prefix' => '<ul>',
            '#suffix' => '</ul>',
            '#menu' => $menu_item['below'],
            '#depth' => $depth,
            '#trail' => $trail,
          );
        }
        else {
          $children = '';
        }
        // Set the class to parent only of children are displayed.
        $parent_class = ($children && ($menu_item['link']['depth'] <= $depth || $depth == -1)) ? 'menuparent ' : '';
         $element = array(
          '#below' => $children,
          '#title' => $menu_item['link']['link_title'],
          '#href' =>  $menu_item['link']['href'],
          '#localized_options' => $menu_item['link']['localized_options'],
          '#attributes' => array(
            'class' => array('menu-' . $mlid, $parent_class, $class, $first_class, $oddeven_class, $last_class),
          ),
        );
        $variables['element'] = $element;
        $output .= theme('menu_link', $variables);
      }
      else {
        $element = array(
          '#below' => '',
          '#title' => $menu_item['link']['link_title'],
          '#href' =>  $menu_item['link']['href'],
          '#localized_options' => $menu_item['link']['localized_options'],
          '#attributes' => array(
            'class' => array('menu-' . $mlid, $class, $first_class, $oddeven_class, $last_class),
          ),
        );
        $variables['element'] = $element;
        $output .= theme('menu_link', $variables);
      }
    }
  }
  return $output;
}

/**
 * Drupal is so stupid, JS progress bars cannot be themed...
 */
function badm_progress_bar($variables) {
  $percent = $variables['percent'];
  return <<<EOT
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="{$percent}" aria-valuemin="0" aria-valuemax="100" style="width: {$percent}%;">
    {$percent}%
  </div>
</div>
EOT;
/*
    <<<EOT
<div id="progress" class="progress">
  <div class="bar">
    <div class="filled" style="width: {$variables['percent']}%"></div>
  </div>
  <div class="percentage">{$variables['percent']}%</div>
  <div class="message">{$variables['message']}</div>
</div>
EOT;
*/
}

/**
 * Process udatetime elements.
 */
function badm_udatetime_element_process($element) {
  // Remove stupid container
  unset($element['container']['#prefix']);
  unset($element['container']['#suffix']);

  return $element;
}

/**
 * Implements hook_widget_WIDGET_TYPE_form_alter().
 */
function badm_field_widget_udate_range_form_alter(&$element, &$form_state, $context) {
  $element['#type'] = 'fieldset';
  $element['#title'] = $context['instance']['label'];
}
