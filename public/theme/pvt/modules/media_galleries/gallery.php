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
if (!defined('gogies')){print 'Direct script access is not allowed'; exit;}?>
<div class="wrap white-bg">
 <h1 class="section-title gap-t"><i class="fa fa-camera-retro"></i> <?=$gal['name']?></h1>
 <div class="full-width h-pad"></div>
<div class="box charcoal table center-block h-pad">

<div class="fotorama full-width"
     data-nav="thumbs" data-allowfullscreen="true" data-fit="scaledown">
<?php while ($imgs=mysqli_fetch_array($imagessql)){?>

  <a href="<?=$GOGIES['url']?>/uploads/galleries/<?=$imgs['image']?>" data-caption="<?=$imgs['text']?>"><img src="<?=$GOGIES['url']?>/uploads/galleries/small/<?=$imgs['image']?>" ></a>


<?php }?>

</div>

</div>
<div class="clearfix d-pad"></div>
</div>