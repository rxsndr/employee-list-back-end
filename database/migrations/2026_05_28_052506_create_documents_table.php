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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->enum('status', ['completed', 'in progress', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->date('deadline_date');
            $table->string('given_by');
            $table->longText('signature')->nullable();
            $table->timestamps();
        });
    }
};
