<?php

use Illuminate\Database\Seeder;

class AdministratorSeeder extends Seeder
{
  protected $admins;

  public function __construct()
  {
    $this->admins = [
      [
        'email' => 'admin@example.com',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
      ]
    ];
  }

  /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->admins as $admin) {
          \App\Models\Nova\Administrator::query()->create($admin);
        }
    }
}
