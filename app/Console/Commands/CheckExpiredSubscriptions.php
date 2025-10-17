<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and update machine_id to EXPIRED';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');
        
        // Find all members with expired subscriptions
        $expiredMembers = Member::where('expiry_date', '<', now())
            ->where('machine_id', '!=', 'EXPIRED')
            ->whereNotNull('machine_id')
            ->get();
        
        $count = 0;
        
        foreach ($expiredMembers as $member) {
            // Update machine_id to EXPIRED
            $member->machine_id = 'EXPIRED';
            $member->save();
            
            $count++;
            $this->line("Updated member {$member->email} (ID: {$member->id}) - Expired on: {$member->expiry_date}");
        }
        
        if ($count > 0) {
            $this->info("Successfully updated {$count} expired subscriptions.");
        } else {
            $this->info('No expired subscriptions found.');
        }
        
        return Command::SUCCESS;
    }
}
