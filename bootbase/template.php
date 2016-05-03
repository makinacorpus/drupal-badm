<?php

/**
 * Implements hook_theme_registry_alter().
 */
function bootbase_theme_registry_alter(&$theme_registry) {
  // Add an empty_list variable
  $theme_registry['item_list']['variables']['empty_list'] = '';
}

/**
 * Implements hook_preprocess().
 */
function bootbase_preprocess(&$vars) {
  $vars['logged_in'] = user_is_logged_in();
}

/**
 * Overrides theme_links().
 *
 * @param $variables
 * @return string
 */
function bootbase_links__main_menu($variables) {
  $output = '';
  if (count($variables['links']) > 0) {
    $output .= '<ul class="nav navbar-nav navbar-right">';
    foreach ($variables['links'] as $link) {
      if (isset($link['icon'])) {
        $icon = '<span aria-hidden="true" class="glyphicon glyphicon-' . $link['icon'] . '"></span> ';
        $link['title'] = $icon . check_plain($link['title']);
        $link['html'] = TRUE;
      }
      // Append active class.
      if (!empty($link['attributes']) && in_array('active-trail', $link['attributes']['class'])) {
        $output .= '<li class="active">';
      }
      else {
        $output .= '<li>';
      }
      $output .= l($link['title'], $link['href'], $link) . '</li>';
    }
    $output .= '</ul>';
  }
  return $output;
}

/**
 * Overrides theme_menu_tree().
 */
function bootbase_menu_tree__admin($variables) {
  return '<ul class="menu tree-admin">' . $variables['tree'] . '</ul>';
}

/**
 * Overrides theme_menu_link().
 */
function bootbase_menu_link__admin($vars) {
  $vars['element']['#localized_options']['attributes']['class'][] = 'tree-item';
  return theme('menu_link', $vars);
}

/**
 * Overrides theme_image().
 *
 * Why the F*** would they not add classes to images??
 *
 * @param $variables
 * @return string
 */
function bootbase_image($variables) {
  $attributes = $variables['attributes'];
  $attributes['src'] = file_create_url($variables['path']);

  foreach (['width', 'height', 'alt', 'title', 'class'] as $key) {

    if (isset($variables[$key])) {
      $attributes[$key] = $variables[$key];
    }
  }

  return '<img' . drupal_attributes($attributes) . ' />';
}

/**
 * Implements hook_preprocess_HOOK().
 */
function bootbase_preprocess_item_list(&$vars) {
  if (empty($vars['items']) && $vars['empty_list']) {
    // Force class
    if (!isset($vars['attributes']['class']) || array_search(
        'list-unstyled',
        $vars['attributes']['class']
      ) === FALSE
    ) {
      $vars['attributes']['class'][] = 'list-unstyled';
    }
    $vars['items'][] = $vars['empty_list'];
  }
}

/**
 * Overrides theme_item_list().
 */
function bootbase_item_list(&$variables) {
  $items = $variables['items'];
  $title = $variables['title'];
  $type = $variables['type'];
  $attributes = $variables['attributes'];

  // Only output the list container and title, if there are any list items.
  // Check to see whether the block title exists before adding a header.
  // Empty headers are not semantic and present accessibility challenges.
  $output = '';
  if (isset($title) && $title !== '') {
    $output = '<div class="item-list">';
    $output .= '<h3>' . $title . '</h3>';
  }

  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';
    $num_items = count($items);
    $i = 0;
    foreach ($items as $item) {
      $attributes = [];
      $children = [];
      $data = '';
      $i++;
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {
        // Render nested list.
        $data .= theme_item_list(
          [
            'items'      => $children,
            'title'      => NULL,
            'type'       => $type,
            'attributes' => $attributes,
          ]
        );
      }
      if ($i == 1) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_items) {
        $attributes['class'][] = 'last';
      }
      $output .= '<li' . drupal_attributes($attributes) . '>' . $data . "</li>\n";
    }
    $output .= "</$type>";
  }
  if (isset($title) && $title !== '') {
    $output .= '</div>';
  }

  return $output;
}

