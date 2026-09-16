<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reinsurance_productions', function (Blueprint $table) {
            $table->id();
            $table->string('policy_number', 50)->unique();
            $table->string('insured_name', 150);
            $table->date('birth_date');
            $table->decimal('sum_insured', 18, 2);
            $table->decimal('retention', 18, 2);
            $table->decimal('ceded_amount', 18, 2);
            $table->string('reinsurance_type', 30);
            $table->decimal('reinsurance_premium', 18, 2);
            $table->timestamps();

            $table->index('policy_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reinsurance_productions');
    }
};
