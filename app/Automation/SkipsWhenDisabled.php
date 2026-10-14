<?php

namespace App\Automation;

/**
 * Use in queue jobs and event listeners: handle() must call
 * $this->skipUnlessEnabled('module_key') first — OFF means truly off
 * (no API calls, no AI cost).
 */
trait SkipsWhenDisabled
{
    protected function skipUnlessEnabled(string $key): bool
    {
        return \App\Services\AutomationRegistry::isEnabled($key);
    }

    protected function skipUnlessLive(string $key): bool
    {
        return \App\Services\AutomationRegistry::isLive($key);
    }
}
