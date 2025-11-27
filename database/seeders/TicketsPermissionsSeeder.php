<?php

namespace Bithoven\Tickets\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Bithoven\Tickets\Database\Seeders\Data\TicketsPermissions;

/**
 * Tickets Permissions Seeder
 * 
 * Creates all Tickets extension permissions with alias and descriptions
 * following Extension Permissions Protocol v2.0
 * 
 * @version 1.0.0
 */
class TicketsPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Installing Tickets extension permissions...');

        $permissions = TicketsPermissions::all();
        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($permissions as $permissionData) {
            try {
                $permission = Permission::firstOrNew(['name' => $permissionData['name']]);
                
                if ($permission->exists) {
                    // Update existing permission
                    $permission->alias = $permissionData['alias'];
                    $permission->description = $permissionData['description'];
                    $permission->save();
                    $updatedCount++;
                    $this->command->comment("Updated: {$permissionData['name']}");
                } else {
                    // Create new permission
                    $permission->alias = $permissionData['alias'];
                    $permission->description = $permissionData['description'];
                    $permission->guard_name = 'web';
                    $permission->save();
                    $createdCount++;
                    $this->command->info("Created: {$permissionData['name']}");
                }
            } catch (\Exception $e) {
                $this->command->error("Failed to create/update permission: {$permissionData['name']}");
                $this->command->error($e->getMessage());
                $skippedCount++;
            }
        }

        // Assign permissions to roles based on extension purpose (helpdesk/support system)
        $this->command->newLine();
        $this->command->info('Assigning permissions to roles...');
        
        try {
            // Admin roles: Full access (all 8 permissions)
            $adminRoles = Role::whereIn('name', ['super-admin', 'master-developer', 'administrator'])->get();
            foreach ($adminRoles as $role) {
                $role->givePermissionTo(TicketsPermissions::names());
                $this->command->info("✅ {$role->name}: 8 permissions assigned");
            }
            
            // Support role: Agent permissions (4 permissions)
            $supportRole = Role::where('name', 'support')->first();
            if ($supportRole) {
                $supportRole->givePermissionTo([
                    'extensions:tickets:base:view',
                    'extensions:tickets:base:create',
                    'extensions:tickets:base:edit',
                    'extensions:tickets:base:assign',
                ]);
                $this->command->info('✅ support: 4 permissions assigned');
            } else {
                $this->command->warn('⚠️  support role not found');
            }
            
            // User role: Basic access (2 permissions)
            $userRole = Role::where('name', 'user')->first();
            if ($userRole) {
                $userRole->givePermissionTo([
                    'extensions:tickets:base:view',
                    'extensions:tickets:base:create',
                ]);
                $this->command->info('✅ user: 2 permissions assigned');
            } else {
                $this->command->warn('⚠️  user role not found');
            }
            
            // Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
        } catch (\Exception $e) {
            $this->command->error('Failed to assign permissions to roles');
            $this->command->error($e->getMessage());
        }

        $this->command->newLine();
        $this->command->info('Tickets Permissions Installation Summary:');
        $this->command->table(
            ['Status', 'Count'],
            [
                ['Created', $createdCount],
                ['Updated', $updatedCount],
                ['Skipped', $skippedCount],
                ['Total', count($permissions)],
            ]
        );
    }
}
