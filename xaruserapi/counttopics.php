<?php
/**
 * Count the number of topics for a given forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 * @todo fid should not have to be mandatory, also what about IP, authors, etc?
 * @todo Move this count to the admin API, since it does a raw count with no privilege checks
 * @note Counting topics against a range of criteria, with privilege checks, is done in user api getalltopics()
 */

function xarbb_userapi_counttopics($args)
{
    extract($args);

    if (!isset($fid)) {
        $msg = xarML('Invalid parameter count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xbbtopicstable = $xartable['xbbtopics'];
    $query = "SELECT COUNT(1) FROM $xbbtopicstable WHERE xar_fid = ?";
    $result =& $dbconn->Execute($query, array((int)$fid));

    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;
}

?>