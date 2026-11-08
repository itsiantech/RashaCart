<?php

namespace App\Console\Commands;

use App\Addons\AddonManager;
use Illuminate\Console\Command;

class AddonList extends Command
{
    protected $signature = 'addon:list';

    protected $description = 'List every add-on discovered in the addons directory and its status';

    public function handle(AddonManager $manager): int
    {
        $addons = $manager->all();

        if ($addons === []) {
            $this->warn('No add-ons found in '.$manager->path());

            return self::SUCCESS;
        }

        $this->table(
            ['Slug', 'Name', 'Version', 'Status', 'Depends on', 'Provides'],
            array_map(fn (array $addon) => [
                $addon['slug'],
                $addon['name'],
                $addon['version'],
                $addon['status'],
                $addon['depends_on'] === [] ? '-' : implode(', ', $addon['depends_on']),
                $addon['provides'] === [] ? '-' : implode('; ', $addon['provides']),
            ], $addons)
        );

        foreach ($addons as $addon) {
            foreach ($addon['problems'] as $problem) {
                $this->line(" <fg=yellow>!</> {$addon['slug']}: {$problem}");
            }
        }

        return self::SUCCESS;
    }
}
