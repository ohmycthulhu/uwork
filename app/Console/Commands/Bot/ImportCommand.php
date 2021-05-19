<?php

namespace App\Console\Commands\Bot;

use App\Models\Authentication\Bot;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot-token:import {token} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports existing token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $token = $this->argument('token');
      $name = $this->option('name');

      Bot::createBot($token, $name);
        return 0;
    }
}
