<?php
  namespace App\Helpers;
  
  class StringHelper {
    private function __construct() {
      // Disable construction
    }
  
    public static function preventXSS(string $str, $encoding = 'UTF-8') {
      return htmlspecialchars($str, ENT_QUOTES | ENT_HTML401, $encoding);
    }
    
    public static function normalize(string $str) {
      $currentLocale = setlocale(LC_ALL, 0);
      
      // This is necessary for the iconv-normalization to function properly
      setlocale(LC_ALL, 'de_DE.UTF8');
      
      $normalizedStr = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
      
      // restore the locale
      setlocale(LC_ALL, $currentLocale);
      
      return trim(mb_convert_case($normalizedStr, MB_CASE_LOWER, 'utf-8'));
    }

    public static function normalizeForUrl(string $str) {
      $str = self::normalize($str);

      // Replace white space with underscore
      $str = str_replace(' ', '_', $str);

      // Remove all non-alphabetic and non-numeric characters
      $str = preg_replace('/[^0-9a-z_]/', '', $str);

      return $str;
    }
    
    // Replaces all [[textual content]] with anchors <a href="#textual+content">textual content</a>
    public static function createLinks($str) {
      $str = self::preventXSS($str);

      $matches = null;
      
      // This is a bloody mess - this needs to be cleaned up. Essentially this is what is done:
      // Each regular expression defines a markup tag, that can be used to apply markup to the
      // associated string parameter.
      //
      // Either there is a tag name, meaning simply that there won't be any parameters or any
      // such like defining additional information to the tag itself. This is ideal for tags
      // trivial, such as _em_ and _u_.
      //
      // The (un)fortunate second case, is non-trivial tags with parameters, such as links and
      // anchors. These require some sort of interaction with the initial result set, and even
      // additional functionality in terms of URL-escaping and string formatting through built-in
      // or user-defined methods. These are marked as follows:
      // {index in resultset[:formatting method]}
      // So {1:urlencode} grabs the first item in the resultset and invokes urlecode on the result
      // and replaces the appropriate tag block with the formatted value.
      //
      // This might be an performance issue, and might need to be optimised.
      $regs = array(
        '/_([^_]*)_/' => array('tag' => 'em'),
        '/~([^~]*)~/' => array('tag' => 'u'),
        '/\\`([^\\`]+)\\`/' => array('tag' => 'strong'),
        '/\\[\\[([^\\]]+)\\]\\]/' => array('tagStart' => 'a href="/w/{{1:\\utils\\StringWizard::createLink}}" class="ed-ref"', 'tagEnd' => 'a')
      );   
      
      foreach ($regs as $reg => $data) {
        if (preg_match_all($reg, $str, $matches)) {
          $matches_c = count($matches[0]);
          for ($i = 0; $i < $matches_c; ++$i) {
            $tagStart = null;
            $tagEnd   = null;
            
            if (isset($data['tag'])) {
              $tagStart = $data['tag'];
            } else if (isset($data['tagStart'])) {
              $tagStart = $data['tagStart'];
              
              $subMatches = null;
              if (preg_match_all('/\\{\\{([0-9]+)\:?([a-zA-Z\\\\:]+)?\\}\\}/', $tagStart, $subMatches)) {
                $subMatches_c = count($subMatches[0]);
                
                for ($j = 0; $j < $subMatches_c; ++$j) {
                  $subData = $matches[$subMatches[1][$j]][$i];
                  
                  if (isset($subMatches[2]) && isset($subMatches[2][$j])) {
                    $subData = call_user_func($subMatches[2][$j], $subData);
                  }
                  
                  $tagStart = str_replace($subMatches[0][$j], $subData, $tagStart);
                }
              }
              
              if (isset($data['tagEnd'])) {
                $tagEnd = $data['tagEnd'];
              }
            }
            
            if ($tagEnd === null) {
            	$tagEnd = $tagStart;
            }
            
            $str = str_replace(
              $matches[0][$i], 
              '<'.$tagStart.'>'.$matches[1][$i].'</'.$tagEnd.'>', 
              $str
            );
          }
        }
      }
      
      $str = preg_replace(
        '/&gt;&gt;([^\\0\\n\\r]+)/', 
        '<div><img src="/img/hand.png" alt="See also" border="0" /> \\1</div>',
        $str
      );
      
      return $str;
    }
  
    public static function createLink($s) {
      return rawurlencode($s);
    }
  }