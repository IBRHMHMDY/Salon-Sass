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
        Schema::create('service_user', function (Blueprint $table) {
            $table->id();

            // الموظف
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // الخدمة
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();

            // منع التكرار (الموظف لا يرتبط بنفس الخدمة مرتين)
            $table->unique(['user_id', 'service_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_user');
    }
};
