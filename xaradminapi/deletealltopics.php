<?php
/**
 * Delete forum topics and replies
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
 * delete a forum
 * @param $args['fid'] ID of the forum
 * @returns bool
 * @return true on success, false on failure
 */
function xarbb_adminapi_deletealltopics($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($fid)) {
        $msg = xarML('Invalid Parameter Count', '', 'admin', 'delete', 'xarbb');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called.
    $data = xarModAPIFunc('xarbb',
                          'user',
                          'getforum',
                          array('fid' => $fid));

    if (empty($data)) return;

    // Security Check
    if(!xarSecurityCheck('ModxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;

    $topics =  xarModAPIFunc('xarbb','user','getalltopics',array('fid' => $fid));

    if ((!$topics) || (count($topics) ==0)) {
         return;
    }
    //Delete all the reply posts (comments table)
    foreach($topics as $topic)       {
        if(!xarModAPIFunc('xarbb','admin','deleteallreplies',array(
                    'tid' => $topic['tid']
                ))) return;
    }
 
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    // Delete the topic items themselves
    $query = "DELETE FROM $xbbtopicstable
              WHERE xar_fid = ?";
    $result =& $dbconn->Execute($query, array($fid));
    if (!$result) return;

    // Let any hooks know that we have deleted topics
    foreach($topics as $topic)    {
        $args['module'] = 'xarbb';
        $args['itemtype'] = $fid; // topic
        $args['itemid'] = $topic['tid'];
        xarModCallHooks('item', 'delete', $topic['tid'], $args);
    } 

    // Let the calling process know that we have finished successfully
    return true;
}
?>