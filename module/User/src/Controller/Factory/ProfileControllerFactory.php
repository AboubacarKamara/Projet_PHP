<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\ProfileController;
use User\Model\UserModel;
use Laminas\Db\Adapter\Adapter;

class ProfileControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        return new ProfileController(
            $container->get(UserModel::class)
        );
    }
}