/**
 * Implements hook_preprocess_HOOK().
 */
function bootbase_preprocess_notification_block(&$vars) {
  foreach ($vars['list'] as &$item) {
    switch ($item['image']) {
      case 'picture':
        $item['class'] = 'success';
        break;

      case 'file':
        $item['class'] = 'info';
        break;

      case 'cloud':
        $item['class'] = 'danger';
        break;

      case 'user':
        $item['class'] = 'warning';
        break;
    }
  }
}


/**
 * Overrides theme_links().
 */
function bootbase_links($variables) {
  $links = $variables['links'];
  $heading = $variables['heading'];
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = [
          'text'  => $heading,
          // Set the default level of the heading.
          'level' => 'h2',
        ];
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(['class' => $heading['class']]);
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    $variables['attributes']['class'][] = "btn-group";
    $output .= '<div' . drupal_attributes($variables['attributes']) . '>';

    foreach ($links as $key => $link) {
      $link['attributes']['class'][] = $key;
      $link['attributes']['class'][] = 'btn';
      $link['attributes']['class'][] = 'btn-default';

      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
        && (empty($link['language']) || $link['language']->language == $language_url->language)
      ) {
        $link['attributes']['class'][] = 'active';
      }

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        $output .= l($link['title'], $link['href'], $link);
      }
      elseif (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }
    }

    $output .= '</div>';
  }

  return $output;
}

/**
 * Overrides theme_status_messages().
 */
function bootbase_status_messages($variables) {
  $display = $variables['display'];
  $output = '';
  foreach (drupal_get_messages($display) as $type => $messages) {
    switch ($type) {
      case 'error':
        $class = 'alert-danger alert-dismissible';
        $icon = '<span class="glyphicon glyphicon-exclamation-sign"></span> '; // Space is important.
        break;
      case 'warning':
        $class = 'alert-warning alert-dismissible';
        $icon = '<span class="glyphicon glyphicon-warning-sign"></span> '; // Space is important.
        break;
      default:
        $class = 'alert-success alert-dismissible';
        $icon = '<span class="glyphicon glyphicon-ok"></span> '; // Space is important.
        break;
    }
    foreach ($messages as $message) {
      $output .= '<div class="alert ' . $class . '" role="alert">' . $icon;
      $output .= $message;
      $output .= '<button type="button" class="close" data-dismiss="alert" aria-label="' . t(
          "Close"
        ) . '"><span aria-hidden="true">&times;</span></button>';
      $output .= '</div>';
    }
  }

  return $output;
}


/**
 * Implements hook_preprocess_HOOK().
 */
function bootbase_preprocess_region(&$variables) {

  unset($variables['theme_hook_suggestions']);
  $variables['theme_hook_suggestions'][] = 'region';
  $variables['theme_hook_suggestions'][] = 'region__' . $variables['region'];
  if (($node = menu_get_object()) && !arg(2)) {
    $variables['theme_hook_suggestions'][] = 'region__' . $variables['region'] . '__' . $node->type;
  }

  // Now that we are a #theme and not a #theme_wrapper, move all renderable
  // children into the content array instead of leaving them as-is. Per default
  // the 'render element' for the 'region' theme hook is 'elements'.
  foreach (element_children($variables['elements']) as $key) {
    $variables['content'][$key] = &$variables['elements'][$key];
    $variables['content'][$key]['#sorted'] = TRUE;
  }
}

/**
 * Implements hook_preprorcess_HOOK().
 */
function bootbase_preprocess_node(&$variables) {
  $node = $variables['node'];
  $view_mode = $variables['view_mode'];

  // Add smart template suggestions.
  $variables['theme_hook_suggestions'][] = 'node__' . $view_mode;
  $variables['theme_hook_suggestions'][] = 'node__' . $node->type . '__' . $view_mode;
}

/**
 * Implements hook_preprocess_HOOK().
 */
function bootbase_preprocess_field(&$variables, $hook) {
  $element = $variables['element'];

  // Add some other smart suggestions.
  $variables['theme_hook_suggestions'] = [
    'field__' . $element['#field_type'],
    'field__' . $element['#field_name'],
    'field__' . $element['#bundle'],
    'field__' . $element['#field_name'] . '__' . $element['#bundle'],
  ];
}


