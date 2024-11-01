<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');// e.g. admin, steward
            $table->boolean('is_global')->default(0);//admin - local - 0, steward - global - 1

            $table->boolean('can_create_articles')->default(1);
            $table->boolean('can_edit_articles')->default(1);
            $table->boolean('can_delete_articles')->default(0);
            $table->boolean('can_restore_articles')->default(0);
            $table->boolean('can_view_deleted_articles')->default(0);
            //$table->boolean('can_patrol_revisions')->default(0);
            //$table->boolean('can_check_revisions')->default(0);
            $table->boolean('can_delete_revisions')->default(0);
            $table->boolean('can_restore_revisions')->default(0);
            $table->boolean('can_view_deleted_revisions')->default(0);
            //$table->boolean('can_edit_articles')->default(0);
            //$table->boolean('can_comment_articles')->default(0);
            //$table->boolean('can_revert_edits_to_old_version')->default(0);
            //$table->boolean('can_oldedit')->default(0);
            $table->boolean('can_view_revision_user_ip')->default(0);
            //$table->boolean('can_check_user_ip')->default(0);
            $table->boolean('can_create_wikis')->default(0);
            $table->boolean('can_close_wikis')->default(0);
            $table->boolean('can_open_wikis')->default(0);

            $table->boolean('can_manage_local_userrights')->default(0);
            $table->boolean('can_manage_global_userrights')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_groups');
    }
};
