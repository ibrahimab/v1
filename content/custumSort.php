<?php 
mb_internal_encoding("UTF-8"); 

class utf_8_german 
{ 
  // everything else is sorted at the end 
  static $order = '0123456789AaÄäBbCcDdEeFfGgHhIiJjKkLlMm 
NnOoÖöPpQqRrSsßTtUuÜüVvWwXxYyZz'; 
  static $char2order; 
  
  static function cmp($a, $b) { 
    if ($a == $b) { 
        return 0; 
    } 
    
    // lazy init mapping 
    if (empty(self::$char2order)) 
    { 
      $order = 1; 
      $len = mb_strlen(self::$order); 
      for ($order=0; $order<$len; ++$order) 
      { 
        self::$char2order[mb_substr(self::$order, $order, 1)] = $order; 
      } 
    } 
    
    $len_a = mb_strlen($a); 
    $len_b = mb_strlen($b); 
    $max=min($len_a, $len_b); 
    for($i=0; $i<$max; ++$i) 
    { 
      $char_a= mb_substr($a, $i, 1); 
      $char_b= mb_substr($b, $i, 1); 
      
      if ($char_a == $char_b) continue; 
      $order_a = (isset(self::$char2order[$char_a])) ? self::$char2order[$char_a] : 9999; 
      $order_b = (isset(self::$char2order[$char_b])) ? self::$char2order[$char_b] : 9999; 
      
      return ($order_a < $order_b) ? -1 : 1; 
    } 
    return ($len_a < $len_b) ? -1 : 1; 
  } 
} 