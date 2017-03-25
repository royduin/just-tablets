<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package     CodeIgniter
 * @author      Rick Ellis
 * @copyright       Copyright (c) 2006, pMachine, Inc.
 * @license     http://www.codeignitor.com/user_guide/license.html
 * @link        http://www.codeigniter.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Taggly
 *
 * Taggly is a customizable CI library that makes creating
 * 'tag clouds' a snap. 
 *
 * @package     CodeIgniter
 * @subpackage      Libraries
 * @category        Libraries
 * @author      Gavin Vickery
 * @link        http://qompile.com/resources-downloads
 */
class Taggly
{

    var $htmlStart;     
    var $htmlEnd;
    var $minFont        =   12;
    var $maxFont        =   48;
    var $shuffleTags    =   true;
    var $className      =   'taggly';
    var $findMatch      =array("");
    var $matchClass     ="tagglyMatch";
    /**
     * Configure Taggly options
     *
     * @access  public
     * @param   array
     */
    function config($dataArr)
    {
        $this->htmlStart = (empty($dataArr['html_start'])) ? $this->htmlStart : $dataArr['html_start'];
        $this->htmlEnd = (empty($dataArr['html_end'])) ? $this->htmlEnd : $dataArr['html_end'];
        $this->minFont = (empty($dataArr['min_font'])) ? $this->minFont : $dataArr['min_font'];
        $this->maxFont = (empty($dataArr['max_font'])) ? $this->maxFont : $dataArr['max_font'];
       // $this->shuffleTags = (empty($data['shuffle'])) ? $this->shuffleTags : $data['shuffle'];
//php5 FIX
        $this->shuffleTags = (!array_key_exists('shuffle', $dataArr) || (array_key_exists('shuffle', $dataArr) && $dataArr['shuffle'] == TRUE)) ? $this->shuffleTags : $dataArr['shuffle']; 
        $this->className = (empty($dataArr['class'])) ? $this->className : $dataArr['class'];
        $this->findMatch = (empty($dataArr['find_Match'])) ? $this->findMatch : $dataArr['find_Match'];
        $this->matchClass = (empty($dataArr['match_Class'])) ? $this->matchClass : $dataArr['match_Class'];
    }

    /**
     * Generate Tag Cloud
     *
     * @access  public
     * @param   $tagsArr -> array
     *          $dataArr -> array (optional)
     * @return  string (html)
     */
    function cloud($tagsArr, $dataArr = "")
    {
        if(!empty($dataArr))
            $this->config($dataArr);

        sort($tagsArr);
        $minCount = $tagsArr[0][0];
        $maxCount = $tagsArr[count($tagsArr) - 1][0];
        $offset = $maxCount - $minCount;
        $offset = ($offset < 1) ? 1 : $offset;
        if($this->shuffleTags)
            shuffle($tagsArr);

        $cloudArr = array();
        foreach ($tagsArr as $tag)
        {
            if(in_array($tag[1],$this->findMatch)){
                $writeClass=$this->matchClass;
            }else{
                $writeClass="";
            }
            $fontSize = $this->minFont + ($tag[0] - $minCount) * ($this->maxFont - $this->minFont) / $offset;
            $cloudArr[] =
                $this->htmlStart.
                '<a style="font-size: '.floor($fontSize).'px" ' 
                    .'class="'.$this->className .' '.$writeClass. '" '
                    .'href="'.$tag[2].'" ' 
                    .'title="' . $tag[1]  . '">' 
                    . htmlspecialchars(stripslashes($tag[1])).
                '</a>'.
                $this->htmlEnd;
        }
        return join("\n", $cloudArr) . "\n";
    }

}   // End Taggly
?>