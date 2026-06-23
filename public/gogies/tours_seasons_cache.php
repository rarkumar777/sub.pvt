<?php
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                               ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                    ///
 * @link		    www.cms.gogies.net                             ///
 * @Version         1.0                                                   ///
 * @Created by      Ahmad Helalat                                         ///
 */                                                                       ///
//--------From the end of this line you can edit what ever you want ------///
if (!defined('gogies'))
{
    print 'Direct script access is not allowed';
    exit;
}

$GOGIES['tours_seasons_sql'] = $G['db']->q('SELECT * FROM ' . $GOGIES['dbprf'] . 'tours_seasons WHERE tour_id=0 ORDER BY from_date ASC');
$GOGIES['save_date'] = '<?php ';
while ($GOGIES['tours_seasons_res'] = mysqli_fetch_array($GOGIES['tours_seasons_sql']))
{
    $GOGIES['tours']['seasons'][$GOGIES['tours_seasons_res']['id']] = ['type' => $GOGIES['tours_seasons_res']['type'], 'from' => $GOGIES['tours_seasons_res']['from_date'], 'to' => $GOGIES['tours_seasons_res']['to_date']];
    $GOGIES['save_date'].=
            ' $GOGIES[\'tours\'][\'seasons\'][' . $GOGIES['tours_seasons_res']['id'] . ']=[\'type\'=>\'' . $GOGIES['tours_seasons_res']['type'] . '\' , \'from\' =>\'' . $GOGIES['tours_seasons_res']['from_date'] . '\', \'to\' => \'' . $GOGIES['tours_seasons_res']['to_date'] . '\'];';
}
$GOGIES['save_date'].=' ?>';
file_put_contents($GOGIES['path'] . '/config/modules/tours_seasons.php', $GOGIES['save_date']);
