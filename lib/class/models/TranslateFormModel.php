<?php
  namespace models;
  
  class TranslateFormModel {
    private $_languages;
    private $_wordClasses;
    private $_wordGenders;
    private $_original;
    
    public function __construct() {
      $this->_languages   = \data\entities\Language::getLanguageArray(true);
      $this->_wordClasses = \data\entities\Word::getWordClasses();
      $this->_wordGenders = \data\entities\Word::getWordGenders();
      $this->_original    = new \data\entities\Translation();
      
      if (isset($_GET['translationID'])) {
        $id = (integer) $_GET['translationID'];
        $this->_original->load($id);
      }
    }
    
    public function getLanguages() {
      return $this->_languages;
    }
    
    public function getWordClasses() {
      return $this->_wordClasses;
    }
    
    public function getWordGenders() {
      return $this->_wordGenders;
    }
    
    public function &getOriginal() {
      return $this->_original;
    }
  }
