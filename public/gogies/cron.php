<?php
if (!defined('gogies'))
{
    exit;
}
//////////////////////////////////// delete services old seasons /////////////

$G['db']->q('DELETE FROM '.$GOGIES['dbprf'].'tours_seasons  WHERE `to_date` <  \''.$GOGIES['current_date'].'\' ');