<?php

/**
 * Clamp a integer $int between $min and $max
 * 
 * @param int $x
 * @param int $min
 * @param int $max
 * @return int
 */
function clamp ($int, $min, $max) {
    $int = max($int, $min);
    
    return min($int, $max);
}

/**
 * Create a limited sequence
 * 
 * @param int $center
 * @param int $limit
 * @param int $min
 * @param int $max
 * @return int
 */
function limited_range ($center, $limit, $min, $max) {
    if ($limit < 2) {
        return array();
    }
    
    $half  = intval($limit / 2);
    $start = (int)clamp($center - $half, $min, $max);
    $end   = (int)clamp($center + $half, $min, $max);
    $range = range($start, $end);
    
    array_splice($range,  0, 1, (int)$min);
    array_splice($range, -1, 1, (int)$max);
    
    return $range;
}

/**
 * Generates pagination array
 * 
 * @param int $total - Total of rows/items
 * @param int $items - Items per page
 * @param int $page  - Page
 * @return array
 */
function pagination_generate ($total, $items, $page) {
    $offset = ($page - 1) * $items;
    
    $pages = ceil($total / $items);
    $page  = clamp($page, 1, $pages);
    
    $limit = clamp($pages, 1, 9);
    $pagination = limited_range($page, $limit, 1, $pages);
    
    $limit = intval($items - $offset % $items);
    
    return compact('offset', 'pages', 'page', 'pagination', 'limit');
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
    
    $count = current(db_select($countQuery, $data, true));
    
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