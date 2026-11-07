<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('weekly_limit', 10, 2)->default(0);
            $table->boolean('is_savings')->default(false); // the "Savings" card fills from savings-goal deposits
            $table->timestamps();
        });

        // The four categories every student starts with (they were in the config file before).
        $now = now();
        DB::table('categories')->insert([
            ['name' => 'Food',            'weekly_limit' => 1100, 'is_savings' => false, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Transport',       'weekly_limit' => 600,  'is_savings' => false, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'School supplies', 'weekly_limit' => 500,  'is_savings' => false, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Savings',         'weekly_limit' => 400,  'is_savings' => true,  'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
