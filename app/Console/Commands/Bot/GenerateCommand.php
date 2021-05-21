<?php

namespace App\Console\Commands\Bot;

use App\Models\Authentication\Bot;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'bot-token:generate {--name=}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generates new token';

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
    $token = $this->generateToken();
    $name = $this->option('name');

    Bot::createBot($token, $name);

    echo ($name ? $name."\t" : "")."$token\n";

    return 0;
  }

  protected function generateToken(): string
  {
    do {
      $token = Str::random();
    } while (Bot::query()->token($token)->first());

    return $token;
  }
}
