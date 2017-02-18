<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);
         $this->call(PostsSeeder::class);
        Model::reguard();
    }

}
class PostsSeeder extends Seeder {

    public function run()
    {
        DB::table('Posts')->delete();
        Post::create([
            'title' => 'First Post',
            'slug' => 'first-post',
            'excerpt' => 'First Post body',
            'content' => 'Content First Post body',
            'published' => true,
            //'published_at' => DB::raw('NOW()'), // для DateTime
            'published_at' => DB::raw('CURRENT_TIMESTAMP'), // для timestamp
        ]);

        Post::create([
            'title' => 'Second Post',
            'slug' => 'second-post',
            'excerpt' => 'Second Post body',
            'content' => 'Content Second Post body',
            'published' => false,
            'published_at' => DB::raw('CURRENT_TIMESTAMP'),
        ]);

        Post::create([
            'title' => 'Third Post',
            'slug' => 'third-post',
            'excerpt' => 'Third Post body',
            'content' => 'Content Third Post body',
            'published' => false,
            'published_at' => DB::raw('CURRENT_TIMESTAMP'),
        ]);
    }
}
