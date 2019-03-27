<?php

function silicagel_collection_label_form_submit($form, $form_state){
    
    return "I am in print label";

}


// THIS IS ORPHAN CODE - I THINK!

function silicagel_print_label($nid){

    // get the node to print
    $node = node_load($nid);
    
    $fieldVals = array();
    
    // barcode
    $fieldVals['{barcode}'] = $node->field_barcode['und'][0]['value'];
    $fieldVals['{barcode}'] = substr(0,9,  $fieldVals['{barcode}']); // only nine digits
    $fieldVals['{barcode}'] = 'XXXXXX';
    
    
    dmp($node);
    
    // taxon name - get shortened version?
    if(!empty($node->field_herbarium_taxon['und'][0]['value'])){
        $fieldVals['{taxon}'] = $node->field_herbarium_taxon['und'][0]['value'];
    }elseif (!empty($node->field_living_taxon['und'][0]['value'])){
       $fieldVals['{taxon}'] = $node->field_living_taxon['und'][0]['value'];
    }elseif (!empty($node->field_coll_books_taxon['und'][0]['value'])){
        $fieldVals['{taxon}'] = $node->field_coll_books_taxon['und'][0]['value'];
    }else{
        $fieldVals['{taxon}'] = "";
    }
    
    $printerCommands = populate_template(variable_get('silicagel_zpl_label_template'), $fieldVals);

    if(variable_get('silicagel_zpl_label_template_debug', '0')){    
        echo "<pre>";
        echo $printerCommands;
        echo "</pre>";
    }else{
        
        // ok - let's send it to the printer
        /* Turn on implicit output flushing so we see what we're getting
            * as it comes in. */
       ob_implicit_flush();
       $ip = variable_get('silicagel_printer_ip_address');
       $port = variable_get('silicagel_printer_port_number');
    
    
       /* Create a TCP/IP socket. */
       $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
       if ($socket === false) {
           echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
       }
       
       $result = socket_connect($socket, $ip, $port);
       if ($result === false) {
           echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
       }
    
       socket_write($socket, $printerCommands, strlen($printerCommands));    
       //echo socket_read($socket, 2048);
       socket_close($socket);
       echo "Print job sent for: " . $node->field_barcode['und'][0]['value'];
    }
    
    drupal_exit();

}

function populate_template($template, $fieldVals){
    
    $result = $template;
    
    foreach($fieldVals as $key => $val){
        
        $result = str_replace($key, $val, $result);
        
    }
    
    return $result;
    
}



