<?php

/**
 *
 *
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function dossier_locationsapi_getcontact($args)
{
    extract($args);

    $invalid = array();
    if (!isset($locationid) || !is_numeric($locationid) || empty($locationid)) {
        $invalid[] = 'locationid';
    }
    if (!isset($contactid) || !is_numeric($contactid) || empty($contactid)) {
        $invalid[] = 'contactid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'locations', 'getcontact', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('PublicDossierAccess', 0, 'Contact', "All:All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $locationstable = $xartable['dossier_locations'];
    $locationdatatable = $xartable['dossier_locationdata'];

    $query = "SELECT b.contactid,
                    b.locationid,
                    b.startdate,
                    b.enddate,
                    a.cat_id,
                    a.address_1,
                    a.address_2,
                    a.city,
                    a.us_state,
                    a.postalcode,
                    a.country,
                    a.latitude,
                    a.longitude
            FROM $locationstable a, $locationdatatable b
            WHERE a.locationid = b.locationid
            AND b.contactid = ?
            AND b.locationid = ?";

    $bindvars = array($contactid,
                    $locationid);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    if($result->EOF) return;
    
    list($contactid,
        $locationid,
        $startdate,
        $enddate,
        $cat_id,
        $address_1,
        $address_2,
        $city,
        $us_state,
        $postalcode,
        $country,
        $latitude,
        $longitude) = $result->fields;
        
    if (xarSecurityCheck('PublicDossierAccess', 0, 'Contact', "All:All:All:All")) {                    
        $item = array('contactid'     => $contactid,
                        'locationid'    => $locationid,
                        'startdate'     => $startdate,
                        'enddate'       => $enddate,
                        'cat_id'        => $cat_id,
                        'address_1'     => $address_1,
                        'address_2'     => $address_2,
                        'city'          => $city,
                        'us_state'      => $us_state,
                        'postalcode'    => $postalcode,
                        'country'       => $country,
                        'latitude'      => $latitude,
                        'longitude'     => $longitude);
    }

    $result->Close();

    return $item;
}

?>