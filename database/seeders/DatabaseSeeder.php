<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

        //Creating global user groups
        //Steward
        DB::table('user_groups')->insert([
            'name' => 'steward',
            'is_global' => 1,
            'can_create_articles' => 1,
            'can_edit_articles' => 1,
            'can_delete_articles' => 1,
            'can_restore_articles' => 1,
            'can_view_deleted_articles' => 1,
            'can_delete_revisions' => 1,
            'can_restore_revisions' => 1,
            'can_view_deleted_revisions' => 1,
            'can_view_revision_user_ip' => 1,
            'can_create_wikis' => 1,
            'can_close_wikis' => 1,
            'can_open_wikis' => 1,
            'can_manage_local_userrights' => 1,
            'can_manage_global_userrights' => 1,
            'can_delete_commons_images' => 1,
        ]);

        //Creating local user groups
        //Admin    
        DB::table('user_groups')->insert([
            'name' => 'admin',
            'is_global' => 0,
            'can_create_articles' => 1,
            'can_edit_articles' => 1,
            'can_delete_articles' => 1,
            'can_restore_articles' => 1,
            'can_view_deleted_articles' => 1,
            'can_delete_revisions' => 1,
            'can_restore_revisions' => 1,
            'can_view_deleted_revisions' => 1,
            'can_view_revision_user_ip' => 1,
            'can_create_wikis' => 0,
            'can_close_wikis' => 0,
            'can_open_wikis' => 0,
            'can_manage_local_userrights' => 1,
            'can_manage_global_userrights' => 0,
            'can_delete_commons_images' => 0,
        ]);

        //Creating your first wiki
        DB::table('wikis')->insert([
            'url' => 'my-first-wiki',
        ]);

        //Creating your first article in your first wiki
        DB::table('articles')->insert([
            'wiki_id' => 1,//it's your first wiki id
            'title' => 'My first article',
            'url_title' => 'my-first-article',
        ]);

        //Creating your first revision for your first article
        DB::table('revisions')->insert([
            'article_id' => 1,//it's your first article id
            'title' => 'My first article',
            'url_title' => 'my-first-article',
            'content' => 'Hello, this is content of your first article! Edit it!',
            'user_ip' => '127.0.0.1',//localhost
            'user_id' => 0,
        ]);
        echo 'Installation completed! Don\'t forget to register and grant steward rights to your account via database editor!';
    }
}
