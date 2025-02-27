<?php

namespace HeimrichHannot\VersionsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use HeimrichHannot\VersionsBundle\HeimrichHannotVersionsBundle;
use HeimrichHannot\VersionsBundle\VersionsBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(VersionsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
            BundleConfig::create(HeimrichHannotVersionsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
