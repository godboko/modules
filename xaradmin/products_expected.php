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

function commerce_admin_product_expected()
{


  new xenQuery("update " . TABLE_PRODUCTS . " set product_date_available = '' where to_days(now()) > to_days(product_date_available)");
  $product_query_raw = "select pd.product_id, pd.product_name, p.product_date_available from " . TABLE_product_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p where p.product_id = pd.product_id and p.product_date_available != '' and pd.language_id = '" . $_SESSION['languages_id'] . "' order by p.product_date_available DESC";
  $product_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $product_query_raw, $product_query_numrows);
  $product_query = new xenQuery($product_query_raw);
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($products = $q->output()) {
    if (((!$_GET['pID']) || ($_GET['pID'] == $products['product_id'])) && (!$pInfo) ) {
      $pInfo = new objectInfo($products);
    }

    if ( (is_object($pInfo)) && ($products['product_id'] == $pInfo->product_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'pID=' . $products['product_id'] . '&action=new_product') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_product_EXPECTED, 'page=' . $_GET['page'] . '&pID=' . $products['product_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $products['product_name']; ?></td>
                <td class="dataTableContent" align="center"><?php echo xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$products['product_date_available'])); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($pInfo)) && ($products['product_id'] == $pInfo->product_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_product_EXPECTED, 'page=' . $_GET['page'] . '&pID=' . $products['product_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif'), IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $product_split->display_count($product_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_product_EXPECTED); ?></td>
                    <td class="smallText" align="right"><?php echo $product_split->display_links($product_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  if (is_object($pInfo)) {
    $heading[] = array('text' => '<b>' . $pInfo->product_name . '</b>');

    $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'pID=' . $pInfo->product_id . '&action=new_product') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a>');
    $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_EXPECTED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$pInfo->product_date_available)));
  }

  if ( (xarModAPIFunc('commerce','user','not_null',array('arg' => $heading))) && (xarModAPIFunc('commerce','user','not_null',array('arg' => $contents))) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
}
?>