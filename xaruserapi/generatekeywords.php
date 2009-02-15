<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * create a keyword list from a given article
 *
 * @param $args array containing text from an article
 * @return string
 */
function publications_userapi_generatekeywords($args)
{
    extract($args);

    // Strip -all- html
    $htmlless = strip_tags($incomingkey);

    // Strip anything that isn't alphanumeric or _ -
    $symbolLess = trim(ereg_replace('([^a-zA-Z0-9_-])+',' ',$htmlless));

    // Remove duplicate words
    $keywords = explode(" ", strtolower($symbolLess));
    $keywords = array_unique($keywords);

    $list = array();
    // Remove words that are < four characters in length
    foreach($keywords as $word) {
        if (strlen($word) >= 4 && !empty($word)) {
            $list[] = $word;
        }
    }
    $keywords = $list;

    // Sort the list of words in Ascending order Alphabetically
    sort($keywords, SORT_STRING);

    // Merge the list of words into a single, comma delimited string of keywords
    $keywords = implode(",",$keywords);

    return $keywords;
}

?>