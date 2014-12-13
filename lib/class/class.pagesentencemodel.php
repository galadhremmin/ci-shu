<?php
  if (!defined('SYS_ACTIVE')) {
    exit;
  }
  
  class PageSentenceModel {
    private $_sentences;
    
    public function __construct() {
      $db = Database::instance();
      
      $query = $db->connection()->query(
        'SELECT s.`SentenceID`, l.`Name` AS `Language`, s.`Description`, f.`Fragment`, f.`TranslationID`, f.`FragmentID`, f.`Comments`
         FROM `sentence` s
           INNER JOIN `language` l ON l.`ID` = s.`LanguageID`
           INNER JOIN `sentence_fragment` f ON f.`SentenceID` = s.`SentenceID`
         ORDER BY f.`Order` ASC'
      );
          
      $this->_sentences = array();
      while ($row = $query->fetch_object()) {
        
        // Create a sentence if it hasn't previously been recorded.
        if (!isset($this->_sentences[$row->SentenceID])) {
          $sentence = new Sentence($row->SentenceID, $row->Language, $row->Description);
          $this->_sentences[$row->SentenceID] = $sentence;
        }
        
        $this->_sentences[$row->SentenceID]->fragments[] = new SentenceFragment($row->FragmentID, $row->Fragment, $row->TranslationID, $row->Comments);
      }
      
      // Coalesce all fragments into sentences
      foreach ($this->_sentences as $sentence) {
        $sentence->create();
      }
      
      $query->close();
    }
    
    public function getSentences() {
      return $this->_sentences;
    }
  }
  
  class Sentence  {
    public $ID;
    public $language;
    public $fragments;
    public $sentence;
    public $description;
    
    public function __construct($id, $language, $description) {
      $this->ID = $id;
      $this->language = $language;
      $this->fragments = array();
      $this->description = $description;
      $this->sentence = '';
    }
    
    public function create() {
      $fragments = array();
      foreach ($this->fragments as $fragment) {
        if (!preg_match('/^[,\\.!\\s]$/', $fragment->fragment)) {
          $fragments[] = ' ';
        }
        
        if (is_numeric($fragment->translationID)) {
          $html = '<a href="#" data-fragment-id="'.$fragment->fragmentID.'" data-translation-id="'.$fragment->translationID.'">'.$fragment->fragment.'</a>';
        } else {        
          $html = $fragment->fragment;
        }
        
        $fragments[] = $html;
      }
      
      $this->sentence = implode($fragments);
    }
  }
  
  class SentenceFragment {
    public $fragmentID;
    public $translationID;
    public $fragment;
    public $comments;
    
    public function __construct($fragmentID, $fragment, $translationID, $comments) {
      $this->fragmentID = $fragmentID;
      $this->fragment = $fragment;
      $this->translationID = $translationID;
      $this->comments = $comments;
    }
  }
?>
