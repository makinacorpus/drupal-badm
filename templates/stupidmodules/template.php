<?php
/**
 * @file
 * Theme overrides for the worst modules ever but everyone uses them.
 */

/**
 * Implements hook_form_FORM_ID_alter().
 */
function badm_form_views_exposed_form_alter(&$form, &$form_state) {
  if ('media_default' === $form_state['view']->name) {
    $form['filename']['#title'] = t("Search");
    $form['filename']['#form_horizontal'] = true;
    $form['filename']['#prefix'] = '<div class="row form-horizontal"><div class="col-xs-6">';
    $form['filename']['#suffix'] = '</div>';
    $form['type']['#title'] = t("Type");
    $form['type']['#form_horizontal'] = true;
    $form['type']['#prefix'] = '<div class="col-xs-6">';
    $form['type']['#suffix'] = '</div></div>';
    $form['sort_by']['#form_horizontal'] = true;
    $form['sort_by']['#prefix'] = '<div class="row form-horizontal"><div class="col-xs-6">';
    $form['sort_by']['#suffix'] = '</div>';
    $form['sort_order']['#form_horizontal'] = true;
    $form['sort_order']['#prefix'] = '<div class="col-xs-6">';
    $form['sort_order']['#suffix'] = '</div></div>';
  }
}

function badm_views_exposed_form__media__default($variables) {
  $form = $variables['form'];
  return drupal_render_children($form);
}

/**
 * Overrides theme_date_popup().
 */
function badm_date_popup($variables) {
  $element = $variables['element'];
  $element['#attributes']['class'][] = 'form-horizontal';
  // That's a very specific to date popup problem, the container has an
  // identifier that is being used by #states, but because it's a container
  // we actually never display this identifier anyway, so let's force the
  // id at an upper level. Also, because Drupal #states will search the
  // previous wrapper to hide instead of the input itself, we do need to
  // force the div with the id to be inside the form element, else the
  // whole dates will be hidden as a side effect.
  $element['#children'] = '<div id="' . $element['#id'] . '">' . $element['#children'] . '</div>';
  return theme('form_element', $element);
}

/**
 * Implements hook_date_popup_process_alter().
 */
function badm_date_popup_process_alter(&$element, &$form_state, $context) {
  unset($element['#theme']);
  foreach (element_children($element) as $key) {
    // This is unfortunately dynamic.
    if (!empty($element[$key]['#type']) && 'textfield' === $element[$key]['#type']) {
      unset($element[$key]['#prefix'], $element[$key]['#suffix'], $element[$key]['#theme_wrappers']);
      $element[$key]['#theme'] = 'textfield';
      $element[$key]['#form_horizontal'] = true;
      $element[$key]['#form_group'] = true;
    }
  }
  // Force our own and only our own to be called.
  // @see badm_date_select().
  $element['#theme_wrappers'] = ['date_popup'];
}

/**
 * Overrides theme_date_select().
 */
function badm_date_select($variables) {
  $element = $variables['element'];
  $element['#form_horizontal'] = true;
  $element['#form_group'] = true;
  return theme('form_element', $element);
}

/**
 * Implements hook_date_select_process_alter().
 */
function badm_date_select_process_alter(&$element, &$form_state, $context) {
  unset($element['#theme']);
  foreach (element_children($element) as $key) {
    // This is unfortunately dynamic.
    if (!empty($element[$key]['#type']) && 'select' === $element[$key]['#type']) {
      unset($element[$key]['#prefix'], $element[$key]['#suffix'], $element[$key]['#theme_wrappers']);
      $element[$key]['#theme'] = 'select';
      $element[$key]['#nowrapper'] = true;
    }
  }
  // Force our own and only our own to be called.
  // @see badm_date_select().
  $element['#theme_wrappers'] = ['date_select'];
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function badm_form_file_entity_admin_file_alter(&$form, &$form_state) {
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
  foreach (element_children($form['filter']['filters']['status']['filters']) as $key) {
    $form['filter']['filters']['status']['filters'][$key]['#form_horizontal'] = true;
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function badm_form_file_entity_edit_alter(&$form, &$form_state) {
  $form['#attributes']['class'][] = 'form-horizontal';

  // Re-order a bit.
  $order = [
    'filename',
    'preview',
    'keep_filename',
    'replace_upload',
    'field_file_image_alt_text',
    'field_file_image_title_text',
  ];
  // Do not ever change that, some other pre_render functions
  // will torture you...
  $weight = -6;
  foreach ($order as $key) {
    if (isset($form[$key])) {
      $form[$key]['#weight'] = $weight++;
    }
  }

  $form['preview'] = [
    '#theme'            => 'form_element',
    '#type'             => 'none',
    '#title'            => t("Preview"),
    '#children'         => render($form['preview']),
    '#form_horizontal'  => true,
  ];

  // Modifies a bit the form structure to be more pleasant.
  foreach (element_children($form) as $key) {
    $form[$key]['#form_horizontal'] = true;
  }
  // Fields...
  $form['field_file_image_alt_text'][LANGUAGE_NONE][0]['value']['#form_horizontal'] = true;
  $form['field_file_image_title_text'][LANGUAGE_NONE][0]['value']['#form_horizontal'] = true;

  $form['#pre_render'][] = 'badm_form_node_form_pre_render';

  // Create a generic tab for node elements.
  // Keep this stupid name because we are going to re-use node form code,
  // it's pretty generic and it should do the trick.
  $form['node_form_orphans'] = array(
    '#type'   => 'fieldset',
    '#title'  => t("Content"),
    '#weight' => -1000,
    '#group'  => 'additional_settings',
  );

  // Also this stupid delete button should not trigger any validation
  // of the form. Still don't get why Drupal does not already do this.
  if (isset($form['actions']['delete'])) {
    $form['actions']['delete']['#limit_validation_errors'] = array();
  }
}

/**
 * Overrides theme_ctools_dropdown().
 */
function badm_ctools_dropdown($variables) {
  return theme('links__ctools_dropdown', ['links' => $variables['links'], 'attributes' => [], 'heading' => '']);
}

/**
 * Overrides theme_links__ctools_dropdown().
 */
function badm_links__ctools_dropdown($variables) {

  $links = $variables['links'];

  if (!$links) {
    return;
  }

  $button = count($links) === 1;

  foreach ($links as $key => $link) {
    $link['attributes']['class'][] = $key;

    if ($button) {
      $link['attributes']['class'][] = 'btn';
      $link['attributes']['class'][] = 'btn-primary';
    }

    if (isset($link['href'])) {
      $links[$key] = l($link['title'], $link['href'], $link);
    } else if (!empty($link['title'])) {
      if (empty($link['html'])) {
        $link['title'] = check_plain($link['title']);
      }
      $span_attributes = drupal_attributes($link['attributes']);
      $links[$key] = "<span{$span_attributes}>{$link['title']}</span>";
    }
  }

  switch (count($links)) {

    case 1:
      return implode('', $links);

    default:
      $links = implode('</li><li>', $links);
      return <<<EOT
<div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Actions <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <li>{$links}</li>
  </ul>
</div>
EOT;
  }
}

/**
 * Implements hook_webform_component_render_alter().
 */
function badm_webform_component_render_alter(&$element, $component, $form_state) {
  if ('textarea' === $element['#type']) {
    $element['#cols'] = 10;
  }
}

