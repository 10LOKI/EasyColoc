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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colocation_id')->nullable()->constrained('colocations')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        $now = now();
        foreach (['Rent', 'Alimentation', 'Utilities', 'Internet', 'Transport'] as $name) {
            DB::table('categories')->insert([
                'colocation_id' => null,
                'name' => $name,
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
        Schema::dropIfExists('categories');
    }
};
