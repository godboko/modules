<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * unpublish a story
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of the story
 * @return bool true on success , or false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_unpublishstory($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'story ID';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'unpublishstory', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getstory',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrStories'];

    // Update the item
    $query = "UPDATE $nwsltrTable
                 SET xar_datepublished = 0
               WHERE xar_id = ?";

    // Execute query
    $result =& $dbconn->Execute($query, array((int) $id));

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'update', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>