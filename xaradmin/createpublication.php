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
 * Create a new Newsletter publication
 *
 * @public
 * @author Richard Cave
 * @param 'ownerId' the id of the publication owner (uid in roles)
 * @param 'categoryId' the category id of the publiction
 * @param 'altcids' array of alternate category ids for the publication
 * @param 'title' the title of the publication
 * @param 'templateHTML' the HTML template for the publication
 * @param 'templateText' the text template for the publication
 * @param 'logo' the logo of the publication
 * @param 'linkExpiration' default number of days before a story link expires
 * @param 'linkRegistration' default text for link registration
 * @param 'description' description of the publication (used on subscription page)
 * @param 'disclaimerId' disclaimer id for the publication
 * @param 'introduction' introduction of the publication
 * @param 'private' publication is open for subscription or private
 * @param 'subject' email subject (title) for an issue
 * @param 'fromname' publication email from name (default = owner name)
 * @param 'fromemail' publication email from address (default = owner email)
 * @return bool true on success, false on failure
 */
function newsletter_admin_createpublication()
{
    // Confirm authorization key
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new #(1) item', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('ownerId', 'id', $ownerId)) {
        xarErrorFree();
        $msg = xarML('You must select an owner name.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('categoryId', 'id', $categoryId, 0)) return;

    if (!xarVarFetch('title', 'str:1:', $title)) {
        xarErrorFree();
        $msg = xarML('You must enter a publication title');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $defaultValue = xarModGetVar('newsletter', 'templateHTML');
    if (!xarVarFetch('templateHTML', 'str:1:', $templateHTML, $defaultValue)) return;

    $defaultValue = xarModGetVar('newsletter', 'templateText');
    if (!xarVarFetch('templateText', 'str:1:', $templateText, $defaultValue)) return;

    $defaultValue = xarModGetVar('newsletter', 'linkexpiration');
    if (!xarVarFetch('linkExpiration', 'int:0:', $linkExpiration, $defaultValue)) return;

    $defaultValue = xarModGetVar('newsletter', 'linkregistration');
    if (!xarVarFetch('linkRegistration', 'str:1:', $linkRegistration, $defaultValue)) return;

    if (!xarVarFetch('logo', 'str:1:', $logo, '')) return;
    if (!xarVarFetch('description', 'str:1:', $description, '')) return;
    if (!xarVarFetch('disclaimerId', 'id', $disclaimerId, 0)) return;
    if (!xarVarFetch('newdisclaimer', 'str:1:', $newdisclaimer, '')) return;
    if (!xarVarFetch('introduction', 'str:1:', $introduction, '')) return;
    if (!xarVarFetch('altcids', 'array:1:', $altcids, array())) return;
    if (!xarVarFetch('private', 'int:0:1:', $private, 0)) return;
    if (!xarVarFetch('subject', 'id', $subject, 0)) return;
    if (!xarVarFetch('fromname', 'str:1:', $fromname, '')) return;
    if (!xarVarFetch('fromemail', 'str:1:', $fromemail, '')) return;

    // If the fromname or fromemail fields are empty, then retrieve the information
    // from the publication owner
    if (empty($fromname) || empty($fromemail)) {
        // Get owner information
        $role = xarModAPIFunc('roles',
                              'user',
                              'get',
                               array('uid' => $ownerId));
        // Check return value
        if (!isset($role) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
            return; // throw back

        // Set name and/or email
        if (empty($fromname)) {
            $fromname = $role['name'];
        }
        if (empty($fromemail)) {
            $fromemail = $role['email'];
        }
    }

    // Add new disclaimer if field isn't empty
    if (!empty($newdisclaimer)) {
        // Add disclaimer
        $disclaimerId = xarModAPIFunc('newsletter',
                                      'admin',
                                      'createdisclaimer',
                                      array('title' => $title,
                                            'disclaimer' => $newdisclaimer));

        // Check return value
        if (!isset($disclaimerId) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
            return; // throw back
    }

    // Call create owner function API
    $pubId = xarModAPIFunc('newsletter',
                           'admin',
                           'createpublication',
                           array('ownerId' => $ownerId,
                                 'categoryId' => $categoryId,
                                 'altcids' => $altcids,
                                 'title' => $title,
                                 'introduction' => $introduction,
                                 'templateHTML' => $templateHTML,
                                 'templateText' => $templateText,
                                 'logo' => $logo,
                                 'linkExpiration' => $linkExpiration,
                                 'linkRegistration' => $linkRegistration,
                                 'disclaimerId' => $disclaimerId,
                                 'description' => $description,
                                 'private' => $private,
                                 'subject' => $subject,
                                 'fromname' => $fromname,
                                 'fromemail' => $fromemail));

    // Check return value
    if (!isset($pubId) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Set publication id
    $data['publicationId'] = $pubId;

    // Success
    xarSessionSetVar('statusmsg', xarML('Issue Created'));

    // Redirect to new issue
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'newissue', $data));

    // Return
    return true;
}

?>