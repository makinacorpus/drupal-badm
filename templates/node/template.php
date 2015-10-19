<?php

/**
 * Implements hook_form_FORM_ID_alter().
 */
function badm_form_node_form_alter(&$form, &$form_state) {

  $form['#pre_render'][] = 'badm_form_node_form_pre_render';

  // Create a generic tab for node elements.
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
 * Alters the node form on pre_render -ie. after all the real form processing
 * happened- thus ensuring we can move elements around without interacting with
 * form validation and submission processes.
 */
function badm_form_node_form_pre_render($form) {

  if (!isset($form['additional_settings']) || (isset($form['additional_settings']['#access']) && !$form['additional_settings']['#access'])) {
    return $form;
  }

  // Exclude all non visible items. Note that fieldset will have a specific
  // case in the foreach loop.
  $excluded_types = array(
    'value'         => true,
    'hidden'        => true,
    'token'         => true,
    'actions'       => true,
    'vertical_tabs' => true,
  );

  /*
  // Do some business specific alterations depending on the form contents.
  $move_candidates = array(); array(
    'planning' => array('options', 100),
    'field_admin_desc' => array('node_form_orphans', -20),
  );

  // We don't want jalon to be part of it
  $node_type = '';
  if (!empty($form['type']['#value'])) {
    $node_type = $form['type']['#value'];
  }

  if ($node_type != 'jalon') {
    foreach ($move_candidates as $key => $options) {
      list($fieldset, $weight) = $options;
      if (isset($form[$key]) && isset($form[$fieldset]) && (!isset($form[$fieldset]['#access']) || $form[$fieldset]['#access'])) {
        $form[$fieldset][$key] = $form[$key];
        $form[$fieldset][$key]['#weight'] = $weight;
        unset($form[$key]);
      }
    }
  }
   */

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
        // @todo This should be done, but it breaks ordering...
        //   Ordering needs to be restored at the field config level in order
        //   to avoid further bad surprises such as this one, but I don't have
        //   time right now.
        // uasort($form['node_form_orphans'], 'drupal_sort_weight');
        unset($form[$key]);
        break;
    }
  }

  return $form;
}
