<?php

/**
 * find the correct point of insertion for a node in Celko�s model for
 * hierarchical SQL Trees.
 *
 *  -- INPUT --
 * 1st param $inorout Where the new category should be: IN or OUT
 * 2nd param $rightorleft Where the new category should be: RIGHT or LEFT
 * 3rd param $right The right value of the reference category
 * 4th param $left The left value of the reference category
 *
 *  -- OUTPUT --
 * @returns int
 * @return value of left for the new category on success, false on failure
 */
function categories_adminapi_find_point_of_insertion($args)
{

    extract($args);

    // Switch chosen over ifs for easiness of comprehession of the code
    $rightorleft = strtolower ($rightorleft);
    $inorout = strtolower ($inorout);

    switch($rightorleft) {
       case "right":
           $point_of_insertion = $right;

           switch($inorout) {
              case "out":
                 $point_of_insertion++;
              break;

              case "in":
              break;

              default:
                $msg = xarML('Valid values: IN or OUT');
                throw new BadParameterException(null, $msg);
           }

       break;
       case "left":
           $point_of_insertion = $left;
           switch($inorout) {
              case "out":
              break;

              case "in":
                 $point_of_insertion++;
              break;

              default:
                $msg = xarML('Valid values: IN or OUT');
                throw new BadParameterException(null, $msg);
           }
       break;
       default:
        $msg = xarML('Valid values: RIGHT or LEFT');
        throw new BadParameterException(null, $msg);
    }
    return $point_of_insertion;
}

?>
