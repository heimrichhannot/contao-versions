<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\VersionsBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('huh_versions');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('persistent_tables')
                    ->scalarPrototype()->end()
                    ->info("Set table names that should be persist within tl_versions.")
                    ->example(['tl_content','tl_my_custom_entity'])
                ->end()
                ->integerNode('persistent_version_period')
                    ->info("Set the time period persistent table versions should be kept in version table. Set to 0 for forever.")
                    ->example(7776000)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}