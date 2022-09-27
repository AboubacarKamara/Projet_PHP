<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\AuthController;
use User\Model\UserModel;
use Laminas\Db\Adapter\Adapter;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        return new AuthController(
            $container->get(UserModel::class)
        );
    }
}