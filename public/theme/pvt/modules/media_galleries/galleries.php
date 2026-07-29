<?php
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                              ///
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
?>
<div class="wrap">
    <h1 class="section-title gap-t"><i class="fa-camera"></i> <?= $media_galleries_lang['media_galleries'] ?></h1>
    <div class="row">
<?php while ($gals = mysqli_fetch_array($galleries_sql))
{ ?>
            <div class="box table pull-left nopad">


                <div class=" pull-left text-center" style="height:220px; margin:8px; width:310px; overflow:hidden" >

                    <div class="zoom"><a href="<?= $GOGIES['seo_url'] ?>media_galleries/<?= $gals['name'] ?>/">
                            <img src="<?= $GOGIES['url'] . '/uploads/galleries/' . $gals['image'] ?>"/></a>
                    </div>
                </div>
                <div class="align-center h-pad-t h-pad-b"> <?= $gals['name'] ?></div>
            </div>
<?php } ?>
    </div>
    <div class="clearfix d-pad"></div>
</div>