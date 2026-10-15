<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('App\Database\Seeds\UserSeeder');
        $this->call('App\Database\Seeds\SettingSeeder');
        $this->call('App\Database\Seeds\ProgramSeeder');
        $this->call('App\Database\Seeds\ExtracurricularSeeder');
        $this->call('App\Database\Seeds\AchievementSeeder');
        $this->call('App\Database\Seeds\TestimonialSeeder');
        $this->call('App\Database\Seeds\NewsSeeder');
        $this->call('App\Database\Seeds\EventSeeder');
        $this->call('App\Database\Seeds\FaqSeeder');
        $this->call('App\Database\Seeds\GallerySeeder');
        $this->call('App\Database\Seeds\TeacherSeeder');
        $this->call('App\Database\Seeds\DownloadSeeder');
        $this->call('App\Database\Seeds\MenuSeeder');
    }
}
