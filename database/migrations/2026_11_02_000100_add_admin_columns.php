<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable()->after('college');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('replied_at');      // when an admin opened it
            $table->timestamp('resolved_at')->nullable()->after('read_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('suspended_at'));
        Schema::table('messages', fn (Blueprint $table) => $table->dropColumn(['read_at', 'resolved_at']));
    }
};
