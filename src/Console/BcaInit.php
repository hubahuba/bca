<?php

namespace Ngungut\Bca\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BcaInit extends Command
{
    /**
     * The console command init bca storage folder.
     *
     * @var string
     */
    protected $signature = 'bca:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create storage folder for storing BCA log and token.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!File::isDirectory(storage_path('bca'))) {
            File::makeDirectory(storage_path('bca'));
        }
    }
}