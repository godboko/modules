<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * the main administration function
 * @param none
 * @return array
 */
function content_admin_tips() {

    if (!xarSecurityCheck('ReadContent')) return;
 
    return xarTplModule('content','admin','tips');
    
}

?>