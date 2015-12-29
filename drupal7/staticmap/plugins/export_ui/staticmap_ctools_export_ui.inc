<?php

/**
 * @file
 * Ctools export routines.
 */

$plugin = array(
  // The name of the table as found in the schema in hook_install. This
  // must be an exportable type with the 'export' section defined.
  'schema' => 'staticmap_presets',
  // The access permission to use. If not provided it will default to
  // 'administer site configuration'

  // You can actually define large chunks of the menu system here. Nothing
  // is required here. If you leave out the values, the prefix will default
  // to admin/structure and the item will default to the plugin name.
  'menu' => array(
    'menu prefix' => 'admin/config/content',
    'menu item' => 'staticmap',
    // Title of the top level menu. Note this should not be translated,
    // as the menu system will translate it.
    'menu title' => 'Static Map',
    // Description of the top level menu, which is usually needed for
    // menu items in an administration list. Will be translated
    // by the menu system.
    'menu description' => 'Administer staticmap presets.',
  ),
  // These are required to provide proper strings for referring to the
  // actual type of exportable. "proper" means it will appear at the
  // beginning of a sentence.
  'title singular' => t('staticmap'),
  'title singular proper' => t('Static Map'),
  'title plural' => t('staticmaps'),
  'title plural proper' => t('Static Maps'),
  // This will provide you with a form for editing the properties on your
  // exportable, with validate and submit handler.
  //
  // The item being edited will be in $form_state['item'].
  //
  // The submit handler is only responsible for moving data from
  // $form_state['values'] to $form_state['item'].
  //
  // All callbacks will accept &$form and &$form_state as arguments.
  'form' => array(
    'settings' => 'staticmap_ctools_export_ui_form',
    'validate' => 'staticmap_ctools_export_ui_form_validate',
    'submit' => 'staticmap_ctools_export_ui_form_submit',
  ),

);
