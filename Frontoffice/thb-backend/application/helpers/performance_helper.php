<?php
function check_indexing() {
    $ci=& get_instance();
    
    $queries = $ci->db->queries;
    for ($i=0; $i < count($queries); $i++) { 
        $query = $queries[$i];

        // Use regex to match the first word (SELECT)
        if (preg_match('/\bSELECT\b/i', $query)) {
            $explains = $ci->db->query('EXPLAIN ' . $query)->result();
            for ($j=0; $j < count($explains); $j++) { 
                $type = $explains[$j]->type ?? null;

                if ($type != 'ref' && $type != 'eq_ref' && $type != 'range' && $type != 'const' && $type != 'index_merge') {
                    var_dump($type);
                    var_dump($query);
                    die();
                }
            }
        }
    }
}