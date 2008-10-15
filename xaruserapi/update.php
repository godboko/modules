<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_update( $args )
{
    extract($args);

    if (!is_numeric($id)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'message ID', 'userapi', 'update', 'messages');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($subject)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'subject', 'userapi', 'update', 'messages');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($body)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'body', 'userapi', 'update', 'messages');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($recipient)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'recipient', 'userapi', 'update', 'messages');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($draft) || $draft != true) {
        $draft = false;
    }

    // check the authorisation key
    if (!xarSecConfirmAuthKey()) return; // throw back

    $messages = xarModAPIFunc('messages','user','get',array('id' => $id, 'status' => 1));

    if (!count($messages) || !is_array($messages)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'Message ID', 'userapi', 'update', 'messages');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $id = xarModAPIFunc('messages',
                         'user',
                         'modify',
                          array('id'          => $id,
                                'title'       => $subject,
                                'date'        => time(),
                                'text'        => $body,
                                'author_id'    => xarUserGetVar('id'),
                                'status'      => ($draft ? 1 : 2),
                                'postanon'    => $postanon,
                                'useeditstamp'=> 0));


    return $id;
}

?>