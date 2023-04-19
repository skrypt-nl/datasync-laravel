<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('model_updates', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->foreignId('model_event_id')->index('model_event_id');

            $table->unique(['key', 'model_event_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('model_updates');
    }
};
