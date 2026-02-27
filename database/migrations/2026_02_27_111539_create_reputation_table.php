<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reputation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('colocation_id')->nullable()->constrained('colocations')->nullOnDelete();
            $table->integer('score');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed baseline reputation entries for existing users.
        $now = now();
        $users = DB::table('users')->select('id')->get();

        foreach ($users as $user) {
            DB::table('reputation')->insert([
                'user_id' => $user->id,
                'colocation_id' => null,
                'score' => 0,
                'reason' => 'Initial score',
                'description' => 'Baseline reputation entry created by migration.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reputation');
    }
};