/**
 * Implements hook_preprocess_HOOK().
 */
function bootbase_preprocess_html(&$variables) {
  foreach (['toolbar', 'toolbar-drawer'] as $class) {
    if (FALSE !== ($index = array_search($class, $variables['classes_array']))) {
      unset($variables['classes_array'][$index]);
    }
  }

  // Media is stupid.
  if (!empty($variables['page']['#theme']) && 'media_dialog_page' === $variables['page']['#theme']) {
    $variables['classes_array'][] = 'fix-iframe';
  }
}

/**
 * Implements hook_page_alter().
 */
function bootbase_page_alter(&$page) {
  // @see system_page_alter().
  // This stupid Drupal code puts the 'region' in '#theme_wrappers' instead
  // instead of using '#theme', it makes me very angry because I can't get
  // back my blocks in the template and dispose of them as I want...
  // The '#theme_wrappers' callbacks are run after rendering children, while
  // the '#theme' callback is run before rendering children, allowing me to
  // proceed with render() calls in my region template.
  $regions = system_region_list($GLOBALS['theme']);
  foreach (array_keys($regions) as $region) {
    unset($page[$region]['#theme_wrappers']);
    if (!empty($page[$region])) {
      $page[$region]['#theme'] = 'region';
    }
  }
}


/**
 * Implements hook_form_FORM_ID_alter().
 */
function bootbase_form_ucms_layout_context_edit_form_alter(&$form, &$form_state) {
  $children = element_children($form['actions']);
  $last_action_name = end($children);

  $form['actions']['#type'] = 'container';
  $form['actions']['#attributes']['class'][] = 'form-actions';
  $form['actions']['#theme_wrappers'] = [];
  $form['actions']['#prefix'] = '<div class="form-actions"><div class="btn-group" role="group" aria-label="actions">';
  $form['actions'][$last_action_name]['#prefix'] = '<div class="btn-group" role="group">
      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
      </button>
      <div class="dropdown-menu dropdown-menu-right">';
  $form['actions'][$last_action_name]['#suffix'] = '</div></div>';
  $form['actions']['#suffix'] = '</div></div>';
}


/**
 * Overrides theme_button().
 */
function bootbase_button($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'submit';
  element_set_attributes($element, ['id', 'name', 'value']);
  $value = NULL;

  // I am sorry because I am going to go to hell for this.
  // @todo Keeping it in case.
  if (!empty($element['#submit']) && in_array('commerce_cart_line_item_delete_form_submit', $element['#submit'])) {
    // Commerce cart remove button.
    $element['#attributes']['class'][] = 'close';
    $element['#attributes']['aria-label'][] = t("Remove");
    $value = '<span aria-hidden="true">&times;</span>';
  }
  else {
    if (FALSE && t("Search") === $element['#value']) {
      // Search button.
      $element['#attributes']['class'][] = 'glyphicon';
      $element['#attributes']['class'][] = 'glyphicon-search';
    }
    else {
      if (empty($element['#attributes']['class']) || false === array_search('btn', $element['#attributes']['class'])) {
        if ($class = _bootbase_colorize_button($element['#value'])) {
          $element['#attributes']['class'][] = 'btn';
          // @todo Un-hardcoded this. (see drupal bootstrap)
          $element['#attributes']['class'][] = $class;
        }
      }
    }
  }

  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'disabled';
  }

  if ($value) {
    return '<button' . drupal_attributes($element['#attributes']) . ' />' . $value . '</button>';
  }
  else {
    if (isset($element['#content'])) {
      return '<button' . drupal_attributes($element['#attributes']) . ' />' . $element['#content'] . '</button>';
    }
    else {
      return '<input' . drupal_attributes($element['#attributes']) . ' />';
    }
  }
}

/**
 * Colorize buttons based on the text value.
 *
 * @param string $text
 *   Button text to search against.
 *
 * @return string
 *   The specific button class to use or FALSE if not matched.
 */
