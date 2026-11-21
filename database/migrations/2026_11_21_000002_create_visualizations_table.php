<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visualizations', function (Blueprint $table) {
            $table->id('visualization_id');
            $table->foreignId('url_id')
                ->constrained('urls', 'url_id')
                ->cascadeOnDelete();
            $table->dateTime('access_date_hour')->index();
            $table->json('device_info')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visualizations');
    }
};
