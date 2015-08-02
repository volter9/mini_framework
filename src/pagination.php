<?php namespace pagination;

use db;

/**
 * Pagination utilities
 * 
 * @package mini_framework
 * @require database
 */

/**
 * Clamp a integer $int between $min and $max
 * 
 * @param int $int
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
    
    $half = ceil($limit / 2);
    
    $start = (int)clamp($center - $half, $min, $max);
    $end   = (int)clamp($start + $limit - ($limit % 2), $min, $max);
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
 * @param int $limit - Show pages limit
 * @return array
 */
function generate ($total, $items, $page, $limit = 9) {
    $offset = ($page - 1) * $items;
    
    $pages = (int)ceil($total / $items);
    $page  = clamp($page, 1, $pages);
    
    $pagination = limited_range($page, $limit, 1, $pages);
    
    $limit = $items;
    
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
    
    $count = current(db\select($countQuery, $data, true));
    
    $pages = generate($count, $limit, $page);
    
    $data[] = $pages['limit'];
    $data[] = $pages['offset'];
    
    return array(
        'items' => db\select("$query LIMIT ? OFFSET ?", $data),
        'pages' => $pages,
    );
}

/**
 * Replace selects by COUNT(*) 
 * 
 * @param string $query
 * @return string
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
 * @return string
 */
function pagiante_query_remove_joins ($query) {
    static $regex = '/(left|inner|outer|right) join [\w\d\s`]+ ON \([^\)]+\)/i';
    
    return preg_replace($regex, ' ', $query);
}