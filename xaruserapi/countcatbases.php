<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * count number of category bases
 *
 * @param $args['module'] the name of the module (will be optional)
 * @param $args['modid'] the ID of the module (will be optional)
 * @param $args['itemtype'] the ID of the itemtype (optional)
 * @returns int
 * @return number of categories
 */
function categories_userapi_countcatbases($args)
{
    // Expand arguments from argument array
    extract($args);

    // Security check
    // TODO: do we need this?
    if (!xarSecurityCheck('ViewCategories')) {return;}

    // Only modid supplied
    if (empty($module) && !empty($modid) && is_numeric($modid)) {
        $modinfo = xarModGetInfo($modid);
        $module = $modinfo['name'];
    }

    // Initialise the return value.
    $count = 0;

    // There will be a table for this, which will be a lot more flexible.
    // For now, the values are stored in module variables.

    // itemtype is not set or is zero
    if (empty($itemtype)) {
        $numcats = (int) xarModGetVar($module, 'number_of_categories');
        if (!empty($numcats) && is_numeric($numcats)) {
            $count += $numcats;
        }
    }

    // If itemtype is set then grab just that item type
    if (isset($itemtype) && is_numeric($itemtype)) {
        $numcats = (int) xarModGetVar($module, 'number_of_categories.' . $itemtype);
        if (!empty($numcats) && is_numeric($numcats)) {
            $count += $numcats;
        }
    }

    // If itemtype is not set, then loop for all itemtypes in the module.
    if (!isset($itemtype)) {
        // Get list of item types.
        // Don't throw an exception if this function doesn't exist.
        $mytypes = xarModAPIFunc(
            $module, 'user', 'getitemtypes',
            array(), 0
        );

        if (!empty($mytypes) && is_array($mytypes)) {
            foreach(array_keys($mytypes) as $itemtype) {
                $numcats = (int) xarModGetVar($module, 'number_of_categories.' . $itemtype);
                if (!empty($numcats) && is_numeric($numcats)) {
                    $count += $numcats;
                }
            }
        }
    }

    return $count;
}

?>