<?php

namespace App\Asterisk;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AsteriskBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {

        $this->addRegisterMappingsPass($container);
    }
    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine-mapping') => 'App\Asterisk\Entity',
        );

        if (class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createAnnotationMappingDriver(['App\Asterisk\Entity'],[realpath(__DIR__.'/Entity')]));
        }
    }
}