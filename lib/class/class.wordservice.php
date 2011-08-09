<?php
  class WordService extends RESTfulService {
    public function handleRequest(&$data) {
      throw new ErrorException('Parameterless request presently unsupported.');
    } 
    
    public function handleParameterizedRequest(&$data, $param = null) {
      if (is_numeric($param)) {
        return self::getWord($param);
      }
      
      switch ($param) {
        case 'search':
          return self::searchWord($data);
          
        default:
          throw new ErrorException("Unrecognised command '".$param."'.");
      }
    }
    
    private function getWord($id) {
      $word = new Word();
      $word->load($id);
      
      if ($word->id < 1) {
        return null;
      }
      
      return $word;
    }
    
    private function searchWord(&$data) {
      if (!isset($data['term'])) {
        throw new ErrorException("Missing parameter 'term'.");
      }
    
      $term = $data['term'];
      $data = array();
      
      if (strlen($term) > 0) {
        $db = Database::instance();
        
        $query = $db->connection()->prepare(
          "SELECT DISTINCT `Key`
            FROM `word` w
            WHERE `Key` LIKE ?
            ORDER BY `Key` ASC
            LIMIT 8"
        );
        
        if (strpos($term, '*') !== false) {
          $term = str_replace('*', '%', $term);
        } else {
          $term .= '%';
        }
        
        $query->bind_param('s', $term);
        $query->execute();
        
        $query->bind_result($key);
        while ($query->fetch()) {
          $data[] = $key;
        }
        
        $query->close();
      }
      
      return $data;
    }
  }
?>