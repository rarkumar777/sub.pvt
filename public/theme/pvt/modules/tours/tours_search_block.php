<?php
//-------------The following may not be changed in any way----------------///
/* Gogies CMS                                                             ///
 *                                                                        ///
 * An open source Contents management system                               ///
 * @package		    Gogies CMS                                            ///
 * @author		    Gogies Dev Team                                       ///
 * @copyright	    Copyright (c) 2012 - 2013, Gogies.net.                ///
 * @license		    www.cms.gogies.net/license/                           ///
 * @link		    www.cms.gogies.net                                    ///
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
    <div class="row" id="search-block">
        <form method="get" action="<?= $GOGIES['seo_url'] ?>/tours/">
            <div class="sd-12 h-pad"></div>
            <div class="row ">
                <nav class="navbar vertical">


                    <div class="nav-toggle"></div>
                    <span class="brand-small"><strong><i class="fa-search"></i> <?= $lang['search'] ?></strong></span>

                    <ul class="nav-menu ">

                        <div class="pad-t d-pad-b">
                            <div class="row">
                                <div class=" hide-sd md-2 d-pad-t align-center"><h3><i class="fa-search"></i> <?= $lang['search'] ?></h3></div>
                                <div class="md-2">
                                    <div class="small sd-12 h-pad"><?= $G['form']->addSelect(['type' => 'select', 'attr' => ['name' => 'country', 'class' => 'btn white pad '], 'options' => $GOGIES['tours_countries']]); ?></div>
                                </div>



                                <div class="md-2">
                                    <div class="small sd-12 h-pad"><?= $G['form']->addSelect(['type' => 'select', 'attr' => ['name' => 'category', 'class' => 'btn white pad'], 'options' => $GOGIES['tours']['categories']]); ?></div>
                                </div>



                                <div class="md-2 ">
                                    <div class="small sd-12 h-pad"><?= $G['form']->addSelect(['type' => 'select', 'attr' => ['name' => 'type', 'class' => 'btn white pad '], 'options' => $GOGIES['tours']['types']]); ?>
                                    </div>
                                </div>

                                <div class="md-1">
                                    <div class="small sd-12 h-pad">
                                        <?= $G['form']->addInput(['type' => 'text', 'attr' => ['name' => 'price_min', 'placeholder' => $lang['price'] . '-' . $lang['min'], 'class' => 'btn white pad ']]); ?>
                                    </div>
                                </div>

                                <div class="md-1 ">
                                    <div class="small sd-12 h-pad">
                                        <?= $G['form']->addInput(['type' => 'text', 'attr' => ['name' => 'price_max', 'placeholder' => $lang['price'] . '-' . $lang['max'], 'class' => 'btn white pad ']]); ?>
                                    </div>
                                </div>

                                <div class="md-1 ">
                                    <div class="small sd-12 h-pad"><?= $G['form']->addInput(['type' => 'text', 'attr' => ['placeholder' => $lang['days'], 'name' => 'days', 'class' => 'btn white pad']]); ?>
                                    </div>
                                </div>




                                <div class="md-1 align-left">
                                    <div class="sd-12 h-pad align-center"><button class="btn orange nopad full-width"> <?= $lang['go'] ?></button></div>
                                </div>
                            </div>
                        </div>
                    </ul></nav></div>
        </form></div>
</div>