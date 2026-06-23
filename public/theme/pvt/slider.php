<?php
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                               ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2014, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                    ///
 * @link		    www.cms.gogies.net                             ///
 * @Version         1.0                                                   ///
 * @Created by      Ahmad Helalat                                         ///
 */                                                                       ///
//--------From the end of this line you can edit what ever you want ------///

if (!defined('gogies'))
{
    exit;
}
if (mysqli_num_rows($GOGIES['slider_images_sql']) > 0)
{

    while ($GOGIES['slide_res'] = mysqli_fetch_array($GOGIES['slider_images_sql']))
    {
        $GOGIES['slides'].='<li><img class="animated fadeIn slider-img" src="' . $GOGIES['url'] . '/uploads/sliders/' . $GOGIES['slide_res']['image'] . '" alt="'.$GOGIES['slide_res']['text'].'"><div class="caption">';
        if (!empty($GOGIES['slide_res']['text']))
        {
            $GOGIES['slides'].='<h3 class="random-transition"><span>' . $GOGIES['slide_res']['text'] . '</span> </h3>'; 
        }

        if (!empty($GOGIES['slide_res']['text2']))
        {
            $GOGIES['slides'].='<h3 class="random-transition h-gap-t"><span>' . $GOGIES['slide_res']['text2'] . '</span> </h3>';
        }
        if (!empty($GOGIES['slide_res']['price']) && $GOGIES['slide_res']['price'] > 0.01)
        {
            $GOGIES['slides'].='<h3 class="random-transition gap-t"><span>' . $G['invoice']->convert_currency($GOGIES['slide_res']['price'], true) . '</span></h3>';
        }

        if (!empty($GOGIES['slide_res']['link']))
        {
            $GOGIES['slides'].='<h3 class="random-transition"><a href="' . $GOGIES['slide_res']['link'] . '" class="btn orange small text-capitalize"><i class="fa-plus"></i> ' . $lang['details'] . '</a></h3>';
        }
        $GOGIES['slides'].='</div></li>';
    }
}
?>

<div class="slider" data-autoplay="true" data-delay="6000" data-indicators="true" data-arrows="true" >

    <ul>
<?= $GOGIES['slides'] ?>
    </ul>
</div>