<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'colocation_id')) {
                $table->foreignId('colocation_id')->nullable()->after('id')->constrained('colocations')->cascadeOnDelete();
            }
        });

        $now = now();
        $essential = ['Rent', 'Alimentation', 'Utilities', 'Internet', 'Transport'];
        foreach ($essential as $name) {
            $exists = DB::table('categories')
                ->whereNull('colocation_id')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->exists();

            if (!$exists) {
                DB::table('categories')->insert([
                    'colocation_id' => null,
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // no-op
    }
};
