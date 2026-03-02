<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get current user from first super-admin we find
$user = \App\Models\User::whereHas('roles', function ($q) {
    $q->where('name', 'super-admin');
})->first();

if ($user) {
    echo 'Found super-admin user: ' . $user->email . PHP_EOL;
    echo 'Roles: ' . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;
    echo 'All permissions count: ' . $user->getAllPermissions()->count() . PHP_EOL;
    $lacks = [];
    foreach (['user.read', 'attendance.create', 'academic.read', 'role.read', 'permission.read', 'fee.read', 'setting.read'] as $perm) {
        if (!$user->can($perm)) {
            $lacks[] = $perm;
        }
    }
    if ($lacks) {
        echo 'MISSING permissions: ' . implode(', ', $lacks) . PHP_EOL;
    } else {
        echo 'Has all required permissions: YES' . PHP_EOL;
    }
} else {
    echo 'No super-admin user found!' . PHP_EOL;
}
