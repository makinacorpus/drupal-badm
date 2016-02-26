<?php

/**
 * Implements hook_form_FORM_ID_alter().
 */
function badm_form_node_form_alter(&$form, &$form_state) {

  $form['#form_horizontal'] = true;

  $form['#pre_render'][] = 'badm_form_node_form_pre_render';

  // Create a generic tab for node elements, but desactivate it for now.
  $form['node_form_orphans'] = [
    '#type'   => 'fieldset',
    '#title'  => t("Content"),
    '#weight' => -1000,
    '#group'  => 'additional_settings',
    '#access' => FALSE,
  ];

  // Also this stupid delete button should not trigger any validation
  // of the form. Still don't get why Drupal does not already do this.
  if (isset($form['actions']['delete'])) {
    $form['actions']['delete']['#limit_validation_errors'] = array();
  }

  $form['actions']['#weight'] = 1000;
}

/**
 * Alters the node form on pre_render -ie. after all the real form processing
 * happened- thus ensuring we can move elements around without interacting with
 * form validation and submission processes.
 */
function badm_form_node_form_pre_render($form) {

  if (!isset($form['additional_settings'])
    || (isset($form['additional_settings']['#access']) && !$form['additional_settings']['#access'])
    || isset($form['additional_settings']['additional_settings__active_tab']) && count(element_children($form['additional_settings']['additional_settings__active_tab'])) == 0
  ) {
    return $form;
  }

  // Exclude all non visible items. Note that fieldset will have a specific
  // case in the foreach loop.
  $excluded_types = [
    'value'         => true,
    'hidden'        => true,
    'token'         => true,
    'actions'       => true,
    'vertical_tabs' => true,
  ];

  foreach (element_children($form) as $key) {

    $element = $form[$key];
    $type = isset($element['#type']) ? $element['#type'] : null;

    if (isset($excluded_types[$type])) {
      continue;
    }

    switch ($type) {

      case 'fieldset':
        if (isset($element['#group']) && 'additional_settings' === $element['#group']) {
          // Leave the ungrouped fieldset go down the road to the default
          // behavior and be moved into the orphans container.
          break;
        }

      default:
        $form['node_form_orphans'][$key] = $element;
        // This breaks ordering... Ideally ordering would need to be restored
        // at the field config level in order to avoid further bad surprises
        // but instead we just force the reordering below.
        unset($form[$key]);
        break;
    }
  }

  uasort($form['node_form_orphans'], 'element_sort');
  // Activate tab.
  $form['node_form_orphans']['#access'] = TRUE;

  return $form;
}
