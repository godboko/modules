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
 * Delete an Newsletter issue
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the issue to be deleted
 * @param 'confirm' confirm that this issue can be deleted
 * @return array $data
 */
function newsletter_admin_deleteissue($args)
{
    // Extract args
    extract ($args);

    // Security check
    if(!xarSecurityCheck('DeleteNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('id', 'id', $id, 0)) return;
    if (!xarVarFetch('confirm', 'int:0:1', $confirm, 0)) return;

    // The admin API function is called
    $issue = xarModAPIFunc('newsletter',
                           'user',
                           'getissue',
                           array('id' => $id));

    // Check for exceptions
    if (!isset($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Check for confirmation.
    if (!$confirm) {

        // Get the admin menu
        // $data = xarModAPIFunc('newsletter', 'admin', 'menu');

        // Specify for which issue you want confirmation
        $data['id'] = $id;
        // $data['confirmbutton'] = xarML('Confirm');

        // Data to display in the template
        $data['namevalue'] = xarVarPrepForDisplay($issue['title']);

        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();

        // Return the template variables defined in this function
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for deleting #(1) issue #(2)',
                    'Newsletter', xarVarPrepForDisplay($id));
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // The API function is called
    if (!xarModAPIFunc('newsletter',
                     'admin',
                     'deleteissue',
                     array('id' => $id))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Item Deleted'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewissue'));
}

?>