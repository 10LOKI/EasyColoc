<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expenses')) {
            return;
        }

        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('paid_by')->constrained('categories')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // no-op
    }
};
