<?php
/**
 * Authentication Block
 * @package math
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.headlines.xarblocks.rss');

class Headlines_RssBlockAdmin extends Headlines_RssBlock implements iBlock
{
/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);
        $vars = $data;

        // Migrate $row['rssurl'] to content if present
        if (!empty($vars['url'])) {
            $vars['rssurl'] = $vars['url'];
            unset($vars['url']);
        }

        // Get parameters from whatever input we need
        $vars['items'] = array();

        // The user API function is called
        $links = xarMod::apiFunc('headlines', 'user', 'getall');
        $vars['items'] = $links;

        // Defaults
        if (!isset($vars['rssurl'])) $vars['rssurl'] = $this->rssurl;

        // bug[ 5322 ] - check for hid
        if (is_numeric($vars['rssurl'])) {
            $headline = xarMod::apiFunc('headlines', 'user', 'get', array('hid' => $vars['rssurl']));
            if (!empty($headline)) { // found headline, use that url
                $vars['rssurl'] = $headline['url'];
            } else {
                $vars['rssurl'] = $this->rssurl;
            }
        }

        // If the current URL is not in the headlines list, then pass it in as 'custom'
        $vars['otherrssurl'] = $vars['rssurl'];
        if (is_array($links) && $vars['rssurl'] != $this->rssurl) {
            foreach($links as $link) {
                if ($link['url'] == $vars['rssurl']) {
                    // The URL was found in the list, so it is not custom
                    $vars['otherrssurl'] = '';
                }
            }
        }

        // Defaults
        if (!isset($vars['show_chantitle'])) $vars['show_chantitle'] = $this->show_chantitle;
        if (!isset($vars['show_chandesc'])) $vars['show_chandesc'] = $this->show_chandesc;
        if (!isset($vars['showdescriptions'])) $vars['showdescriptions'] = $this->showdescriptions;
        if (!isset($vars['maxitems'])) $vars['maxitems'] = $this->maxitems;
        if (!isset($vars['refresh'])) $vars['refresh'] = $this->refresh;
        // bug [4545]
        if (!isset($vars['truncate'])) $vars['truncate'] = $this->truncate;
        // FR: add alt title/description/link
        if (!isset($vars['alt_chantitle'])) $vars['alt_chantitle'] = $this->alt_chantitle;
        if (!isset($vars['alt_chandesc'])) $vars['alt_chandesc'] = $this->alt_chandesc;
        if (!isset($vars['alt_chanlink'])) $vars['alt_chanlink'] = $this->alt_chanlink;
        if (!isset($vars['linkhid'])) $vars['linkhid'] = $this->linkhid;
        // get the current parser
        $vars['parser'] = xarModVars::get('headlines', 'parser');
        // check for legacy magpie code, checkme: is this still necessary?
        if (xarModVars::get('headlines', 'magpie')) $vars['parser'] = 'magpie';
        // check module available if not default parser
        if ($vars['parser'] != 'default' && !xarMod::isAvailable($vars['parser'])) $vars['parser'] = 'default';
        if ($vars['parser'] == 'simplepie') {
            // optionally show images and cats if available (SimplePie only)
            if (!isset($vars['show_chanimage'])) $vars['show_chanimage'] = $this->show_chanimage;
            if (!isset($vars['show_itemimage'])) $vars['show_itemimage'] = $this->show_itemimage;
            if (!isset($vars['show_itemcats'])) $vars['show_itemcats'] = $this->show_itemcats;
        } else {
            // otherwise set false (defaults)
            $vars['show_chanimage'] = $this->show_chanimage;
            $vars['show_itemimage'] = $this->show_itemimage;
            $vars['show_itemcats'] = $this->show_itemcats;
        }
        if (!isset($vars['show_warning'])) $vars['show_warning'] = $this->show_warning;


        $vars['blockid'] = $data['bid'];

        // Just return the template variables.
        return $vars;
    }

    public function insert(Array $data=array())
    {
        $vars = array();

        if (!xarVarFetch('rssurl', 'str:1:', $vars['rssurl'], $this->rssurl, XARVAR_NOT_REQUIRED)) {return;}
        // The 'otherrssurl' can override the 'rssurl'
        if (!xarVarFetch('otherrssurl', 'str:1:', $otherrssurl, $this->rssurl, XARVAR_NOT_REQUIRED)) {return;}
        // FR: added check for correct url format, including local urls
        if (!empty($otherrssurl) && $otherrssurl != $this->rssurl) {
            if (strstr($otherrssurl,'://')) {
                if (preg_match("!^http://|https://|ftp://!", $otherrssurl)) {
                    $vars['rssurl'] = $otherrssurl;
                }
            } elseif (substr($otherrssurl,0,1) == '/') {
                $server = xarServerGetHost();
                $protocol = xarServerGetProtocol();
                $vars['rssurl'] = $protocol . '://' . $server . $otherrssurl;
            } else {
                $baseurl = xarServerGetBaseURL();
                $vars['rssurl'] = $baseurl . $otherrssurl;
            }
        }
        // bug[ 5322 ] replace url value with numeric hid
        // allowing changes to module feeds to be reflected in blocks
        if (is_numeric($vars['rssurl'])) {
            $headline = xarMod::apiFunc('headlines', 'user', 'get', array('hid' => $vars['rssurl']));
            if (empty($headline)) {
                $vars['rssurl'] = $this->rssurl;
            }
        }
        // TODO: check for duplicates
        // TODO: check otherrssurl against stored headlines

        if (!xarVarFetch('maxitems', 'int:0', $vars['maxitems'], $this->maxitems, XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('showdescriptions', 'checkbox', $vars['showdescriptions'], $this->showdescriptions, XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('show_chantitle', 'checkbox', $vars['show_chantitle'], $this->show_chantitle, XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('show_chandesc', 'checkbox', $vars['show_chandesc'], $this->show_chandesc, XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('refresh', 'int:0', $vars['refresh'], $this->refresh, XARVAR_NOT_REQUIRED)) {return;}
        // bug [4545]
        if (!xarVarFetch('truncate', 'int:0', $vars['truncate'], $this->truncate, XARVAR_NOT_REQUIRED)) return;
        // FR: add alt title/description/link
        if (!xarVarFetch('alt_chantitle', 'str:1:', $vars['alt_chantitle'], $this->alt_chantitle, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('alt_chandesc', 'str:1:', $vars['alt_chandesc'], $this->alt_chandesc, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('alt_chanlink', 'str:1:', $vars['alt_chanlink'], $this->alt_chanlink, XARVAR_NOT_REQUIRED)) return;
        if (!preg_match("!^http://|https://|ftp://!", $vars['alt_chanlink'])) $vars['alt_chanlink'] = $this->alt_chanlink;
        if (!xarVarFetch('linkhid', 'checkbox', $vars['linkhid'], $this->linkhid, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('show_chanimage', 'checkbox', $vars['show_chanimage'], $this->show_chanimage, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('show_itemimage', 'checkbox', $vars['show_itemimage'], $this->show_itemimage, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('show_itemcats', 'checkbox', $vars['show_itemcats'], $this->show_itemcats, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('show_warning', 'checkbox', $vars['show_warning'], false, XARVAR_NOT_REQUIRED)) return;

        $data['content'] = $vars;
        return $data;
    }
}
?>