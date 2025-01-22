<?php

return [

    //List of User Permissions used by the site
    'permission_names' => [
        'admin' => 'Super admin',
        'view_admin'  => 'View admin area',
        'edit_conference' => 'Edit conference info',
        'edit_schedules' => 'Edit conference schedule',
        'edit_sessions' => 'Edit session info',
        'edit_users' => 'Edit users',
    ],

    'session_type_id' => [
        'panel' => 1,
        'presentation' => 2,
        'keynote' => 3,
        'workshop' => 4,
        'masterclass' => 5,
        'chat' => 6,
        'pitch' => 7,

    ]
];
