<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Departments
        $departments = ['Front Desk', 'Kitchen', 'Laundry', 'Housekeeping', 'Bar', 'Management'];
        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept]);
        }

        // 2. Permissions
        $permissions = [
            'view-dashboard',
            'manage-bookings',
            'view-bookings',
            'manage-customers',
            'view-customers',
            'manage-rooms',
            'view-rooms',
            'update-room-status',
            'manage-kitchen-orders',
            'view-kitchen-orders',
            'manage-laundry-orders',
            'view-laundry-orders',
            'generate-bills',
            'view-bills',
            'manage-payments',
            'view-reports',
            'view-department-reports',
            'manage-users',
            'manage-departments',
            'manage-roles',
            'view-audit-logs',
            'manage-bar-orders',
            'view-bar-orders',
            'manage-bar-items',
            'view-bar-items',
            'manage-incidents',
            'view-incidents',
            'manage-housekeeping-tasks',
            'view-housekeeping-tasks',
            'update-housekeeping-status',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 3. Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        $frontDeskRole = Role::firstOrCreate(['name' => 'front_desk', 'guard_name' => 'web']);
        $frontDeskRole->syncPermissions([
            'view-dashboard',
            'manage-bookings',
            'view-bookings',
            'manage-customers',
            'view-customers',
            'view-rooms',
            'update-room-status',
            'generate-bills',
            'view-bills',
            'manage-payments',
            'view-reports',
            'view-kitchen-orders',
            'view-laundry-orders',
            'view-bar-orders',
            'manage-bar-orders',
            'view-bar-items',
            'manage-incidents',
            'view-incidents',
            'manage-housekeeping-tasks',
            'view-housekeeping-tasks',
            'update-housekeeping-status',
        ]);

        $kitchenRole = Role::firstOrCreate(['name' => 'kitchen', 'guard_name' => 'web']);
        $kitchenRole->syncPermissions([
            'view-dashboard',
            'manage-kitchen-orders',
            'view-kitchen-orders',
            'view-department-reports',
            'view-customers',
        ]);

        $laundryRole = Role::firstOrCreate(['name' => 'laundry', 'guard_name' => 'web']);
        $laundryRole->syncPermissions([
            'view-dashboard',
            'manage-laundry-orders',
            'view-laundry-orders',
            'view-department-reports',
            'view-customers',
        ]);

        $housekeepingRole = Role::firstOrCreate(['name' => 'housekeeping', 'guard_name' => 'web']);
        $housekeepingRole->syncPermissions([
            'view-dashboard',
            'view-rooms',
            'update-room-status',
            'view-housekeeping-tasks',
            'update-housekeeping-status',
        ]);

        $managementRole = Role::firstOrCreate(['name' => 'management', 'guard_name' => 'web']);
        $managementRole->syncPermissions([
            'view-dashboard',
            'view-reports',
            'view-department-reports',
            'view-bookings',
            'view-bills',
            'view-customers',
            'view-rooms',
            'view-kitchen-orders',
            'view-laundry-orders',
            'view-audit-logs',
            'view-incidents',
            'view-housekeeping-tasks',
        ]);

        $barRole = Role::firstOrCreate(['name' => 'bar', 'guard_name' => 'web']);
        $barRole->syncPermissions([
            'view-dashboard',
            'manage-bar-orders',
            'view-bar-orders',
            'view-bar-items',
        ]);

        // 4. Admin user
        $mgmtDept = Department::where('name', 'Management')->first();
        $admin = User::firstOrCreate(
            ['email' => 'admin@goldplushotel.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('password'),
                'department_id' => $mgmtDept?->id,
                'is_active' => true,
                'phone' => '+256700000000',
            ]
        );
        $admin->syncRoles(['admin']);

        // 5. Room Types
        $roomTypes = [
            ['name' => 'Executive Deluxe', 'description' => 'Our premium suite with all-inclusive amenities', 'price_per_night' => 250.00, 'max_guests' => 4, 'amenities' => ['King Bed', 'Jacuzzi', 'Mini Bar', 'City View', 'AC', 'WiFi', 'Smart TV', 'Lounge']],
            ['name' => 'Deluxe', 'description' => 'Spacious and elegant room with premium comfort', 'price_per_night' => 150.00, 'max_guests' => 3, 'amenities' => ['Queen Bed', 'Bathtub', 'Mini Fridge', 'AC', 'WiFi', 'Smart TV']],
            ['name' => 'Standard', 'description' => 'Comfortable and affordable accommodation', 'price_per_night' => 80.00, 'max_guests' => 2, 'amenities' => ['Double Bed', 'Shower', 'AC', 'WiFi', 'TV']],
        ];
        foreach ($roomTypes as $rt) {
            RoomType::firstOrCreate(['name' => $rt['name']], $rt);
        }

        // 6. Rooms
        $execDeluxe = RoomType::where('name', 'Executive Deluxe')->first();
        $deluxe = RoomType::where('name', 'Deluxe')->first();
        $standard = RoomType::where('name', 'Standard')->first();

        $rooms = [
            // Floor 1 – Standard
            ['room_number' => '101', 'room_type_id' => $standard->id, 'floor' => 1],
            ['room_number' => '102', 'room_type_id' => $standard->id, 'floor' => 1],
            ['room_number' => '103', 'room_type_id' => $standard->id, 'floor' => 1],
            ['room_number' => '104', 'room_type_id' => $standard->id, 'floor' => 1],
            ['room_number' => '105', 'room_type_id' => $standard->id, 'floor' => 1],
            // Floor 2 – Deluxe
            ['room_number' => '201', 'room_type_id' => $deluxe->id, 'floor' => 2],
            ['room_number' => '202', 'room_type_id' => $deluxe->id, 'floor' => 2],
            ['room_number' => '203', 'room_type_id' => $deluxe->id, 'floor' => 2],
            ['room_number' => '204', 'room_type_id' => $deluxe->id, 'floor' => 2],
            ['room_number' => '205', 'room_type_id' => $deluxe->id, 'floor' => 2],
            // Floor 3 – Executive Deluxe
            ['room_number' => '301', 'room_type_id' => $execDeluxe->id, 'floor' => 3],
            ['room_number' => '302', 'room_type_id' => $execDeluxe->id, 'floor' => 3],
            ['room_number' => '303', 'room_type_id' => $execDeluxe->id, 'floor' => 3],
            ['room_number' => '304', 'room_type_id' => $execDeluxe->id, 'floor' => 3],
            ['room_number' => '305', 'room_type_id' => $execDeluxe->id, 'floor' => 3],
        ];
        foreach ($rooms as $room) {
            Room::firstOrCreate(['room_number' => $room['room_number']], $room);
        }
    }
}
