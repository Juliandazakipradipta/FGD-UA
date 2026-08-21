<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->string('session_topic');
            $table->string('notulis_name')->nullable();
            $table->date('session_date')->default(now());
            
            // 1. Problem - Penyebab - Solusi
            $table->text('problem')->nullable();
            $table->text('cause')->nullable();
            $table->text('solution')->nullable();

            // 2. Action Plan
            $table->text('action_ppg')->nullable();
            $table->text('action_description')->nullable();
            $table->text('action_name')->nullable();
            $table->text('action_participants')->nullable();
            $table->text('action_time')->nullable();
            $table->text('action_budget')->nullable();

            // 3. Peran 5 Unsur
            $table->text('role_keimaman')->nullable();
            $table->text('role_pengurus')->nullable();
            $table->text('role_parents')->nullable();
            $table->text('role_muballigh')->nullable();
            $table->text('role_educator')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minutes');
    }
};