function _bootbase_colorize_button($text) {
  // Text values containing these generic strings
  $generic_strings = [
    'btn-primary' => [
      t('Actualiser'),
      t('Write'),
      t('Chercher'),
      t('Next'),
      t('Terminer ma commande'),
      // t("Cancel"),
      // t("Back"),
    ],
    'btn-warning' => [
    ],
    'btn-danger'  => [
      t("Delete"),
      t("Rebuild"),
      t("Restore"),
      t("Revert"),
      t("Remove"),
      t("Re-index selected"),
      t("Re-index"),
    ],
    'btn-success' => [
      t('Log in'),
      t('Update'),
      t("Follow"),
      t('Send'),
      t('Confirm'),
      t('Submit'),
      t('Save'),
      t('Add'),
      t('Create'),
      t('Apply'),
      t("Upload")
    ],
    'btn-default' => [
      t('Export'),
      t('Import'),
      t('Stock épuisé'),
    ],
  ];
  foreach ($generic_strings as $class => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return $class;
      }
    }
  }
  return 'btn-primary';
}


/**
 * Implements hook_preprocess_HOOK().
 */
function bootbase_preprocess_form_element(&$variables) {

  $element = &$variables['element'];
  $element += ['#title_display' => 'before'];
  $attributes = $element['#attributes'];

  if (isset($element['#id'])) {
    $variables['id'] = $element['#id'];
    $attributes['class'][] = $element['#id'];
  }
  else {
    $variables['id'] = NULL;
  }
  $attributes['class'][] = 'form-group';

  $attributes['class'][] = 'form-item';
  if (!empty($element['#required']) && $element['#required']) {
    $attributes['class'][] = 'form-required';
  }
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], [
        ' ' => '-',
        '_' => '-',
        '[' => '-',
        ']' => ''
      ]);
  }

  if (empty($element['#type'])) {
    $element['#type'] = NULL;
  }

  switch ($element['#type']) {

    case 'radio':
    case 'checkbox':
      // Title has already been rendered.
      $element['#title_display'] = 'invisible';
      unset($element['#title']);
      break;

    case 'list_other_select':
      $element['#title_display'] = 'before';
      break;
  }

  if (isset($element['#form_horizontal'])) {
    $is_horizontal = (bool) $element['#form_horizontal'];
  }
  else {
    $is_horizontal = FALSE;
  }

  $variables['form_horizontal'] = $is_horizontal;
  $variables['prefix'] = isset($element['#field_prefix']) ? $element['#field_prefix'] : NULL;
  $variables['suffix'] = isset($element['#field_suffix']) ? $element['#field_suffix'] : NULL;
  if (isset($element['#form_group'])) {
    $variables['form_group'] = $element['#form_group'];
  }
  else {
    $variables['form_group'] = !empty($variables['prefix']) || !empty($variables['suffix']);
  }
  $variables['nowrapper'] = empty($element['#nowrapper']) ? FALSE : TRUE;
  $variables['input'] = $element['#children'];
  $variables['type'] = $element['#type'];
  $variables['description'] = isset($element['#description']) ? $element['#description'] : NULL;
  $variables['label_show'] = ($element['#title_display'] === 'before' || $element['#title_display'] === 'after');
  $variables['label'] = isset($element['#title']) ? filter_xss_admin($element['#title']) : NULL;
  $variables['attributes'] = $attributes;

  $variables['theme_hook_suggestions'][] = 'form_element__' . $element['#type'];
  if ($variables['form_group'] && $variables['form_horizontal']) {
    $variables['theme_hook_suggestions'][] = 'form_element__horizontal_group';
  }
  else {
    if ($variables['form_horizontal']) {
      $variables['theme_hook_suggestions'][] = 'form_element__horizontal';
    }
    else {
      if ($variables['form_group']) {
        $variables['theme_hook_suggestions'][] = 'form_element__group';
      }
    }
  }
}


/**
 * Implements hook_preprocess_HOOK().
 */
