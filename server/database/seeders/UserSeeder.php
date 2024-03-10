<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $super_admin = User::factory()->create([
            'username' => 'admin_hebat',
            'password' => bcrypt('admin_hebat77776666'), 
            'email' => 'hebatcuy@gmail.com',
            'full_name' => 'Secret',
            'address' => 'Seberang',
            'is_actived' => 1, 
            'avatar_name' => 'default.jpg',
        ]);
        
        $admin = User::factory()->create([
            'username' => 'admin_hitam',
            'password' => bcrypt('admin123'), 
            'email' => 'admin@gmail.com',
            'full_name' => 'Secret',
            'address' => 'Under mahakam bridge',
            'is_actived' => 1, 
            'avatar_name' => 'default.jpg',
        ]);
        $client = User::factory()->create([
            'username' => 'clientCuy',
            'password' => bcrypt('client123'), 
            'email' => 'user@gmail.com',
            'full_name' => 'Oli',
            'address' => 'Near bridge',
            'bio' => 'I am a porography cuy',
            'is_actived' => 1, 
            'avatar_name' => 'default.jpg',
        ]);

        // {"user":{"id":3,"username":"clientCuy","email":"user@gmail.com","full_name":"Olivia","address":"Near bridge","is_actived":1,"bio":"I am a porography cuy","avatar_name":"WLuI1OxKlHUnNLweFwZr_65cf7ee8ab885.jpg","role":["client"]}}
        // 3|qniipunkUXMuN0q41eMfkeBPsWKKtmlPYhyajPEude85a21c


        $dataUsers = \App\Models\User::factory(50)->create();

        // Role
        $role_admin = Role::create(['name' => 'admin']); 
        $role_super_admin = Role::create(['name' => 'super admin']); 
        $role_client = Role::create(['name' => 'client']); 

        // Permission
        $basePermission = collect([
        'photo.all',
        'photo.create',
        'photo.detail',
        'photo.delete',

        'folder.all',
        'folder.create',
        'folder.detail',
        'folder.update',
        'folder.delete',

        'like.all',
        'like.create',
        'like.delete',

        'comment.all',
        'comment.create',
        'comment.delete',

        'save.all',
        'save.create',
        'save.delete',

        'report.create',

        'follow.all',
        'follow.create',
        'follow.delete',
        

        'user.detail',
        'user.update',

        ]);

        $adminPermission = collect([
            'user.all',
            'user.create',
            'user.delete',

            'report.all',
            'report.detail',
            'report.delete',
        ]);

        $basePermission->each(function($permission) use($role_client,$role_admin) {
            Permission::create(['name' => $permission]);
            
            $role_client->givePermissionTo($permission);
            $role_admin->givePermissionTo($permission);
        }); 
        $adminPermission->each(function($permission) use($role_admin) {
            Permission::create(['name' => $permission]);

            $role_admin->givePermissionTo($permission);
        }); 

        $admin->assignRole('admin');
        $client->assignRole('client');
        $super_admin->assignRole('super admin');

        foreach ($dataUsers as $user) {
            $user->assignRole('client');
        }
    }   
}
