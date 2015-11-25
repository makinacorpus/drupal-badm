<?php
/**
 * @file
 * Form related overrides.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 */
function badm_form_user_filter_form_alter(&$form, &$form_state) {
  $form['filters']['#attributes']['class'][] = 'form-horizontal';
  unset(
    $form['filters']['#theme'],
    $form['filters']['status']['#type'],
    $form['filters']['status']['#attributes'],
    $form['filters']['status']['#prefix'],
    $form['filters']['status']['filters']['#prefix'],
    $form['filters']['status']['filters']['#attributes'],
    $form['filters']['status']['filters']['#type'],
    $form['filters']['status']['actions']['#attributes']
  );
  if (isset($form['filters']['status']['filters'])) {
    foreach (element_children($form['filters']['status']['filters']) as $key) {
      $form['filters']['status']['filters'][$key]['#form_horizontal'] = true;
    }
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function badm_form_node_admin_content_alter(&$form, &$form_state) {
  $form['filter']['filters']['#attributes']['class'][] = 'form-horizontal';
  unset(
    // $form['filter']['filters']['#type'],
    // $form['filter']['filters']['#attributes'],
    // $form['filter']['filters']['#title'],
    $form['filter']['filters']['#theme'],
    $form['filter']['filters']['status']['#type'],
    $form['filter']['filters']['status']['#attributes'],
    $form['filter']['filters']['status']['#prefix'],
    $form['filter']['filters']['status']['filters']['#prefix'],
    $form['filter']['filters']['status']['filters']['#attributes'],
    $form['filter']['filters']['status']['filters']['#type'],
    $form['filter']['filters']['status']['actions']['#attributes']
  );
  if (isset($form['filter']['filters']['status']['filters'])) {
    foreach (element_children($form['filter']['filters']['status']['filters']) as $key) {
      $form['filter']['filters']['status']['filters'][$key]['#form_horizontal'] = true;
    }
  }
}

/**
 * Recurse into forms in order to detect vertical tabs and disable
 * collapsing on children.
 */
function _badm_vertical_tabs_find($form, &$ret) {
  foreach (element_children($form) as $key) {
    if (isset($form[$key]['#type']) && 'vertical_tabs' === $form[$key]['#type']) {
      $ret[] = $key;
      _badm_vertical_tabs_find($form[$key], $ret);
    }
  }
}

/**
 * Recurse into forms in order to detect vertical tabs and disable
 * collapsing on children.
 */
function _badm_vertical_tabs_recursion(&$form, $vertical_tabs = []) {
  if (isset($form['#type']) && 'fieldset' === $form['#type'] && isset($form['#group'])) {
    if (in_array($form['#group'], $vertical_tabs)) {
      $form['#collapsible'] = 0;
      $form['#collapsed'] = 0;
    }
  }
  foreach (element_children($form) as $key) {
    _badm_vertical_tabs_recursion($form[$key], $vertical_tabs);
  }
}

/**
 * Generic form alter.
 */
function badm_form_alter(&$form, &$form_state, $form_id) {

  // Alter the confirm form, because Drupal is stupid.
  if ('confirm_form' === $form['#theme']) {

    $question = drupal_get_title();
    if ($item = menu_get_item()) {
      if (empty($item['title'])) {
        drupal_set_title(t("Confirmation"));
      } else {
        drupal_set_title($item['title']);
      }
    }

    $question = [
      '#prefix' => '<p class="lead"><strong>',
      '#markup' => $question,
      '#suffix' => '</strong></p>',
      '#weight' => -100,
    ];
    array_unshift($form, $question);
  }

  // We have to do 2 passes...
  $vertical_tabs = [];
  _badm_vertical_tabs_find($form, $vertical_tabs);
  _badm_vertical_tabs_recursion($form, $vertical_tabs);

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
 * Renders the filter_format another way.
 */
function badm_fieldset_filter_format($variables) {
  $element = &$variables['element'];
  element_set_attributes($element, ['id']);

  // A modal is perfect for this.
  $text = t("Text format");
  $modalId = isset($element['#id']) ? ($element['#id'] . '-modal') : drupal_html_id('modal-inner');
  $element['#type'] = 'container';
  $closeText = t("Close");
  $titleText = t("Change format");
  $content = $element['#children'];

  return <<<EOT
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#{$modalId}">
  <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
  {$text}
</button>
<div class="modal fade" id="{$modalId}" tabindex="-1" role="dialog" aria-labelledby="{$modalId}Label">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="{$closeText}"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="{$modalId}Label">{$titleText}</h4>
      </div>
      <div class="modal-body">
        {$content}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{$closeText}</button>
      </div>
    </div>
  </div>
</div>
EOT;
}

/**
 * Overrides theme_fieldset().
 */
function badm_fieldset($variables) {
  $element = &$variables['element'];

  if (isset($element['help']) && isset($element['format']) && isset($element['guidelines'])) {
    // We are rendering filter tips, swap the fieldset for something else.
    return badm_fieldset_filter_format($variables);
  }

  element_set_attributes($element, array('id'));

  $collapsible  = isset($element['#collapsible']) ? $element['#collapsible'] : false;
  $collapsed    = isset($element['#collapsed']) ? $element['#collapsed'] : false;
  $innerid      = isset($element['#id']) ? ($element['#id'] . '-content') : drupal_html_id('fieldset-inner');
  $innerclass   = '';
  $title        = isset($element['#title']) ? $element['#title'] : null;

  $element['#attributes']['class'][] = 'panel panel-default';
  if ($collapsible && $collapsed) {
    $innerclass = ' collapse';
  }

  if (false !== ($index = array_search('collapsible', $element['#attributes']['class']))) {
    unset($element['#attributes']['class'][$index]);
    if (empty($title)) {
      $title = t("Display");
    }
    if (false !== ($index = array_search('collapsed', $element['#attributes']['class']))) {
      unset($element['#attributes']['class'][$index]);
    }
  }

  $output = '<fieldset' . drupal_attributes($element['#attributes']) . '>';
  if (!empty($element['#title'])) {
    if ($collapsible) {
      $output .= <<<EOT
<legend class="panel-heading">
  <a role="button" data-toggle="collapse" href="#{$innerid}">
    {$title}
    <span class="caret"></span>
  </a>
</legend>
EOT;
    } else {
      $output .= '<legend class="panel-heading">' . $element['#title'] . '</legend>';
    }
  }
  $output .= '<div id="' . $innerid . '" class="panel-body' . $innerclass . '">';
  if (!empty($element['#description'])) {
    $output .= '<p>' . $element['#description'] . '</p>';
  }
  $output .= $element['#children'];
  if (isset($element['#value'])) {
    $output .= $element['#value'];
  }
  $output .= "</div></fieldset>";
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
  } else if (isset($element['#content'])) {
    return '<button' . drupal_attributes($element['#attributes']) . ' />' . $element['#content'] . '</button>';
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
 * Implements hook_preprocess_HOOK().
 */
function badm_preprocess_form_element(&$variables) {

  $element = &$variables['element'];
  $element += array('#title_display' => 'before');
  $attributes = $element['#attributes'];

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
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], [' ' => '-', '_' => '-', '[' => '-', ']' => '']);
  }

  switch ($element['#type']) {
    case 'checkbox':
      // Title has already been rendered.
      $element['#title_display'] = 'invisible';
      unset($element['#title']);
    case 'list_other_select':
      $element['#title_display'] = 'before';
      break;
  }

  $variables['form_horizontal'] = empty($element['#form_horizontal']) ? false : true;
  $variables['form_group']      = empty($element['#form_group']) ? false : true;
  $variables['nowrapper']       = empty($element['#nowrapper']) ? false : true;
  $variables['input']           = $element['#children'];
  $variables['type']            = $element['#type'];
  $variables['prefix']          = isset($element['#field_prefix']) ? $element['#field_prefix'] : null;
  $variables['suffix']          = isset($element['#field_suffix']) ? $element['#field_suffix'] : null;
  $variables['description']     = isset($element['#description']) ? $element['#description'] : null;
  $variables['label_show']      = ($element['#title_display'] === 'before' || $element['#title_display'] === 'after');
  $variables['label']           = isset($element['#title']) ? filter_xss_admin($element['#title']) : null;
  $variables['attributes']      = $attributes;

  $variables['theme_hook_suggestions'][] = 'form_element__' . $element['#type'];
  if ($variables['form_group'] && $variables['form_horizontal']) {
    $variables['theme_hook_suggestions'][] = 'form_element__horizontal_group';
  } else if ($variables['form_horizontal']) {
    $variables['theme_hook_suggestions'][] = 'form_element__horizontal';
  } else if ($variables['form_group']) {
    $variables['theme_hook_suggestions'][] = 'form_element__group';
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function badm_preprocess_textfield(&$variables) {
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
 * Overrides theme_checkbox().
 */
function badm_checkbox($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'checkbox';
  element_set_attributes($element, array('id', 'name', '#return_value' => 'value'));

  // Unchecked checkbox has #value of integer 0.
  if (!empty($element['#checked'])) {
    $element['#attributes']['checked'] = 'checked';
  }
  _form_set_class($element, array('form-checkbox'));

  $attributes = drupal_attributes($element['#attributes']);
  if (isset($element['#title'])) {
    $label = filter_xss($element['#title']);
  } else {
    $label = null;
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
      foreach (array_keys($element['#header']) as $fieldname) {
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
      t("Re-index selected"),
      t("Re-index"),
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