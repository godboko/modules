<?php
/**
 * Get all externally added courses for one itsp
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Get all external courses that have been added to an ITSP by a student
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int numitems $ the number of items to retrieve (default -1 = all)
 * @param int startnum $ start with this item number (default 1)
 * @param int itspid The id of the ITSP to look for
 * @param int pitemid The id of the planitem to look (OPTIONAL)
 * @since 18 feb 2006
 * @return array Array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_getall_itspcourses($args)
{
    /* Get arguments from argument array */
    extract($args);
    /* Optional arguments. */
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    /* Argument check */
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (!isset($itspid) || !is_numeric($itspid)) {
        $invalid[] = 'itspid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall_itspcourses', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    $itsp = xarModApiFunc('itsp','user','get',array('itspid'=>$itspid));
    $planid = $itsp['planid'];
    $userid = $itsp['userid'];
    if (!xarSecurityCheck('ViewITSP', 1, 'ITSP', "$itspid:$planid:$userid")) {
       return;
    }
    $items = array();

    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table definitions you are
     * using - $table doesn't cut it in more complex modules
     */
    $icoursestable = $xartable['itsp_itsp_courses'];
    $query = "SELECT xar_icourseid,
                   xar_pitemid,
                   xar_icoursetitle,
                   xar_icourseloc,
                   xar_icoursedesc,
                   xar_icoursecredits,
                   xar_icourselevel,
                   xar_icourseresult,
                   xar_icoursedate,
                   xar_dateappr,
                   xar_datemodi,
                   xar_modiby
                  FROM $icoursestable
                  WHERE xar_itspid = $itspid";
    if (!empty($pitemid)) {
       $query .= " AND xar_pitemid = $pitemid ";
    }
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($icourseid, $pitemid, $icoursetitle, $icourseloc, $icoursedesc, $icoursecredits, $icourselevel, $icourseresult,
        $icoursedate, $dateappr, $datemodi,$modiby) = $result->fields;
        if (xarSecurityCheck('ReadITSP', 0, 'ITSP', "$itspid:$planid:$userid")) {
            $items[] = array('icourseid'      => $icourseid,
                             'pitemid'        => $pitemid,
                             'icoursetitle'   => $icoursetitle,
                             'icourseloc'     => $icourseloc,
                             'icoursedesc'    => $icoursedesc,
                             'icoursecredits' => $icoursecredits,
                             'icourselevel'   => $icourselevel,
                             'icourseresult'  => $icourseresult,
                             'icoursedate'    => $icoursedate,
                             'dateappr'       => $dateappr,
                             'datemodi'       => $datemodi,
                             'modiby'         => $modiby);
        }
    }
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the items */
    return $items;
}
?>