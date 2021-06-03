<?php

use App\Models\Categories\CategoryService;
use App\Models\User\Profile;
use App\Models\User\ProfileSpeciality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProfileSeeder extends Seeder
{
  /* @var array $images */
  protected $images;

  public function __construct()
  {
    $this->images = [
      storage_path("images/example_1.jpg"),
      storage_path("images/example_2.jpg"),
      storage_path("images/example_3.jpg"),
      storage_path("images/example_4.jpg"),
    ];

  }

  /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::all();
      $services = CategoryService::query()->get();
      $minImagesCount = 1;
      $maxImagesCount = sizeof($this->images);

        foreach ($users as $user) {
          $p = factory(\App\Models\User\Profile::class)
            ->make()
            ->toArray();
          unset($p['is_approved']);
          /* @var Profile $profile */
          $profile = $user->profile()->first() ??
            $user->profile()->create($p);

          foreach ($services->shuffle()->slice(0, 3) as $service) {
            /* @var ProfileSpeciality $speciality */
            $speciality = $profile->addSpeciality(
              $service->id,
              rand(100, 5000) / 10,
              Str::random(),
            );

            for ($i = rand($minImagesCount, $maxImagesCount); $i > 0; $i--) {
              $this->addImageToSpeciality($speciality);
            }
          }

          foreach ($users->shuffle()->take(3) as $u) {
            $profile->reviews()
              ->create(
                factory(\App\Models\Profile\Review::class)
                  ->make(['user_id' => $u->id, 'speciality_id' => $profile->specialities()->inRandomOrder()->first()->id])
                  ->toArray()
              );
          }

          $profile->views()
            ->createMany(
              factory(\App\Models\Profile\ProfileView::class, 15)
                ->make()
                ->toArray()
            );

          $profile->synchronizeViews();
          $profile->synchronizeReviews();
        }
    }

    /**
     * Method to add example image to speciality
     *
     * @param ProfileSpeciality $speciality
     *
     * @return ProfileSpeciality
    */
    protected function addImageToSpeciality(ProfileSpeciality $speciality): ProfileSpeciality {
      $srcImage = $this->images[array_rand($this->images)];
      $destImage = storage_path("images/example_dest.jpg");
      \Illuminate\Support\Facades\File::copy($srcImage, $destImage);
      $image = new \Illuminate\Http\UploadedFile(
        $destImage,
        "example.jpg",
        "images/jpeg",
      null
      );

      \App\Facades\MediaFacade::upload(
        $image,
        null,
        ProfileSpeciality::class,
        $speciality->id
      );

      return $speciality;
    }
}
