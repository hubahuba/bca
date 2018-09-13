<?php

namespace Ngungut\Bca\Console;

use Illuminate\Console\Command;
use Ngungut\Bca\Bca;
use Ngungut\Bca\Exception\BCAException;

class BcaSandbox extends Command
{
    /**
     * The console command test bca sandbox request.
     *
     * @var string
     */
    protected $signature = 'bca:sandbox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make auth test request to BCA sandbox server.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $bca = new Bca;
        try {
            $bca->auth();
        } catch (BCAException $e) {
            echo $e;
        }
    }
}