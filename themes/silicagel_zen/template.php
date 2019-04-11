<?php
/**
 * @file
 * Contains the theme's functions to manipulate Drupal's default markup.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728096
 */


/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
/* -- Delete this line if you want to use this function
function silicagel_zen_preprocess_maintenance_page(&$variables, $hook) {
  // When a variable is manipulated or added in preprocess_html or
  // preprocess_page, that same work is probably needed for the maintenance page
  // as well, so we can just re-use those functions to do that work here.
  silicagel_zen_preprocess_html($variables, $hook);
  silicagel_zen_preprocess_page($variables, $hook);
}
// */

/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
/* -- Delete this line if you want to use this function
function silicagel_zen_preprocess_html(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // The body tag's classes are controlled by the $classes_array variable. To
  // remove a class from $classes_array, use array_diff().
  //$variables['classes_array'] = array_diff($variables['classes_array'], array('class-to-remove'));
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
/* -- Delete this line if you want to use this function
function silicagel_zen_preprocess_page(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the node templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */

function silicagel_zen_preprocess_node(&$variables, $hook) {
  
  // Optionally, run node-type-specific preprocess functions, like
  // silicagel_zen_preprocess_node_page() or silicagel_zen_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}

/**
 * Preprocess the silica_gel_packet page and populate the label print form
 */
function silicagel_zen_preprocess_node_silica_gel_packet(&$variables, $hook){

    //array('taxon' => $taxon, 'collector_number' => '1234', 'collector_name' => 'Roger Hyam', '')

    $node = $variables['node'];
    $formParams = array();
    $formParams['nid'] = $node->nid;
    $formParams['barcode'] = $node->field_barcode['und'][0]['value'];
    
    $keyType = get_lookup_key_type($node->field_data_lookup_key['und'][0]['value']);
    switch ($keyType) {
        case 'HERBARIUM':
            $formParams['taxon'] = empty($node->field_herbarium_taxon['und'][0]['value']) ? '' : $node->field_herbarium_taxon['und'][0]['value'];
            $formParams['collection_date'] = empty($node->field_herbarium_date['und'][0]['value']) ? '000-00-00' : $node->field_herbarium_date['und'][0]['value'];                     
            //$formParams['collector_name'] = empty($node->field_herbarium_collector_name['und'][0]['value']) ? '' : $node->field_herbarium_collector_name['und'][0]['value'];
            //$formParams['collector_number'] = empty($node->field_herbarium_collector_number['und'][0]['value']) ? '' : $node->field_herbarium_collector_number['und'][0]['value'];
            break;
            
        case 'ACCESSION':
        case 'PLANT':
            $formParams['taxon'] = empty($node->field_living_taxon['und'][0]['value']) ? '' : $node->field_living_taxon['und'][0]['value'];
            $formParams['collection_date'] = empty($node->field_living_date['und'][0]['value']) ? '000-00-00' : $node->field_living_date['und'][0]['value'];
            //$formParams['collector_name'] = empty($node->field_living_collector_name['und'][0]['value']) ? '' : $node->field_living_collector_name['und'][0]['value'];
            //$formParams['collector_number'] = empty($node->field_living_accession_number['und'][0]['value']) ? '' : $node->field_living_accession_number['und'][0]['value'];
            break;
            
        case 'COLL_BOOK':
            $formParams['taxon'] = empty($node->field_coll_books_taxon['und'][0]['value']) ? '' : $node->field_coll_books_taxon['und'][0]['value'];
            $formParams['collection_date'] = empty($node->field_coll_books_date['und'][0]['value']) ? '000-00-00' : $node->field_coll_books_date['und'][0]['value'];            
            //$formParams['collector_name'] = empty($node->field_coll_books_collector_name['und'][0]['value']) ? '' : $node->field_coll_books_collector_name['und'][0]['value'];
            //$formParams['collector_number'] = empty($node->field_coll_books_coll_num['und'][0]['value']) ? '' : $node->field_coll_books_coll_num['und'][0]['value'];
            break;

        default:
            $formParams['taxon'] = 'default';
            $formParams['collection_date'] = '0000-00-00';
            //$formParams['collector_number'] = 'default';
            //$formParams['collector_name'] = 'default';
            break;
    }

    // we always have the same creation date
    $formParams['creation_date'] = date('Y-m-d',$node->created);
    
    // always have a data lookup key
    $formParams['data_lookup_key'] = $node->field_data_lookup_key['und'][0]['value'];
    
   // dpm($node->field_storage_location);

    if(isset($node->field_storage_location['und'])){
        $storage_location_tid = $node->field_storage_location['und'][0]['tid'];
        $storage_path_terms = taxonomy_get_parents_all($storage_location_tid);
        foreach($storage_path_terms as $term){
            $storage_path_words[] = $term->name;
        }
        $storage_path_words = array_reverse($storage_path_words);
        $formParams['storage_location'] = implode(' ', $storage_path_words);
    }

    
    // finally set the rendered form in the variables
    $form = drupal_get_form('silicagel_collection_label_form', $formParams);
    $variables['label_form'] = render($form);

}


function silicagel_zen_preprocess_search_results(&$vars){
    
    // if we only have one result send them to the node
    // this makes the barcode search behavior better
    if(count($vars['results']) == 1){
        $nid = $vars['results'][0]['node']->nid;
        drupal_goto("/node/$nid");
    }

}

/**
 * Override or insert variables into the comment templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function silicagel_zen_preprocess_comment(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the region templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("region" in this case.)
 */
/* -- Delete this line if you want to use this function
function silicagel_zen_preprocess_region(&$variables, $hook) {
  // Don't use Zen's region--sidebar.tpl.php template for sidebars.
  //if (strpos($variables['region'], 'sidebar_') === 0) {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('region__sidebar'));
  //}
}
// */

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
/* -- Delete this line if you want to use this function
function silicagel_zen_preprocess_block(&$variables, $hook) {
  // Add a count to all the blocks in the region.
  // $variables['classes_array'][] = 'count-' . $variables['block_id'];

  // By default, Zen will use the block--no-wrapper.tpl.php for the main
  // content. This optional bit of code undoes that:
  //if ($variables['block_html_id'] == 'block-system-main') {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('block__no_wrapper'));
  //}
}
// */
