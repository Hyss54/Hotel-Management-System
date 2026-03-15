<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleTestUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $users = [
            [
                'name' => 'Front Desk Test',
                'email' => 'frontdesk@test.com',
                'role' => 'front_desk',
                'dept' => 'Front Desk'
            ],
            [
                'name' => 'Kitchen Test',
                'email' => 'kitchen@test.com',
                'role' => 'kitchen',
                'dept' => 'Kitchen'
            ],
            [
                'name' => 'Laundry Test',
                'email' => 'laundry@test.com',
                'role' => 'laundry',
                'dept' => 'Laundry'
            ],
            [
                'name' => 'Housekeeping Test',
                'email' => 'housekeeping@test.com',
                'role' => 'housekeeping',
                'dept' => 'Housekeeping'
            ],
            [
                'name' => 'Management Test',
                'email' => 'management@test.com',
                'role' => 'management',
                'dept' => 'Management'
            ],
            [
                'name' => 'Bar Test',
                'email' => 'bar@test.com',
                'role' => 'bar',
                'dept' => 'Bar'
            ],
        ];

        foreach ($users as $userData) {
            $dept = Department::where('name', $userData['dept'])->first();
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $password,
                    'department_id' => $dept?->id,
                    'is_active' => true,
                    'phone' => '0000000000',
                ]
            );
            $user->syncRoles([$userData['role']]);
        }
    }
}
