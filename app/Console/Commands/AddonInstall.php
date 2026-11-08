<?php

namespace App\Console\Commands;

use App\Addons\AddonException;
use App\Addons\AddonManager;
use Illuminate\Console\Command;

class AddonInstall extends Command
{
    protected $signature = 'addon:install {slug : Folder name / slug of the add-on}';

    protected $description = 'Run an add-on\'s migrations and mark it as installed';

    public function handle(AddonManager $manager): int
    {
        $slug = (string) $this->argument('slug');

        try {
            $addon = $manager->install($slug);
        } catch (AddonException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("[{$addon->slug}] installed.");
        $this->line('Next: php artisan addon:activate '.$addon->slug);

        return self::SUCCESS;
    }
}
