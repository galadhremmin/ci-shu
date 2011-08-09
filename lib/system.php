<?php
  define('SYS_ACTIVE', true);

  include_once 'config/global.php';
  
  function __autoload($className) {
    $className = strtolower($className);
    $file = ROOT.'lib/class/class.'.$className.'.php';
    
    if (file_exists($file)) {
      $config = ROOT.'lib/config/config.'.$className.'.php';
      
      if (file_exists($config))
        include_once $config;
      
      include_once $file;
    
    // extension for smarty
    } else if (substr($className, 0, 6) == 'smarty') {
      smartyAutoload($className);
    }
  }
?>