<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reputation')) {
            return;
        }

        Schema::table('reputation', function (Blueprint $table) {
            if (!Schema::hasColumn('reputation', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('reputation', 'colocation_id')) {
                $table->foreignId('colocation_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('reputation', 'score')) {
                $table->integer('score')->default(0)->after('colocation_id');
            }
            if (!Schema::hasColumn('reputation', 'reason')) {
                $table->string('reason')->default('Initial score')->after('score');
            }
            if (!Schema::hasColumn('reputation', 'description')) {
                $table->text('description')->nullable()->after('reason');
            }
        });

        // Backfill user_id for any pre-existing rows without user relation.
        $firstUserId = DB::table('users')->min('id');
        if ($firstUserId) {
            DB::table('reputation')->whereNull('user_id')->update(['user_id' => $firstUserId]);
        }

        // Seed baseline entries for users that do not yet have any reputation row.
        $usersWithoutEntry = DB::table('users')
            ->whereNotIn('id', DB::table('reputation')->select('user_id')->whereNotNull('user_id'))
            ->pluck('id');

        $now = now();
        foreach ($usersWithoutEntry as $userId) {
            DB::table('reputation')->insert([
                'user_id' => $userId,
                'colocation_id' => null,
                'score' => 0,
                'reason' => 'Initial score',
                'description' => 'Baseline reputation entry created by migration.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Intentional no-op to avoid destructive schema rollback in existing environments.
    }
};
