<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settlements')) {
            return;
        }

        Schema::table('settlements', function (Blueprint $table) {
            if (!Schema::hasColumn('settlements', 'colocation_id')) {
                $table->foreignId('colocation_id')->nullable()->after('id')->constrained('colocations')->cascadeOnDelete();
            }
        });

        // Backfill from memberships if possible (same colocation for sender and receiver).
        $settlements = DB::table('settlements')->whereNull('colocation_id')->get();
        foreach ($settlements as $settlement) {
            $colocationId = DB::table('memberships as m1')
                ->join('memberships as m2', 'm1.colocation_id', '=', 'm2.colocation_id')
                ->where('m1.user_id', $settlement->sender_id)
                ->where('m2.user_id', $settlement->receiver_id)
                ->whereNull('m1.left_at')
                ->whereNull('m2.left_at')
                ->value('m1.colocation_id');

            if ($colocationId) {
                DB::table('settlements')
                    ->where('id', $settlement->id)
                    ->update(['colocation_id' => $colocationId]);
            }
        }
    }

    public function down(): void
    {
        // no-op
    }
};
