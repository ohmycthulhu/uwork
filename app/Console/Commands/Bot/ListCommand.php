<?php

namespace App\Console\Commands\Bot;

use App\Models\Authentication\Bot;
use Illuminate\Console\Command;

class ListCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'bot-token:list';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Lists existing bot tokens';

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
    $bots = Bot::query()->get();
    echo "Name\t\tToken\t\t\tStatus\n";
    foreach ($bots as $bot) {
      echo "{$bot->name}\t\t{$bot->token}\t" . ($bot->enabled ? 'Enabled' : 'Disabled') . "\n";
    }
    return 0;
  }
}
