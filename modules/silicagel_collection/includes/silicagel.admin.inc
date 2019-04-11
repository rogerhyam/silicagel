<?php

function silicagel_printer_form(){
    
    $form = array();
    
    $form['silicagel_printer_ip_address'] = array(
        '#type' => 'textfield',
        '#title' => 'Printer IP address',
        '#description' => 'You can find out the IP address of the printer from the front panel under settings. It should be fixed or change rarely.',
        '#default_value' => variable_get('silicagel_printer_ip_address', '192.168.7.170'),
        '#required' => true
    );
    
    $form['silicagel_printer_port_number'] = array(
        '#type' => 'textfield',
        '#title' => 'Printer Port Number',
        '#description' => 'It is very unlikely you will need to change this from 9100',
        '#default_value' => variable_get('silicagel_printer_port_number', 9100),
        '#required' => true
    );
    
    $form['silicagel_zpl_label_template'] = array(
        '#type' => 'textarea',
        '#title' => 'Label Template',
        '#description' => 'This is the ZPL template for the label format',
        '#rows'  => 20,
        '#default_value' => variable_get('silicagel_zpl_label_template', '')
    );
    
    $form['silicagel_zpl_label_template_debug'] = array(
        '#type' => 'checkbox',
        '#title' => 'Template Debug Mode',
        '#description' => 'If this is on then the output from the ZPL template will be displayed rather than sent to the printer.',
        '#default_value' => variable_get('silicagel_zpl_label_template_debug', '0')
    );
    
    return system_settings_form($form); // this handles the saving
    
}

function silicagel_printer_form_validate($form, &$form_state){
    
    // check they have given us an ip number
    if (!ip2long( $form_state['values']['silicagel_printer_ip_address'] )){
        form_set_error('silicagel_printer_ip_address', "Sorry. This doesn't look like an IP address to me");
    }
    
    $i = $form_state['values']['silicagel_printer_port_number'];
    if (!is_numeric($i) || $i < 1 || $i != round($i)) {
       form_set_error('silicagel_printer_port_number', "Sorry. This doesn't look like a valid port number to me.");
    }
    
    // ping the printer to check it is on line
    $ip = $form_state['values']['silicagel_printer_ip_address'];
    $pingresult = exec("/bin/ping -c 1 $ip", $outcome, $status);
    if (0 == $status) {
        drupal_set_message('Successfully pinged the printer. It appears to be on-line and reachable.');
    } else {
        drupal_set_message("Couldn't ping the printer. Is it on-line? Is the IP address correct?", 'warning');
    } 
    
}


