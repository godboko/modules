<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/
/**
 * Add a new Newsletter issue
 *
 * @public
 * @author Richard Cave
 * @param string 'display' display 'published' or 'unpublished' stories
 * @param int 'publicationId' the publication id for the issue (0 = no publication)
 * @return array $data
 */
function newsletter_admin_newissue()
{
    // Security check
    if(!xarSecurityCheck('AddNewsletter')) return;

    // Get input parameters
    if (!xarVarFetch('display', 'str:1:', $display, 'unpublished')) return;
    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;

    // Get the admin menu
    // $data = xarModAPIFunc('newsletter', 'admin', 'menu');
    $data = array();
    // Get the list of publications
    $data['publications'] = xarModAPIFunc('newsletter',
                                          'user',
                                          'get',
                                           array('phase' => 'publication',
                                                 'sortby' => 'title'));

    // Check for exceptions
    if (!isset($data['publications']) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Get publication if publicationId present
    if ($publicationId) {
        // Get this publication
        $publication = xarModAPIFunc('newsletter',
                                     'user',
                                     'getpublication',
                                     array('id' => $publicationId));

        // Check for exceptions
        if (!isset($publication) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
            return; // throw back

        // Set publication name
        $publication_title = $publication['title'];

        // Get issue from name and email and publication
        $data['fromname'] = $publication['fromname'];
        $data['fromemail'] = $publication['fromemail'];
    } else {
        // Set publication name to empty string
        $publication_title = '';
        $data['fromname'] = '';
        $data['fromemail'] = '';
    }

    // Get current user
    $data['loggeduser'] = xarModAPIFunc('newsletter',
                                        'user',
                                        'getloggeduser');

    // Get the list of owners
    $data['owners'] = xarModAPIFunc('newsletter',
                                    'user',
                                    'get',
                                     array('phase' => 'owner'));

    if (empty($data['owners'])) {
        $msg = xarML('You must create an owner name before publishing.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Set publication
    $data['publicationId'] = $publicationId;
    $data['publication_title'] = $publication_title;
    $data['issue_title'] = $publication_title;
    $data['display'] = $display;

    // Set external checkbox to true
    $data['externalvalue'] = 1;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Return the template variables defined in this function
    return $data;
}

?>