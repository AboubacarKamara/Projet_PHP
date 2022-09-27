<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\LoginController;
use User\Model\UserModel;
use Laminas\Db\Adapter\Adapter;

class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        return new LoginController(
            $container->get(Adapter::class),
            $container->get(UserModel::class)
        );
    }
}