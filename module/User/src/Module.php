<?php

declare(strict_types=1);

namespace User;

class Module
{
    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig(): array
    {
        return[
            'factories' =>[
                UserModel::class => function($sm){
                    $dbAdapt = $sm->get(Adapter::class);
                    return new UserModel($dbAdapt);
                }
            ]
            ];
    }
}