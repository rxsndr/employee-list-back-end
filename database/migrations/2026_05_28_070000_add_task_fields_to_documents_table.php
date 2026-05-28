<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('task_name')->after('id');
            $table->enum('status', ['completed', 'in progress', 'pending'])->default('pending')->after('task_name');
            $table->text('notes')->nullable()->after('status');
            $table->date('deadline_date')->after('notes');
            $table->string('given_by')->after('deadline_date');
            $table->longText('signature')->nullable()->after('given_by');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'task_name',
                'status',
                'notes',
                'deadline_date',
                'given_by',
                'signature',
            ]);
        });
    }
};
