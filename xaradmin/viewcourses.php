`<?php
/**
 * Admin function to view items
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Admin view of all courses
 *
 * This view shows all details of all courses, including hidden ones.
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param string catid ID of category or a string with catids glued, defaults to NULL
 * @param int startnum Number to start with in view
 * @param string sortby Default: name
 * @param string sortorder Defaults to ASC
 * @return array Data for template
 */
function courses_admin_viewcourses()
{
    if (!xarVarFetch('startnum', 'int:1:',         $startnum,  1,      XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid',    'isset',          $catid,     NULL,   XARVAR_DONT_SET))     return;
    if (!xarVarFetch('sortby',   'str:1:',         $sortby,    'name', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortorder','enum:DESC:ASC:', $sortorder, 'ASC', XARVAR_NOT_REQUIRED)) return;
    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Initialise the variable that will hold the items, so that the template
    // doesn't need to be adapted in case of errors
    $data['items'] = array();
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('courses', 'user', 'countitems', array('catid' => $catid)),
        xarModURL('courses', 'admin', 'viewcourses', array('startnum' => '%%',
                                                           'catid'    => $catid,
                                                           'sortby'   => $sortby,
                                                           'sortorder'=> $sortorder)),
        xarModGetVar('courses', 'itemsperpage'));

    // Security check
    if (!xarSecurityCheck('EditCourses')) return;

    // The user API function is called.
    $items = xarModAPIFunc('courses',
        'user',
        'getall',
        array('startnum' => $startnum,
              'numitems' => xarModGetVar('courses','itemsperpage'),
              'catid'    => $catid,
              'sortby'   => $sortby,
              'sortorder'=> $sortorder));
    // Check for exceptions
//    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $courseid = $item['courseid'];
        $name = $item['name'];
        $hidecourse = $item['hidecourse'];
        if (xarSecurityCheck('EditCourses', 0, 'Course', "$courseid:All:All")) {
            $items[$i]['planurl'] = xarModURL('courses',
                'admin',
                'plancourse',
                array('courseid' => $item['courseid']));
        } else {
            $items[$i]['planurl'] = '';
        }
        $items[$i]['plantitle'] = xarML('Plan');
        if (xarSecurityCheck('EditCourses', 0, 'Course', "$courseid:All:All")) {
            $items[$i]['editurl'] = xarModURL('courses',
                'admin',
                'modifycourse',
                array('courseid' => $item['courseid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');

        if (xarSecurityCheck('ReadCourses', 0, 'Course', "$courseid:All:All")) {
            $items[$i]['displayurl'] = xarModURL('courses',
                'user',
                'display',
                array('courseid' => $item['courseid']));
            $allplanned = xarModApiFunc('courses','user','getplandates',array('courseid'=> $courseid,'startafter'=>time()));
            if (!empty($allplanned)) {
                $items[$i]['next'] = $allplanned[0]['startdate'];
                if (!empty($allplanned[0]['expected'])) {
                    $items[$i]['expected'] = $allplanned[0]['expected'];
                }
            } else {
                $items[$i]['next'] = '';
            }
        } else {
            $items[$i]['displayurl'] = '';
        }

        if ((xarSecurityCheck('DeleteCourses', 0, 'Course', "$courseid:All:All")) && empty($allplanned)){
            $items[$i]['deleteurl'] = xarModURL('courses',
                'admin',
                'deletecourse',
                array('courseid' => $item['courseid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');
    }
    // Add the array of items to the template variables
    $data['items'] = $items;
    $data['catid'] = $catid;
    $data['sortorder'] = $sortorder;
    $data['sortby'] = $sortby;

    if (strcmp($sortorder, 'DESC')==0) {
        $sort ='ASC';
    } else {
        $sort = 'DESC';
    }

    // Create sort by URLs
    $data['snamelink'] = xarModURL('courses',
                                   'admin',
                                   'viewcourses',
                                   array('startnum' => 1,
                                         'sortby' => 'name',
                                         'sortorder' => $sort,
                                         'catid' => $catid));

    $data['sdesclink'] = xarModURL('courses',
                                   'admin',
                                   'viewcourses',
                                   array('startnum' => 1,
                                         'sortby' => 'shortdesc',
                                         'sortorder' => $sort,
                                         'catid' => $catid));

    $data['snumberlink'] = xarModURL('courses',
                                    'admin',
                                    'viewcourses',
                                    array('startnum' => 1,
                                          'sortby' => 'number',
                                          'sortorder' => $sort,
                                          'catid' => $catid));

    // Return the template variables defined in this function
    return $data;
}

?>