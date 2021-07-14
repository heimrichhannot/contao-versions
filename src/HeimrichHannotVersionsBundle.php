<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\VersionsBundle;


use HeimrichHannot\VersionsBundle\DependencyInjection\HeimrichHannotVersionsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotVersionsBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new HeimrichHannotVersionsExtension();
    }

}