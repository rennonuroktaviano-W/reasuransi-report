<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reinsurance_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number', 50)->unique();
            $table->unsignedBigInteger('production_id');
            $table->text('claim_cause');
            $table->decimal('total_claim_value', 18, 2);
            $table->decimal('reinsurance_recovery', 18, 2);
            $table->string('claim_status', 40);
            $table->timestamps();

            $table->index('claim_number');
            $table->foreign('production_id')
                ->references('id')
                ->on('reinsurance_productions')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reinsurance_claims');
    }
};
