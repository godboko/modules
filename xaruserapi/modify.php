<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Modify a comment
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_modify($args)
{

    extract($args);

    $msg = xarML('Missing or Invalid Parameters: ');
    $error = FALSE;

    if (!isset($title)) {
        $msg .= xarMLbykey('title ');
        $error = TRUE;
    }

    if (!isset($cid)) {
        $msg .= xarMLbykey('cid ');
        $error = TRUE;
    }

    if (!isset($text)) {
        $msg .= xarMLbykey('text ');
        $error = TRUE;
    }

    if (!isset($postanon)) {
        $msg .= xarMLbykey('postanon ');
        $error = TRUE;
    }

    if(isset($itemtype) && !xarVarValidate('int:0:', $itemtype)) {
            $msg .= xarMLbykey('itemtype');
            $error = TRUE;
    }

    if(isset($objectid) && !xarVarValidate('int:1:', $objectid)) {
            $msg .= xarMLbykey('objectid');
            $error = TRUE;
    }

    if(isset($date) && !xarVarValidate('int:1:', $date)) {
            $msg .= xarMLbykey('date');
            $error = TRUE;
    }

    if(isset($status) && !xarVarValidate('enum:1:2:3', $status)) {
            $msg .= xarMLbykey('status');
            $error = TRUE;
    }

    if(isset($useeditstamp) && !xarVarValidate('enum:0:1:2', $useeditstamp)) {
            $msg .= xarMLbykey('useeditstamp');
            $error = TRUE;
    }

    if ($error) {
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $hostname = preg_replace('/,.*/', '', $forwarded);
    } else {
        $hostname = xarServerGetVar('REMOTE_ADDR');
    }

    if(!isset($useeditstamp)) {
        $useeditstamp = xarModGetVar('comments','editstamp');
    }

    $adminid = xarModGetVar('roles','admin');

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Let's leave a link for the changelog module if it is hooked to track comments
    /* jojodee: good idea. I'll move it direct to comments template and then can add it to
                any others we like as well, like xarbb.
    if (xarModIsHooked('changelog', 'comments', 0)){
        $url = xarModUrl('changelog', 'admin', 'showlog', array('modid' => '14', 'itemid' => $cid));
        $text .= "\n<p>\n";
        $text .= '<a href="' . $url . '" title="' . xarML('See Changes') .'">';
        $text .= '</a>';
        $text .= "\n</p>\n"; //let's keep the begin and end tags together around the wrapped content
    }
    */

    if  (($useeditstamp ==1 ) ||
                     (($useeditstamp == 2 ) && (xarUserGetVar('uid')<>$adminid))) {
        $text .= "\n";
        $text .= xarTplModule('comments','user','modifiedby', array(
                              'isauthor' => (xarUserGetVar('uid') == $authorid),
                              'postanon'=>$postanon));
        $text .= "\n"; //let's keep the begin and end tags together around the wrapped content
    }

    $sql =  "UPDATE $xartable[comments]
                SET xar_title    = ?,
                    xar_text     = ?,
                    xar_anonpost = ?";
    $bpostanon = empty($postanon) ? 0 : 1;
    $bindvars = array($title, $text, $bpostanon);

    if(isset($itemtype)) {
        $sql .= ",\nxar_itemtype = ?";
        $bindvars[] = $itemtype;
    }

    if(isset($objectid)) {
        $sql .= ",\nxar_objectid = ?";
        $bindvars[] = $objectid;
    }

    if(isset($date)) {
        $sql .= ",\nxar_date = ?";
        $bindvars[] = $date;
    }

    if(isset($status)) {
        $sql .= ",\nxar_status = ?";
        $bindvars[] = $status;
    }

    $sql .= "\nWHERE xar_cid = ?";
    $bindvars[] = $cid;
    $result = &$dbconn->Execute($sql,$bindvars);

    if (!$result) {
        return;
    }
    // Call update hooks for categories etc.
    $args['module'] = 'comments';
    $args['itemtype'] = 0;
    $args['itemid'] = $cid;
    xarModCallHooks('item', 'update', $cid, $args);

    return true;
}

?>