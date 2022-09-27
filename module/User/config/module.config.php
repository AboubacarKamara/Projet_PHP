<?php

declare(strict_types=1);

namespace User;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

$lang;

if(isset($_COOKIE["langue"]) && $_COOKIE["langue"] != ""){
    global $lang;
    $lang = $_COOKIE["langue"];
}
else{
    global $lang;
    setcookie("langue","fr");
    $lang = "fr";
}

return [
    'router' => [
    	'routes' => [
    		'signup' => [
    			'type' => Literal::class,
    			'options' => [
    				'route' => '/signup',
    				'defaults' => [
    					'controller' => Controller\AuthController::class,
    					'action' => 'create'
    				],
    			],
    		],
			'login' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/login',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action' => 'index'
                    ],
                ],
            ],
			'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/logout',
                    'defaults' => [
                        'controller' => Controller\LogoutController::class,
                        'action' => 'index'
                    ],
                ],
            ],
			'profile' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/profile',
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action' => 'index'
                    ],
                ],
        	],
            'add' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/add',
                    'defaults' => [
                        'controller' => Controller\AddController::class,
                        'action' => 'index'
                    ],
                ],
            ],
            'delete' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/delete[/:index]',
                    'defaults' => [
                        'controller' => Controller\AddController::class,
                        'action' => 'delete'
                    ],
                ],
            ],
            'edit' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/edit[/:index]',
                    'defaults' => [
                        'controller' => Controller\AddController::class,
                        'action' => 'edit'
                    ],
                ],
            ],
    	],
    ],

    'controllers' => [
    	'factories' => [
    		Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
			Controller\LoginController::class => Controller\Factory\LoginControllerFactory::class,
			Controller\LogoutController::class => InvokableFactory::class,
			Controller\ProfileController::class => Controller\Factory\ProfileControllerFactory::class,
            Controller\AddController::class => Controller\Factory\AddControllerFactory::class
        ],
    ],
    'view_manager' => [
    	'template_map' => [
    		'auth/create'   => __DIR__ . '/../view/user/auth/create.phtml',
			'login/index'   => __DIR__ . '/../view/user/auth/login.phtml',
			'profile/index' => __DIR__ . '/../view/user/auth/profile.phtml',
            'add/index'     => __DIR__ . '/../view/user/auth/addmedicine.phtml',
            'delete/delete' => __DIR__ . '/../view/user/auth/profile.phtml',
            'edit/edit' =>     __DIR__ . '/../view/user/auth/addmedicine.phtml'
    	],
    	'template_path_stack' => [
    		'user' => __DIR__ . '/../view',
    	]
    ],

    'translator' => [
        'locale' => "$lang",
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
];
?>