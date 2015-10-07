<?php
/**
 * @file
 * Form related overrides.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 */
function badm_form_user_login_alter(&$form, &$form_state) {
  $form['name']['#title_force'] = true;
  $form['name']['#title'] = t("Your e-mail address");
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function badm_form_user_login_block_alter(&$form, &$form_state) {
  $form['name']['#title_force'] = true;
}

/**
 * Generic form alter.
 */
function badm_form_alter(&$form, &$form_state, $form_id) {
  if (isset($form['actions']) && isset($_GET['destination']) && !isset($form['actions']['cancel']) && !isset($form['cancel'])) {
    $destination = drupal_parse_url($_GET['destination']);
    $path = $destination['path'];
    $form['actions']['cancel']['#markup'] = l("Cancel", $path, [
      'query'       => $destination['query'],
      'fragment'    => $destination['fragment'],
      'attributes'  => ['class' => ['btn', 'btn-default']]
    ]);
  }
}

/**
 * Overrides theme_form().
 */
function badm_form($variables) {
  $element = $variables['element'];

  if (isset($element['#action'])) {
    $element['#attributes']['action'] = drupal_strip_dangerous_protocols($element['#action']);
  }
  element_set_attributes($element, array('method', 'id'));

  if (empty($element['#attributes']['accept-charset'])) {
    $element['#attributes']['accept-charset'] = "UTF-8";
  }

  switch ($element['#form_id']) {

    case 'notification_follow_form':
      $fieldset = false;
      break;

    default:
      $fieldset = true;
      break;
  }

  if ($fieldset) {
    return '<form' . drupal_attributes($element['#attributes']) . '><fieldset>' . $element['#children'] . '</fieldset></form>';
  } else {
    return '<form' . drupal_attributes($element['#attributes']) . '>' . $element['#children'] . '</form>';
  }
}

/**
 * Overrides theme_fieldset().
 */
function badm_fieldset($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id'));

  $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    $output .= '<legend>' . $element['#title'] . '</legend>';
  }
  if (!empty($element['#description'])) {
    $output .= '<p>' . $element['#description'] . '</p>';
  }
  $output .= $element['#children'];
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  $output .= "</fieldset>";
  return $output;
}

/**
 * Overrides theme_button().
 */
function badm_button($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'submit';
  element_set_attributes($element, array('id', 'name', 'value'));
  $value = null;

  // I am sorry because I am going to go to hell for this.
  // @todo Keeping it in case.
  if (!empty($element['#submit']) && in_array('commerce_cart_line_item_delete_form_submit', $element['#submit'])) {
    // Commerce cart remove button.
    $element['#attributes']['class'][] = 'close';
    $element['#attributes']['aria-label'][] = t("Remove");
    $value = '<span aria-hidden="true">&times;</span>';
  } else if (false && t("Search") === $element['#value']) {
    // Search button.
    $element['#attributes']['class'][] = 'glyphicon';
    $element['#attributes']['class'][] = 'glyphicon-search';
  } else if ($class = _badm_colorize_button($element['#value'])) {
    $element['#attributes']['class'][] = 'btn';
    // @todo Un-hardcoded this. (see drupal bootstrap)
    $element['#attributes']['class'][] = $class;
  }

  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'disabled';
  }

  if ($value) {
    return '<button' . drupal_attributes($element['#attributes']) . ' />' . $value . '</button>';
  } else {
    return '<input' . drupal_attributes($element['#attributes']) . ' />';
  }
}

/**
 * Overrides theme_submit().
 */
function badm_submit($variables) {
  $variables['element']['#attributes']['class'][] = 'form-submit';
  return theme('button', $variables['element']);
}

/**
 * Implements hook_webform_component_render_alter().
 */
