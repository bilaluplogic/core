<?php

/**
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function categories_userapi_getitemlinks($args)
{
    $itemlinks = array();
    $catlist = xarMod::apiFunc('categories','user','getcatinfo',
                             array('cids' => $args['itemids']));
    if (!isset($catlist) || !is_array($catlist) || count($catlist) == 0) {
       return $itemlinks;
    }

    foreach ($args['itemids'] as $itemid) {
        if (!isset($catlist[$itemid])) continue;
        $itemlinks[$itemid] = array('url'   => xarModURL('categories', 'user', 'main',
                                                         array('catid' => $itemid)),
                                            'title' => xarVarPrepForDisplay($catlist[$itemid]['name']),
                                            'label' => xarVarPrepForDisplay($catlist[$itemid]['description']));
    }
    return $itemlinks;
}

?>
