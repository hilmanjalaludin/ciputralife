<?php

/**
* filesplit class : Split big text files in multiple files
*
* @package
* @author Ben Yacoub Hatem <hatem@php.net>
* @copyright Copyright (c) 2004
* @version $Id$ - 29/05/2004 09:02:10 - filesplit.class.php
* @access public
**/
class filesplit{
    /**
     * Constructor
     * @access protected
     */
    function filesplit(){

    }

    /**
     * File to split
     * @access private
     * @var string
     **/
    var $_source = 'logs.txt';

    /**
     *
     * @access public
     * @return string
     **/
    function Getsource(){
        return $this->_source;
    }

    /**
     *
     * @access public
     * @return void
     **/
    function Setsource($newValue){
        $this->_source = $newValue;
    }

    /**
     * how much lines per file
     * @access private
     * @var integer
     **/
    var $_lines = 1000;

    /**
     *
     * @access public
     * @return integer
     **/
    function Getlines(){
        return $this->_lines;
    }

    /**
     *
     * @access public
     * @return void
     **/
    function Setlines($newValue){
        $this->_lines = $newValue;
    }

    /**
     * Folder to create splitted files with trail slash at end
     * @access private
     * @var string
     **/
    var $_path = 'logs/';

    /**
     *
     * @access public
     * @return string
     **/
    function Getpath(){
        return $this->_path;
    }

    /**
     *
     * @access public
     * @return void
     **/
    function Setpath($newValue){
        $this->_path = $newValue;
    }

    /**
     * Configure the class
     * @access public
     * @return void
     **/
    function configure($source = "",$path = "",$lines = ""){
        if ($source != "") {
            $this->Setsource($source);
        }
        if ($path!="") {
            $this->Setpath($path);
        }
        if ($lines!="") {
            $this->Setlines($lines);
        }
    }


    /**
     *
     * @access public
     * @return void
     **/
    function run(){
        $i=0;
        $j=1;
        $date = date("m-d-y");
        unset($buffer);

        $handle = @fopen ($this->Getsource(), "r");
        while (!feof ($handle)) {
          $buffer .= @fgets($handle, 4096);
          $i++;
              if ($i >= $split) {
              $fname = $this->Getpath()."part.$date.$j.txt";
               if (!$fhandle = @fopen($fname, 'w')) {
                    print "Cannot open file ($fname)";
                    exit;
               }

               if (!@fwrite($fhandle, $buffer)) {
                   print "Cannot write to file ($fname)";
                   exit;
               }
               fclose($fhandle);
               $j++;
               unset($buffer,$i);
                }
        }
        fclose ($handle);
    }
}
?>
