<?php
/**
 * Delete a course
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Delete a course
 *
 * @author MichelV <michelv@xaraya.com>
 * @param  $args ['courseid'] ID of the course
 * @return bool true on success of deletion
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_deletecourse($args)
{
    extract($args);
    if (!xarVarFetch('courseid', 'id', $courseid)) return;

    if (!isset($courseid) || !is_int($courseid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'course ID', 'admin', 'deletecourse', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('courses',
        'user',
        'get',
        array('courseid' => $courseid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('DeleteCourses', 1, 'Course', "$courseid:All:All")) {
        return;
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];
    // Delete the item
    $query = "DELETE FROM $coursestable
            WHERE xar_courseid = ?";
    $result = &$dbconn->Execute($query, array($courseid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have deleted an item.  As this is a
    // delete hook we're not passing any extra info
    // xarModCallHooks('item', 'delete', $exid, '');
    $item['module'] = 'courses';
    $item['itemtype']=$item['coursetype'];
    $item['itemid'] = $courseid;
    xarModCallHooks('item', 'delete', $courseid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>