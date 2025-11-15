<?php

namespace Bithoven\Tickets\Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds for demo data.
     *
     * This seeder creates ONLY demo tickets with comments.
     * Essential data (categories, templates, responses, rules) 
     * are seeded during installation via DatabaseSeeder.
     *
     * Can be run multiple times safely - adds demo tickets to existing data.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Tickets Demo Data...');

        // Check existing tickets count
        $existingTickets = \Bithoven\Tickets\Models\Ticket::count();

        if ($existingTickets > 0) {
            $this->command->info("ℹ️  Found {$existingTickets} existing tickets");
            $this->command->info('   Adding demo tickets to existing data...');
        }

        // Run ONLY demo tickets seeder
        $this->call([
            TicketsDemoSeeder::class,
        ]);

        $newTotal = \Bithoven\Tickets\Models\Ticket::count();
        $added = $newTotal - $existingTickets;

        $this->command->info("✅ Demo data loaded! Added {$added} tickets (Total: {$newTotal})");
    }
}
