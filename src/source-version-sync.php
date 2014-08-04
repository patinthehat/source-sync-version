#!/usr/bin/php
<?php

/**
 *  version-update.php --
 *  
 *    Most of the Slim source files have a @version tag, but no easy way
 *    to keep them all synchronized.  This updates all files _(optionally)_
 *    in `Slim/` and `tests/`.  It updates @version tags amd the `const VERSION`
 *    declaration in `\Slim`.
 *    The version to update to is pulled from the file `VERSION`., a text
 *    file containing only the new version number.  No checking is done other
 *    than its existence. 
 *    
 *    The script executes via `main()`, and will return with an error if no `VERSION`
 *    file is found.
 * 
 *    Configuration is done within the script itself, modifying the `$opts` variable.
 *    Settings are self-explainatory; 1 = ON, 0 = OFF.
 *    
 *    A note on 'TEST_MODE':  enabling this feature will write the updated contents to
 *      `VERSION-TEST/filename.tmp`` 
 *    
 *    ---
 *    
 *    TODO: 
 *      Command-line configuration
 *      Better version-string checking
 *      Checking the @package tag before updating @version (though tests don't have this tag)
 *      updating `the value of define(VERSIOIN)` declarations
 *       
 * 
 * 
 * 
 * 
 */

$opts = array(
    'TEST_MODE'           => 1,
    'VERBOSE_UPDATE'      => 1,
    'UPDATE_SLIM_SOURCE'  => 1,
    'UPDATE_SLIM_TESTS'   => 0,
);

if ($argv[1] == "TEST") 
  $opts['TEST_MODE'] = 1;




  define('DS',                DIRECTORY_SEPARATOR);
  define('BASEDIR',           dirname(__FILE__));
  define('VERSION_FILE',      'VERSION');
  define('VERSION_FN',        BASEDIR.DS.VERSION_FILE);
  
  if (have_version_file()) {
    define('NEW_VERSION',       file_get_contents(VERSION_FN));
  } else {
    define('NEW_VERSION',       null);
  }
  
  
  define('SUCCESS',            0);
  define('ERR_SUCCESS',        0);
  define('ERR_NO_VERSION',    -1);
  

  function have_version_file() {
    if (file_exists(VERSION_FN))
      return true;
    exit(ERR_NO_VERSION);
  }

  function get_updated_version() {
    return trim(file_get_contents(VERSION_FN));
  }
  
  function get_files_list($path, $type = "*.php") {
    $dirs = new \RecursiveDirectoryIterator($path);
    $ret = array();
    foreach (new RecursiveIteratorIterator($dirs) as $filename=>$current) {
      if (!$current->isDir() ){
        if (fnmatch($type, $filename))
          $ret[] = ($filename);
      }    
    }
    return $ret;    
  }
  
  function process_files($files) {
    global $opts;    
    foreach($files as $fn) {
      if ($opts['VERBOSE_UPDATE'])
        echo "Processing $fn" . PHP_EOL;
            
      $fn = realpath($fn);
      $contents = file_get_contents($fn); 
      $contents = preg_replace('/\@version([ \s\t]{1,})([0-9\.\-A-Za-z]{4,})/', "\@version    ".NEW_VERSION, $contents);;
      $contents = preg_replace('/VERSION[ \t]{0,}=[ \t]{0,}\'[0-9\.\-A-Za-z]{4,}\'/', "VERSION = '".NEW_VERSION."'", $contents);
      
      if ($opts['TEST_MODE']==1) {
        //echo "[simulated] file-put-contents($fn)".PHP_EOL;
      } else {
        //NOT TEST MODE
        if ($opts['TEST_MODE']!==1) 
          if (file_put_contents("$fn", $contents) === FALSE) { 
            throw new \Exception("file_put_contents() FAILED.");
          }
      }
    }    
  }

  
  
function main() {
  global $opts;
  
  if (!have_version_file() || NEW_VERSION == null) { 
    echo "# No VERSION file exists, cannot perform updates.". PHP_EOL;
    return ERR_NO_VERSION;
  }
  
  if ($opts['TEST_MODE']==1)
    echo "# RUNNING IN TEST MODE" . PHP_EOL;
  
  echo "# NEW_VERSION = ".NEW_VERSION. PHP_EOL;

  if ($opts['UPDATE_SLIM_SOURCE']) {
    echo "Updating version data in Slim source...";
    process_files(get_files_list('./Slim', "*"));
    echo "done.". PHP_EOL;
  }
  
  if ($opts['UPDATE_SLIM_TESTS']) {
    echo "Updating version data in Slim tests...". PHP_EOL;
    process_files(get_files_list('./tests', "*"));
    echo "done.". PHP_EOL;
  }    

  return SUCCESS;
}


$retCode = main();
exit($retCode);

  