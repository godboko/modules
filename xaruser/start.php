<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

  function products_user_start()
  {
    include_once 'modules/xen/xarclasses/xenquery.php';
    $xartables = xarDBGetTables();

//      include( 'includes/application_top.php');
      // the following cPath references come from application_top.php

//    xarResponseRedirect(xarModURL('products','user','default'));
    include_once 'modules/products/xarincludes/modules/default.php';


    // Show the categories block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productscategories'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the admin info block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productsadmininfo'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the search block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productssearch'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the information block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productsinformation'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the language block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productslanguage'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the manufacturers block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productsmanufacturers'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the currencies block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productscurrencies'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show the shopping cart block
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productscart'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

    // Show  the exit menu
    $blockinfo = xarModAPIFunc('blocks', 'user', 'get', array('name'=> 'productsexit'));
    if(!xarModAPIFunc('blocks', 'admin', 'activate', array('bid' => $blockinfo['bid']))) return;

//    $data['account'] = xarModAPIFunc('products','user','getaccount', array('accountid' => $account));
    $data['account'] = 1;
    return $data;
}
?>