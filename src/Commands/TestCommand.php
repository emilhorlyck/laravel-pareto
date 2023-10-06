<?php

namespace EmilHorlyck\LaravelPareto;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    public $signature = 'xpareto:test';

    public $description = 'Test if command works';

    public function handle(): int
    {
        this->info('Test command');
    }
}
