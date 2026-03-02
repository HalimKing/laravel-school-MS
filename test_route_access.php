<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the super-admin user
$user = \App\Models\User::where('email', 'dumoralyci@mailinator.com')->first();
if (!$user) {
    echo 'User not found!';
    exit(1);
}

echo 'User: ' . $user->email . PHP_EOL;
echo 'User ID: ' . $user->id . PHP_EOL;
echo 'Roles: ' . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;
echo PHP_EOL;

// Test each permission in the route guard
$testPerms = [
    'user.read|role.read|permission.read|academic.read|attendance.read|fee.read|setting.read',
    'attendance.create'
];

foreach ($testPerms as $perm) {
    $result = $user->can($perm);
    echo 'Can access "' . $perm . '": ' . ($result ? 'YES' : 'NO') . PHP_EOL;
}

// Now let me check the actual middleware chain from the route
echo PHP_EOL . 'Testing admin route guard middleware...' . PHP_EOL;

// The admin routes use this middleware:
// middleware('can:user.read|role.read|permission.read|academic.read|attendance.read|fee.read|setting.read')

// So the user needs at LEAST ONE of these
$mainGuard = ['user.read', 'role.read', 'permission.read', 'academic.read', 'attendance.read', 'fee.read', 'setting.read'];
$hasMainGuard = false;
foreach ($mainGuard as $perm) {
    if ($user->can($perm)) {
        $hasMainGuard = true;
        echo 'Has ' . $perm . ': YES' . PHP_EOL;
        break;
    }
}

echo 'Passes admin route guard: ' . ($hasMainGuard ? 'YES' : 'NO') . PHP_EOL;
echo 'Passes attendance.create check: ' . ($user->can('attendance.create') ? 'YES' : 'NO') . PHP_EOL;
