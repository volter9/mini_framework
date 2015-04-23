<?php

/**
 * Generates pagination array
 * 
 * @param int $total - Total of rows/items
 * @param int $ipp   - Items Per Page
 * @param int $page  - Page
 * @return array
 */
function pagination_generate ($total, $ipp, $page) {
    $offset = 0;
    $pages = ceil($total / $ipp);
    $pagination = array();
    
    if ($page >= $pages) {
        $page = $pages;
    }
    
    if ($total > $ipp) {
        $offset = ($page - 1) * $ipp;
    }
    
    if ($pages > 1) {
        for ($i = 0, $c = ($pages > 9) ? 9 : $pages; $i < $c; $i++) {
            if ($pages <= $c) {
                $pagination[] = $i + 1;
            }
            else {
                if ($i === 0) {
                    $pagination[$i] = 1;
                }
                else if ($i === $c - 1) {
                    $pagination[$i] = $pages;
                }
                else {
                    $cell = $page - ceil(($c - 1) / 2) + $i;
                    
                    if ($cell > $pages - 1 || $cell < 2) {
                        continue;
                    }
                    
                    $pagination[] = $cell;
                }
            }
        }
    }
    
    $limit = (int)($ipp - ($offset % $ipp))
    
    return compact('limit', 'offset', 'pages', 'page', 'pagination');
}

/**
 * Paginate a query
 * 
 * @param string $query
 * @param array $data
 * @param int $limit
 * @param int $page
 * @return array
 */
function paginate_query ($query, array $data, $limit, $page) {
    $countQuery = paginate_query_replace_select($query);
    $countQuery = pagiante_query_remove_joins($countQuery);
    
    $count = db_query($countQuery, $data, DB_AGGREGATE);
    
    $pages = pagination_generate($count, $limit, $page);
    $query .= ' LIMIT ? OFFSET ?';
    
    $data[] = $pages['limit'];
    $data[] = $pages['offset'];
    
    return array(
        'items' => db_select($query, $data),
        'pages' => $pages,
    );
}

/**
 * Replace selects by COUNT(*) 
 * 
 * @param string $query
 * @return $query
 */
function paginate_query_replace_select ($query) {
    $from = stripos($query, 'from');
    $rest = substr($query, $from);
    
    return "SELECT COUNT(*) $rest";
}

/**
 * Remove joins
 * 
 * @param string $query
 * @return $query
 */
function pagiante_query_remove_joins ($query) {
    static $regex = '/(left|inner|outer|right) join [\w\d\s]+ ON \([^\)]+\)/i';
    
    return preg_replace($regex, ' ', $query);
}