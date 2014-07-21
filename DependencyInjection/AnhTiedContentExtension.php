<?php

namespace Anh\TiedContentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AnhTiedContentExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $config += array(
            'sections' => array()
        );

        $sections = $container->getParameter('anh_content.sections');
        $sectionsDiff = array_diff($config['sections'], array_keys($sections));

        if (!empty($sectionsDiff)) {
            throw new \InvalidArgumentException(
                sprintf("Section(s) '%s' not defined in 'anh_content.sections'.", implode(', ', $sectionsDiff))
            );
        }

        $container->setParameter('anh_tied_content.sections', $config['sections']);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $prepend = array();

        foreach ($config['sections'] as $section) {
            $prepend['sections'][$section] = array(
                'routes' => array(
                    'tied_parent' => sprintf('anh_tied_content_%s_parent', $section),
                    'tied_child' => sprintf('anh_tied_content_%s_child', $section),
                )
            );
        }

        if (!empty($prepend)) {
            $container->prependExtensionConfig('anh_content', $prepend);
        }

        $container->prependExtensionConfig('assetic', array(
            'assets' => array(
                'anh_tied_content_navigation_css' => array(
                    'inputs' => array(
                        '@AnhTiedContentBundle/Resources/scss/navigation.scss'
                    ),
                ),
            ),
            'bundles' => array(
                'AnhTiedContentBundle'
            )
        ));

         $container->prependExtensionConfig('anh_doctrine_resource', array(
            'resources' => array(
                'anh_tied_content.tie' => array(
                    'model' => '%anh_tied_content.entity.tie.class%',
                    'driver' => 'orm',
                    'controller' => 'Anh\TiedContentBundle\Controller\TieController',
                    'rules' => array(
                        'isPublished' => array(
                            'child.isDraft' => false,
                            'child.publishedSince <= current_timestamp()',
                        ),
                    ),
                ),
            )
        ));
   }
}