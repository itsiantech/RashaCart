<?php

namespace App\Console\Commands;

use App\Addons\AddonException;
use App\Addons\AddonManager;
use Illuminate\Console\Command;

class AddonDeactivate extends Command
{
    protected $signature = 'addon:deactivate {slug : Folder name / slug of the add-on}';

    protected $description = 'Deactivate an add-on without touching its data';

    public function handle(AddonManager $manager): int
    {
        try {
            $addon = $manager->deactivate((string) $this->argument('slug'));
        } catch (AddonException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("[{$addon->slug}] is now inactive. Its data was kept.");

        return self::SUCCESS;
    }
}
