<?php

namespace App\Console\Commands;

use App\Addons\AddonException;
use App\Addons\AddonManager;
use Illuminate\Console\Command;

class AddonActivate extends Command
{
    protected $signature = 'addon:activate {slug : Folder name / slug of the add-on}';

    protected $description = 'Activate an installed add-on';

    public function handle(AddonManager $manager): int
    {
        try {
            $addon = $manager->activate((string) $this->argument('slug'));
        } catch (AddonException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("[{$addon->slug}] is now active.");

        return self::SUCCESS;
    }
}