function bootbase_preprocess_textfield(&$variables) {
  $element = &$variables['element'];

  // Enculé de Drupal:
  if (60 === $element['#size']) {
    unset($element['#size'], $element['#attributes']['size']);
  }

  $element['#attributes']['class'][] = 'form-control';
  if (!empty($element['#title'])) {
    if (empty($element['#attributes']['title'])) {
      $element['#attributes']['title'] = $element['#title'];
    }
    if (empty($element['#title_force'])) {
      if (empty($element['#attributes']['placeholder'])) {
        // drupal_attributes will check_plain on values
        $element['#attributes']['placeholder'] = html_entity_decode($element['#title'], ENT_QUOTES);
      }
      $element['#title_display'] = 'invisible';
    }
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function bootbase_preprocess_textarea(&$variables) {
  $element = &$variables['element'];
  $element['#attributes']['class'][] = 'form-control';
}

/**
 * Overrides theme_checkbox().
 */
function bootbase_checkbox($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'checkbox';
  element_set_attributes($element, ['id', 'name', '#return_value' => 'value']);

  // Unchecked checkbox has #value of integer 0.
  if (!empty($element['#checked'])) {
    $element['#attributes']['checked'] = 'checked';
  }
  _form_set_class($element, ['form-checkbox']);

  $attributes = drupal_attributes($element['#attributes']);
  if (isset($element['#title'])) {
    $label = filter_xss($element['#title']);
  }
  else {
    $label = NULL;
  }

  if (!$label || isset($element['#title_display']) && 'invisible' === $element['#title_display']) {
    return "<input{$attributes}/>";
  }

  return <<<EOT
<div class="checkbox">
  <label>
    <input{$attributes}/> {$label}
  </label>
</div>
EOT;
}

/**
 * Overrides theme_radio().
 */
function bootbase_radio($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'radio';
  element_set_attributes($element, ['id', 'name', '#return_value' => 'value']);

  if (isset($element['#return_value']) && $element['#value'] !== FALSE && $element['#value'] == $element['#return_value']) {
    $element['#attributes']['checked'] = 'checked';
  }
  _form_set_class($element, ['form-radio']);

  $attributes = drupal_attributes($element['#attributes']);
  if (isset($element['#title'])) {
    $label = filter_xss_admin($element['#title']);
  }
  else {
    $label = NULL;
  }

  if (!$label || isset($element['#title_display']) && 'invisible' === $element['#title_display']) {
    return "<input{$attributes}/>";
  }

  return <<<EOT
<div class="radio">
  <label>
    <input{$attributes}/> {$label}
  </label>
</div>
EOT;
}

/**
 * Implements hook_preprocess_HOOK().
 */
function bootbase_preprocess_select(&$variables) {
  $element = &$variables['element'];
  $element['#attributes']['class'][] = 'form-control';

  if (!empty($element['#title'])) {
    if (empty($element['#attributes']['title'])) {
      $element['#attributes']['title'] = $element['#title'];
    }

    // Fill the "empty" option using the title.
    /*
    if (!empty($element['#title_display']) && 'invisible' === $element['#title_display']) {
      if (array_key_exists(null, $element['#options'])) {
        $element['#options'][null] = $element['#title'];
      } else if (array_key_exists('', $element['#options'])) {
        $element['#options'][''] = $element['#title'];
      } else {
        $element['#options'] = array(null => $element['#title']) + $element['#options'];
      }
    }
     */
    //$element['#title_display'] = 'invisible';
  }
}

/**
 * Overrides theme_password().
 */
function bootbase_password($variables) {
  $element = $variables['element'];

  if (empty($element['#attributes']['placeholder'])) {
    if ('invisible' === $element['#title_display'] && !empty($element['#title'])) {
      $element['#attributes']['placeholder'] = $element['#title'];
    }
    else {
      $element['#attributes']['placeholder'] = t("Password");
    }
  }

  $element['#attributes']['type'] = 'password';
  $element['#attributes']['class'][] = 'form-control';
  element_set_attributes($element, ['id', 'name', 'size', 'maxlength']);
  _form_set_class($element, ['form-text']);

  return '<input' . drupal_attributes($element['#attributes']) . ' />';
}
