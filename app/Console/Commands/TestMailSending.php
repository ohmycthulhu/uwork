<?php

namespace App\Console\Commands;

use App\Mail\TestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailSending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mail {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests if mail sending works';

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
      $email = $this->option('email') ?? 'elvin.bayramov@protonmail.com';

      Mail::to([$email])
        ->send(new TestMail);
        return 0;
    }
}
