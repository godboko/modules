<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * Get publication information or redirect to create a new publication
 *
 * @public
 * @author Richard Cave
 * @param int 'publicationId' the id of the publication
 * @return bool true on success, false on failure
 */
function newsletter_admin_retrievepublication()
{
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('publicationId', 'int:1:', $publicationId, 0)) {return;}

    // Set stage information
    $templateVarArray = array();

    // Check if user wants to create new publication
    if ($publicationId == 0) {
        $nextstage = 'newpublication';
    } else {
        $nextstage = 'newissue';
    }

    // Set publicationId
    $templateVarArray['publicationId'] = $publicationId;

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', $nextstage, $templateVarArray));
}

?>