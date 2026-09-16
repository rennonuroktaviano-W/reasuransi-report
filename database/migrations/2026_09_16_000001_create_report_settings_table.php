<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('commission_rate', 5, 4)->default(0.1000);
            $table->string('company_name', 150)->nullable();
            $table->string('report_title', 150)->default('Laporan Reasuransi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_settings');
    }
};
