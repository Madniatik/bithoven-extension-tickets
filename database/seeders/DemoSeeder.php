<?php

namespace Bithoven\Tickets\Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds for demo data.
     * 
     * This seeder orchestrates all demo data seeders for the tickets extension.
     * 
     * Note: Can only be run once. To re-run, manually delete existing demo data first.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Tickets Demo Seeders...');
        
        // Check if demo data already exists
        $existingTickets = \Bithoven\Tickets\Models\Ticket::count();
        $existingTemplates = \Bithoven\Tickets\Models\TicketTemplate::count();
        $existingResponses = \Bithoven\Tickets\Models\CannedResponse::count();
        
        if ($existingTickets > 0 || $existingTemplates > 0 || $existingResponses > 0) {
            $this->command->warn('⚠️  Demo data already exists!');
            $this->command->info("   Tickets: {$existingTickets}");
            $this->command->info("   Templates: {$existingTemplates}");
            $this->command->info("   Canned Responses: {$existingResponses}");
            $this->command->newLine();
            $this->command->info('💡 Only new categories and automation rules will be updated.');
            $this->command->info('💡 To reload demo data, delete existing records first.');
            
            // Only run seeders that are idempotent
            $this->call([
                CategorySeeder::class,
                AutomationRulesSeeder::class,
            ]);
            
            return;
        }
        
        // Run all seeders (first time)
        $this->call([
            CategorySeeder::class,
            TemplatesResponsesSeeder::class,
            AutomationRulesSeeder::class,
            TicketsDemoSeeder::class,
        ]);
        
        $this->command->info('✅ All demo data loaded successfully!');
    }
}
