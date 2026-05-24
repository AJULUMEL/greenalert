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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->longText('description');
            $table->enum('severity', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->enum('status', ['Open', 'On Progress', 'Resolved'])->default('Open');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->date('incident_date');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('severity');
            $table->index('status');
            $table->index('reported_by');
            $table->index('incident_date');
            $table->index('created_at');
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
