<?php

namespace LowB\LineSignup\Commands;

use Illuminate\Console\Command;

class LineSignupCommand extends Command
{
    public $signature = 'line-signup';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
