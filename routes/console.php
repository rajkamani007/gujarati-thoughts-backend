<?php

use App\Models\AdminUser;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:reset-password {password=password}', function (string $password) {
    $admin = AdminUser::where('email', 'admin@quotes.com')->first();

    if (!$admin) {
        AdminUser::create([
            'name' => 'Admin',
            'email' => 'admin@quotes.com',
            'password' => $password,
        ]);
        $this->info('Admin user created.');
    } else {
        $admin->password = $password;
        $admin->save();
        $this->info('Admin password reset.');
    }

    $this->line('Login: admin@quotes.com / ' . $password);
})->purpose('Reset the default admin login password');
