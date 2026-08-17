<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LeaveType;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Admin user
        User::create([
            'employee_id'=>'ADM001',
            'first_name'=>'Admin',
            'last_name'=>'User',
            'email'=>'admin@example.com',
            'password'=>Hash::make('password'),
            'role'=>'admin',
            'employee_level'=>'management'
        ]);

        // Default leave types
        LeaveType::create(['name'=>'Annual Leave','is_paid'=>1,'days_allowed_management'=>30,'days_allowed_staff'=>20,'is_active'=>1]);
        LeaveType::create(['name'=>'Sick Leave','is_paid'=>1,'days_allowed_management'=>15,'days_allowed_staff'=>10,'is_active'=>1]);
    }
}
