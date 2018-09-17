<?php

namespace Ngungut\Bca\Console;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Ngungut\Bca\Client;
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
        $bca = new Client;
        try {
            $response = $bca->getBalance('BCAAPI2016', ['0201245680']);
            dd($response);
        } catch (ClientException|BCAException $e) {
            echo $e;
        }
    }
}