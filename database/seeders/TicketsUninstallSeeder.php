<?php

namespace Bithoven\Tickets\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

/**
 * Tickets Uninstall Seeder
 * 
 * Cleans up all Tickets extension permissions and role assignments during uninstallation.
 * Implements Extension Permissions Protocol v2.0 cleanup strategy.
 * 
 * @version 1.0.0
 */
class TicketsUninstallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Cleaning up Tickets extension permissions...');

        try {
            // Step 1: Delete role assignments
            $deletedAssignments = DB::table('role_has_permissions')
                ->whereIn('permission_id', function($query) {
                    $query->select('id')
                        ->from('permissions')
                        ->where('name', 'like', 'extensions:tickets:%');
                })
                ->delete();

            $this->command->comment("   Deleted {$deletedAssignments} role assignments");

            // Step 2: Delete permissions
            $deletedPermissions = Permission::where('name', 'like', 'extensions:tickets:%')->delete();

            $this->command->comment("   Deleted {$deletedPermissions} permissions");

            // Step 3: Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            $this->command->newLine();
            $this->command->info("✅ Tickets extension permissions cleaned up successfully");
            $this->command->table(
                ['Item', 'Count'],
                [
                    ['Role Assignments Deleted', $deletedAssignments],
                    ['Permissions Deleted', $deletedPermissions],
                ]
            );

        } catch (\Exception $e) {
            $this->command->error('❌ Failed to cleanup Tickets extension permissions');
            $this->command->error($e->getMessage());
            
            // Don't throw - allow uninstall to continue even if cleanup fails
            logger()->error('Tickets uninstall seeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
