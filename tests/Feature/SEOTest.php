<?php

namespace Tests\Feature;

use App\Models\User\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SEOTest extends TestCase
{
  use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
      $this->fillDatabase();
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);

        $profiles = Profile::query()->get();

        $content = $response->content();

        foreach ($profiles as $profile) {
          /* @var Profile $profile */
          if ($profile->isApproved) {
            $this->assertStringContainsString($this->getProfileUrl($profile), $content);
          } else {
            $this->assertStringNotContainsString($this->getProfileUrl($profile), $content);
          }
        }
    }

    protected function getProfileUrl(Profile $profile): string {
      return "profiles_list/{$profile->id}";
    }
}
