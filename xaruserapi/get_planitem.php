<?php
/**
 * Get a specific ITSP
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Get a specific planitem of an plan
 *
 * A planitem is a part of a plan
 *
 * @author the ITSP module development team
 * @param  int pitemid Th id of the plan item to get
 * @return array Item array, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_get_planitem($args)
{
    extract($args);
    /* Argument check */
    if (!isset($pitemid) || !is_numeric($pitemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get_planitem', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you are
     * getting - $table and $column don't cut it in more complex modules
     */
    $planitemstable = $xartable['itsp_planitems'];
    /* Get item
     */
    $query = "SELECT
               xar_pitemname,
               xar_pitemdesc,
               xar_pitemrules,
               xar_credits,
               xar_mincredit,
               xar_dateopen,
               xar_dateclose,
               xar_datemodi,
               xar_modiby
              FROM $planitemstable
              WHERE xar_pitemid = ?";
    $result = &$dbconn->Execute($query,array($pitemid));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    /* Obtain the item information from the result set */
    list($pitemname,
           $pitemdesc,
           $pitemrules,
           $credits,
           $mincredit,
           $dateopen,
           $dateclose,
           $datemodi,
           $modiby) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    // Security check
    if (!xarSecurityCheck('ReadITSPPlan', 1, 'Plan', "All:$pitemid")) {
        return;
    }
    /* Create the item array */
    $item = array( 'pitemid'    => $pitemid,
                   'pitemname'  => $pitemname,
                   'pitemdesc'  => $pitemdesc,
                   'pitemrules' => $pitemrules,
                   'credits'    => $credits,
                   'mincredit'  => $mincredit,
                   'dateopen'   => $dateopen,
                   'dateclose'  => $dateclose,
                   'datemodi'   => $datemodi,
                   'modiby'     => $modiby);
    /* Return the item array */
    return $item;
}
?>