<?php

return [

    [
        'label' => 'Dashboard',
        'route' => 'dashboard',
        'permission' => null, // sab login users
    ],

    [
        'label' => 'Users',
        'route' => 'admin.users.index',
        'permission' => 'user.view',
    ],

    [
        'label' => 'Roles',
        'route' => 'admin.roles.index',
        'permission' => 'role.view',
    ],

    [
        'label' => 'Permissions',
        'route' => 'admin.permissions.index',
        'permission' => 'permission.view',
    ],

];