function badm_webform_component_render_alter(&$element, $component, $form_state) {
  // Redmine #7763
  if ('textarea' === $element['#type']) {
    $element['#cols'] = 10;
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function badm_preprocess_form_element(&$variables) {
  $element = &$variables['element'];
  $element += array('#title_display' => 'before');
  $attributes = array();

  if (isset($element['#id'])) {
    $variables['id'] = $element['#id'];
    $attributes['class'][] = $element['#id'];
  } else {
    $variables['id'] = null;
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
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(
        ' ' => '-',
        '_' => '-',
        '[' => '-',
        ']' => '',
      ));
  }
  switch ($element['#type']) {

    case 'textfield':
    case 'date_select':
      if (empty($element['#title_force'])) {
        $element['#title_display'] = 'invisible';
      }
      break;

    case 'select':
      //$element['#title_display'] = 'invisible';
      break;

    case 'list_other_select':
      $element['#title_display'] = 'before';
      break;
  }

  $variables['input']       = $element['#children'];
  $variables['type']        = $element['#type'];
  $variables['prefix']      = isset($element['#field_prefix']) ? $element['#field_prefix'] : null;
  $variables['suffix']      = isset($element['#field_suffix']) ? $element['#field_suffix'] : null;
  $variables['description'] = isset($element['#description']) ? $element['#description'] : null;
  $variables['label_show']  = ($element['#title_display'] === 'before' || $element['#title_display'] === 'after');
  $variables['label']       = isset($element['#title']) ? filter_xss_admin($element['#title']) : null;
  $variables['attributes']  = $attributes;

  $variables['theme_hook_suggestions'][] = 'form_element__' . $element['#type'];
}

/**
 * Implements hook_preprocess_HOOK().
 */
function badm_preprocess_textfield(&$variables) {
  $element = &$variables['element'];
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
function badm_preprocess_select(&$variables) {
  $element = &$variables['element'];
  $element['#attributes']['class'][] = 'form-control';

  if (!empty($element['#title'])) {
    if (empty($element['#attributes']['title'])) {
      $element['#attributes']['title'] = $element['#title'];
    }
    if (empty($element['#attributes']['placeholder'])) { // Is placeholder OK for select?
      $element['#attributes']['placeholder'] = $element['#title'];
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
function badm_password($variables) {
  $element = $variables['element'];

  if (empty($element['#attributes']['placeholder'])) {
    if ('invisible' === $element['#title_display'] && !empty($element['#title'])) {
      $element['#attributes']['placeholder'] = $element['#title'];
    } else {
      $element['#attributes']['placeholder'] = t("Password");
    }
  }

  $element['#attributes']['type'] = 'password';
  $element['#attributes']['class'][] = 'form-control';
  element_set_attributes($element, array('id', 'name', 'size', 'maxlength'));
  _form_set_class($element, array('form-text'));

  return '<input' . drupal_attributes($element['#attributes']) . ' />';
}

/**
 * Overrides theme_tableselect().
 */
function badm_tableselect($variables) {
  $element = $variables['element'];
  $rows = array();
  $header = $element['#header'];
  if (!empty($element['#options'])) {
    // Generate a table row for each selectable item in #options.
    foreach (element_children($element) as $key) {
      $row = array();
      $row['data'] = array();
      if (isset($element['#options'][$key]['#attributes'])) {
        $row += $element['#options'][$key]['#attributes'];
      }
      // Render the checkbox / radio element.
      $row['data'][] = drupal_render($element[$key]);
      // As theme_table only maps header and row columns by order, create the
      // correct order by iterating over the header fields.
      foreach ($element['#header'] as $fieldname => $title) {
        $row['data'][] = $element['#options'][$key][$fieldname];
      }
      $rows[] = $row;
    }
    // Add an empty header or a "Select all" checkbox to provide room for the
    // checkboxes/radios in the first table column.
    if ($element['#js_select']) {
      // Add a "Select all" checkbox.
      drupal_add_js('misc/tableselect.js');
      array_unshift($header, array('class' => array('select-all')));
    } else {
      // Add an empty header when radio buttons are displayed or a "Select all"
      // checkbox is not desired.
      array_unshift($header, '');
    }
  }
  return theme('table', array('header' => $header, 'rows' => $rows, 'empty' => $element['#empty'], 'attributes' => $element['#attributes']));
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
function _badm_colorize_button($text) {
  // Text values containing these generic strings
  $generic_strings = array(
    'btn-primary' => array(
      t('Actualiser'),
      t('Write'),
      t('Chercher'),
      t('Next'),
      t('Terminer ma commande'),
      // t("Cancel"),
      // t("Back"),
    ),
    'btn-warning' => array(
    ),
    'btn-danger' => array(
      t("Delete"),
      t("Rebuild"),
      t("Restore"),
      t("Revert"),
      t("Remove"),
    ),
    'btn-success' => array(
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
    ),
    'btn-default' => array(
      t('Export'),
      t('Import'),
      t('Stock épuisé'),
    ),
  );
  foreach ($generic_strings as $class => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return $class;
      }
    }
  }
  return 'btn-primary';
}