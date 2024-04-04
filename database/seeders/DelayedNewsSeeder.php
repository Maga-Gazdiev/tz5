<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;

class DelayedNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $currentTime = now();

    $delayedTime = $currentTime->addMinutes(10);

    $unpublishedNews = News::where('published', false)->first();

    $unpublishedNews->update(['published_at' => $delayedTime]);
}

}
