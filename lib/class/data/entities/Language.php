<?php
  namespace data\entities;
  
  class Language extends Entity {
    public $id;
    public $invented;
    public $name;
  
    public function __construct($data = null) {
      parent::__construct($data);
    }
    
    public function validate() {
      return true;
    }
    
    public function load($numericId) {
      return $this;
    }
    
    public function save() {
      return $this;
    }
    
    public static function getAllLanguages() {
      $db = \data\Database::instance()->connection();
      $data = array();
      
      $res = $db->query(
        'SELECT `ID`, `Name`, `Invented` FROM `language` ORDER BY `Order` ASC'
      );
      
      while ($language = $res->fetch_object()) {
        $data[] = new Language(array('id'       => $language->ID, 
                                     'name'     => $language->Name, 
                                     'invented' => $language->Invented));
      }
      
      $res->close();
      
      return $data;
    }
    
    public static function getLanguageArray($excludeRealLanguages = true) {
      $originalData = self::getAllLanguages();
      $data = array();
      
      foreach ($originalData as $language) {
        if ($excludeRealLanguages && !$language->invented) {
          continue;
        }
        
        $data[$language->id] = $language->name;
      }
      
      return $data;
    }
  }
