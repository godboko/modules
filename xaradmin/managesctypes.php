<?php
/**
 * Sitecontact itemtype management
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * manage sitecontact item types
 */
function sitecontact_admin_managesctypes()
{
    // Get parameters
    if(!xarVarFetch('scid',          'int:1:',   $scid,           NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('sctypename',    'str:1:',   $sctypename,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('sctypedesc',    'str:1:',   $sctypedesc,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('customtext',    'str:1:',   $customtext,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('customtitle',   'str:1:',   $customtitle,    '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('optiontext',    'str:1:',   $optiontext,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('webconfirmtext','str:1:',   $webconfirmtext, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('notetouser',    'str:1:',   $notetouser,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('allowcopy',     'checkbox', $allowcopy,      true,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usehtmlemail',  'checkbox', $usehtmlemail,   false,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('scdefaultemail','str:1:',   $scdefaultemail, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('scdefaultname', 'str:1:',   $scdefaultname,  '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('action',       'isset',    $action,         NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('scactive',     'checkbox', $scactive,       true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('savedata',     'checkbox', $savedata, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('permissioncheck', 'checkbox', $permissioncheck, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('termslink',    'str:1:',   $termslink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowccs',      'checkbox', $allowccs, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowbccs',     'checkbox', $allowbccs, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('adminccs',     'checkbox', $adminccs, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('admincclist',  'str:0:', $admincclist, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowanoncopy', 'checkbox', $allowanoncopy, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useantibot',    'checkbox', $useantibot,    true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum',      'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl',      'str:0:', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('EditSiteContact')) return;
    xarCore::setCached('sitecontact.data','scid',$scid);
    // Initialise the template variables
    $data = array();
    $sctypes=array();

    // Get current item types
    $sctypes = xarModAPIFunc('sitecontact','user', 'getcontacttypes',
                            array('startnum' => $startnum,
                                  'numitems' => xarModVars::get('sitecontact','itemsperpage')));

    $defaultform= xarModVars::get('sitecontact','defaultform');
    $data['defaultform']=$defaultform;
    // Verify the action
    if (!isset($action) ){
        $action = 'view';
        xarSession::setVar('statusmsg','');
    }
    if (!isset($scid) && $action =='view') {
         xarSession::setVar('statusmsg','');
    }

    // Add a pager for forms
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('sitecontact','user','countitems'),
        xarModURL('sitecontact', 'admin', 'managesctypes', array('action'=>$action, 'startnum' => '%%')),
        xarModVars::get('sitecontact', 'itemsperpage'));


    $data['managetype']=xarML('List Forms');
    $formisactive = xarModVars::get('sitecontact', 'scactive') ? 'checked' : '';
    $allowanoncopy = ($allowcopy && $allowanoncopy)? true :false; //only allow anonymous if allow copy for registered too
    $soptions=array('allowccs'=>$allowccs,'allowbccs'=>$allowbccs,
                    'allowanoncopy' => $allowanoncopy,
                    'useantibot'=>$useantibot,
                    'adminccs'=>$adminccs,
                    'admincclist'=>$admincclist);
    $soptions=serialize($soptions);

    //Setup array with captured vars
    $item=array('scid' => (int)$scid,
                'sctypename'     => $sctypename,
                'sctypedesc'     => $sctypedesc,
                'customtext'     => $customtext,
                'customtitle'    => $customtitle,
                'optiontext'     => $optiontext,
                'webconfirmtext' => $webconfirmtext,
                'notetouser'     => $notetouser,
                'allowcopy'      => (int)$allowcopy,
                'usehtmlemail'   => (int)$usehtmlemail,
                'scdefaultemail' => $scdefaultemail,
                'scdefaultname'  => $scdefaultname,
                'scactive'       => (int)$scactive,
                'savedata'       => $savedata,
                'permissioncheck'=> $permissioncheck,
                'termslink'      => $termslink,
                'adminccs'       => $adminccs,
                'admincclist'    => $admincclist,
                'allowccs'       => $allowccs,
                'allowbccs'      => $allowbccs,
                'allowanoncopy'  => $allowanoncopy,
                'soptions'       => $soptions,
                'useantibot'     => $useantibot,
                'returnurl'      => $returnurl,
                'formisactive'   => $formisactive // add this in addition to normal field value
                );

    // Take action if necessary
    if ($action == 'create' || $action == 'update' || $action == 'confirm'){
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        if ($action == 'create') {

            $sctype = xarModAPIFunc('sitecontact','admin','createsctype',$item);

            if (isset($sctype) && $sctype['created']==1) {
               // Redirect to the admin view page
                xarSession::setVar('statusmsg',xarML('New Sitecontact Form created'));
                xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes',
                                              array('action' => 'view',
                                                    'scid' => $sctype['sctypeid'])));
                return true;
            } else {
                   xarSession::setVar('statusmsg',xarML('Problem with creation of New Sitecontact Form '));
                   xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes'));
            }

        } elseif ($action == 'update') {

             $updatedscid=xarModAPIFunc('sitecontact','admin','updatesctype', $item);

             if (!$updatedscid) {
                  xarSession::setVar('statusmsg',xarML('Problem updating site contact form #(1)',$item['sctypename']));
                $msg = xarML('Problem updating a Sitecontact Forum with ID of #(1)',$item['scid']);
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
                return false;
            } else {

                // Redirect to the admin view page
                xarSession::setVar('statusmsg',xarML('Contact form updated'));
                xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes',
                                              array('action' => 'view', 'scid'=>$scid)));
                return true;
            }

        } elseif ($action == 'confirm') { //go ahead and delete

           $item = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));

           $data['item']=$item[0];
           if ($scid == $defaultform) {
            $msg = xarML('You cannot delete the default form. Please change the default form first');
             xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
            return false;
           }
           if (!xarModAPIFunc('sitecontact','admin','deletesctype',array('scid'=> (int)$scid))) {
              $msg = xarML('Problem deleting a sitecontact Form with id #(1)',$scid);
             xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
            return false;
           } else {
                // Redirect to the admin view page
                xarSession::setVar('statusmsg',
                                xarML('Sitecontact Form deleted'));
                xarResponseRedirect(xarModURL('sitecontact', 'admin', 'managesctypes',
                                              array('action' => 'view')));
                return true;
          }
        }
    }


    // Create Edit/Delete links
    $totalforms=count($sctypes);

    if ($totalforms >0 ) {
      foreach ($sctypes as $id => $sctype) {
        if (xarSecurityCheck('EditSiteContact',0)) {
            $sctypes[$id]['editurl'] = xarModURL('sitecontact','admin','managesctypes',
                                             array('scid' => $sctype['scid'],
                                                   'action' => 'modify'));
        } else {
            $sctypes[$id]['editurl']='';
        }
        if (xarSecurityCheck('DeleteSiteContact',0)) {
            if (($totalforms >1)  && ($sctype['scid'] != $defaultform)){ //we can delete but not if only form left, or the default one
                $sctypes[$id]['deleteurl'] = xarModURL('sitecontact','admin','managesctypes',
                                               array('scid' => $sctype['scid'],
                                                     'action' => 'delete'));
            }

        } else {
               $sctypes[$sctype['scid']]['deleteurl'] = '';
        }

        if (xarSecurityCheck('EditSiteContact',0)) {
            $sctypes[$id]['previewurl'] = xarModURL('sitecontact','admin','managesctypes',
                                               array('scid' =>$sctype['scid'],
                                                     'action' => 'preview'));
        } else {
            $sctypes[$id]['previewurl'] = '';
        }
      }
    }

    $data['newurl'] = xarModURL('sitecontact','admin','managesctypes',
                               array('action' => 'new'));

    // Fill in relevant variables
    if ($action == 'new') {
        xarSession::setVar('statusmsg','');
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Create');
        $data['managetype']=xarML('Create Form');
        $data['link'] = xarModURL('sitecontact','admin','managesctypes',
                                 array('action' => 'create'));
        $soptions =xarModVars::get('sitecontact','soptions');
        $soptions=unserialize($soptions);
        if (is_array($soptions)) {
            foreach ($soptions as $k=>$v) {
                $k=$v;
            }
        }
        if (!isset($allowbccs)) $allowbccs=false;
        if (!isset($allowccs)) $allowccs=false;
        if (!isset($adminccs)) $adminccs=false;
        if (!isset($admincclist)) $admincclist='';
        if (!isset($allowanoncopy)) $allowanoncopy=false;
        if (!isset($useantibot)) $useantibot=false;
        $item = array('sctypename'     => xarML('Unique name for new form'),
                      'sctypedesc'     => xarML('Another contact form'),
                      'customtext'     => xarModVars::get('sitecontact','customtext'),
                      'customtitle'    => xarModVars::get('sitecontact','customtitle'),
                      'optiontext'     => xarModVars::get('sitecontact','optiontext'),
                      'webconfirmtext' => xarModVars::get('sitecontact','webconfirmtext'),
                      'notetouser'     => xarModVars::get('sitecontact','notetouser'),
                      'allowcopy'      => xarModVars::get('sitecontact','allowcopy'),
                      'usehtmlemail'   => xarModVars::get('sitecontact','usehtmlemail'),
                      'scdefaultemail' => xarModVars::get('sitecontact','scdefaultemail'),
                      'scdefaultname'  => xarModVars::get('sitecontact','scdefaultname'),
                      'permissioncheck'=> xarModVars::get('sitecontact','permissioncheck'),
                      'savedata'       => xarModVars::get('sitecontact','savedata'),
                      'termslink'      => xarModVars::get('sitecontact','termslink'),
                      'allowbccs'      => $allowbccs,
                      'allowccs'       => $allowccs,
                      'adminccs'       => $adminccs,
                      'admincclist'    => $admincclist,
                      'allowanoncopy'  => $allowanoncopy,
                      'useantibot'     => $useantibot,
                      'formisactive'   => (xarModVars::get('sitecontact', 'scactive') ? 'checked' : '')
                );
        $data['item']=$item;

    } elseif ($action == 'modify') {
         xarSession::setVar('statusmsg','');
        $item = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));
        $data['item']=$item[0];

        if (isset($data['item']['soptions'])) {
            $soptions=unserialize($data['item']['soptions']);
            if (is_array($soptions)) {
                foreach ($soptions as $k=>$v) {
                    $data['item'][$k]=$v;
                }
            }
        }

        if (!isset($data['item']['allowbccs']))$data['item']['allowbccs']=0;
        if (!isset($data['item']['allowccs']))$data['item']['allowccs']=0;
        if (!isset($data['item']['adminccs']))$data['item']['adminccs']=0;
        if (!isset($data['item']['admincclist']))$data['item']['admincclist']='';
        if (!isset($data['item']['allowanoncopy']))$data['item']['allowanoncopy']=0;
        if (!isset($data['item']['useantibot']))$data['item']['useantibot']=false;
        if (!isset($data['item']['savedata']))$data['item']['savedata']=xarModVars::get('sitecontact','savedata')?xarModVars::get('sitecontact','savedata'):0;
        if (!isset($data['item']['permissioncheck']))$data['item']['permissioncheck']=xarModVars::get('sitecontact','permissioncheck');
        if (!isset($data['item']['termslink']))$data['item']['termslink']=xarModVars::get('sitecontact','termslink');

        $data['managetype']=xarML('Edit Form Definition');
        $data['formisactive']=xarModVars::get('sitecontact', 'scactive') ? 'checked' : '';
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Modify');
        $data['link'] = xarModURL('sitecontact','admin','managesctypes',
                                 array('action' => 'update'));
        $data['returnurl'] = xarModURL('sitecontact','admin','managesctypes',array('action'=>'modify','scid'=>$scid));
        $hooks = xarModCallHooks('module', 'modifyconfig', 'sitecontact',
                             array('module'   => 'sitecontact',
                                   'itemtype' => $scid));
         if (empty($hooks)) {
            $data['hooks'] = array('dynamicdata' => xarML('You can add Dynamic Data fields here by hooking Dynamic Data to Sitecontact'));
        } else {
            $data['hooks'] = $hooks;
        }
    } elseif ($action == 'delete') {
        xarSession::setVar('statusmsg','');
        $forminfo = xarModAPIFunc('sitecontact','user','getcontacttypes', array('scid'=> $scid));
        $forminfo=$forminfo[0];
        $info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('name'=> $forminfo['sctypename']));
        $thisobject = xarModAPIFunc('dynamicdata','user','getobject', array('objectid' => $info['objectid']));

        $thisitem =$thisobject->properties;

        if (is_array($thisitem) ) {
          $data['item']=$forminfo;
          $data['objectid']=$info['objectid'];
        } else {
          //there is something wrong - the item doesn't exist
            $msg = xarML('There has been an error. Please contact the system administrator and inform them of this error message.');
             xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
            return false;
        }
        if ($scid == $defaultform) {
            $msg = xarML('You cannot delete the default form. Please change the default form first');
             xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
            return false;
        }
        $data['authid'] = xarSecGenAuthKey();
        $data['buttonlabel'] = xarML('Delete');
        $data['managetype']=xarML('Delete Form Definition');
        $data['numitems'] = xarModAPIFunc('sitecontact','user','countitems');
        $data['link'] = xarModURL('sitecontact','admin','managesctypes',
                                 array('action' => 'confirm'));

    } elseif ($action == 'preview') {
        xarSession::setVar('statusmsg','');

        $item = xarModAPIFunc('sitecontact','user','getcontacttypes', array('scid'=> $scid));
        $forminfo=$item[0];
        $info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('name'=> $forminfo['sctypename']));
        $thisobject = xarModAPIFunc('dynamicdata','user','getobject', array('objectid' => $info['objectid']));

        $thisitem =$thisobject->properties;
        $data['item']=$forminfo;

        $data['properties']=$thisitem;

        if (isset($data['item']['soptions'])) {
            $soptions=unserialize($data['item']['soptions']);
            if (is_array($soptions)) {
                foreach ($soptions as $k=>$v) {
                   $data['item'][$k]=$v;
               }
            }
        }

        if (!isset($data['item']['allowbccs']))$data['item']['allowbccs']=0;
        if (!isset($data['item']['allowccs']))$data['item']['allowccs']=0;
        if (!isset($data['item']['adminccs']))$data['item']['adminccs']=0;
        if (!isset($data['item']['admincclist']))$data['item']['admincclist']='';
        if (!isset($data['item']['allowanoncopy']))$data['item']['allowanoncopy']=0;
        if (!isset($data['item']['useantibot']))$data['item']['useantibot']=false;
        if (!isset($data['item']['savedata']))$data['item']['savedata']=xarModVars::get('sitecontact','savedata');
        if (!isset($data['item']['permissioncheck']))$data['item']['permissioncheck']=xarModVars::get('sitecontact','permissioncheck');
        if (!isset($data['item']['termslink']))$data['item']['termslink']=xarModVars::get('sitecontact','termslink');

        $optionset=explode(',',$item[0]['optiontext']);
        $data['optionset']=$optionset;
        $optionitems=array();
        foreach ($optionset as $optionitem) {
           $optionitems[]=explode(';',$optionitem);
        }

       $data['requesttext']='';
       $data['optionitems']=$optionitems;
       $data['link'] = xarModURL('sitecontact','admin','managesctypes');
       $scid=$data['item']['scid'];
       $data['managetype']=xarML('Preview Form');
       if (xarModIsHooked('dynamicdata','sitecontact',$scid) ) {
          /* get the Dynamic Object defined for this module (and itemtype, if relevant) */
          $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'sitecontact',
                                   'itemtype' => $scid));
          if (!isset($object)) return;  /* throw back */

         /* check the input values for this object and do ....what here? */
         $isvalid = $object->checkInput();

         /*we just want a copy of data - don't need to save it in a table (no request yet anyway!) */
         $dditems = $thisitem;

         foreach ($dditems as $itemid => $fields) {
            $items[$itemid] = array();
            foreach ($fields as $name => $value) {
                $items[$itemid][$name] = ($value);
            }

            $propdata=array();
            foreach ($items as $key => $value) {
                $propdata[$value['name']]['label']=$value['label'];
                $propdata[$value['name']]['value']=$value['value'];
            }
         }
      }
    }

    $data['action'] = $action;
    $data['sctypes']=$sctypes;


    $data['sctypelink'] = xarModURL('sitecontact','admin','managesctypes');
    // Return the template variables defined in this function
    return $data;
}

?>