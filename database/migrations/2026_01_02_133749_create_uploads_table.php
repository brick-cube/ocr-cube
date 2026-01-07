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
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->string('og_name', 100);
            $table->string('name', 100);
            $table->string('file_sha', 100);
            $table->string('status', 20);
            $table->text('ocr_data', 100)->nullable();
            $table->string('tl_file_id', 100)->nullable();
            $table->string('tl_job_id', 100)->nullable();
            $table->string('tl_status', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
