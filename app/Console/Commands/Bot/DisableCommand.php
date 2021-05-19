<?php

namespace App\Console\Commands\Bot;

use App\Models\Authentication\Bot;
use Illuminate\Console\Command;

class DisableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot-token:disable {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disables the token';

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
      $bot = Bot::query()->token($token)->first();
      if (!$bot) {
        echo "Bot token not found\n";
        return 1;
      }
      $bot->setState(false);
      echo "Bot was disabled successfully\n";
        return 0;
    }
}
