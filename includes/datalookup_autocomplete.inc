<?php

function silicagel_collection_datalookup_autocomplete($term =""){
    
    $limit = 10;
    
    $matches = array();    
        
    // do the herbarium barcode 
    if(preg_match('/^E[0-9]{0,8}$/i', $term)){
        
        // switch to the correct database
         db_set_active('bgbase_dump');

        // get the row
        $query = db_select('specimens', 's');
        $return = $query
            ->fields('s', array('barcode'))
            ->condition('s.barcode', db_like($term) . '%', 'LIKE')
            ->range(0, $limit)
            ->execute();

        foreach ($return as $row) {
          $matches[$row->barcode] = check_plain($row->barcode);
        }

        // switch back db
        db_set_active();
        
        
    }
     
    // lookup an accession   
    if(preg_match('/^[0-9]{1,8}/', $term)){
                
        $sql = "SELECT concat_ws('', a.ACC_NUM, p.ACC_NUM_QUAL) as identifier
        FROM bgbase_dump.accessions as a 
        LEFT JOIN bgbase_dump.`plants` as p
        ON a.ACC_NUM = p.ACC_NUM
        WHERE a.ACC_NUM like '$term%' 
        ORDER BY concat_ws('', a.ACC_NUM, p.ACC_NUM_QUAL) 
        LIMIT $limit";
        
        
        // switch to the correct database
         db_set_active('bgbase_dump');
        
        $result = db_query($sql);

        foreach($result as $row){
            
            if(count($matches) > $limit) break;

            // if this is a plant we look to see if we have an accession already
            // in the list for that plant and if not we add it.
            if(strlen($row->identifier) > 8){
                $accNum = substr($row->identifier, 0, 8);
                $matches[$accNum] = $accNum;
            }
            $matches[$row->identifier] = $row->identifier;

        }

        // switch back db
        db_set_active();
        
    }
    
    // lookup an coll_book   
    if(preg_match('/^[A-Za-z]+/', $term)){
                
        $sql = "SELECT concat_ws(':', cb.COLL_ID, cb.COLL_NUM) as identifier
        FROM coll_books as cb 
        WHERE concat_ws(':', cb.COLL_ID, cb.COLL_NUM) like '$term%' 
        ORDER BY concat_ws(':', cb.COLL_ID, cb.COLL_NUM)
        LIMIT $limit";
        
        
        // switch to the correct database
         db_set_active('bgbase_dump');
        
        $result = db_query($sql);

        foreach($result as $row){
            $matches[$row->identifier] = $row->identifier;
        }

        // switch back db
        db_set_active();
        
    }
    
    drupal_json_output($matches);
    
    
}
