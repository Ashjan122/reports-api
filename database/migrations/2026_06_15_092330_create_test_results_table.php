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
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');

            $table->string('client_ref')->nullable();
            $table->string('lab_sample_ref')->nullable();
            $table->integer('req_test_age')->nullable();
            $table->integer('act_test_age')->nullable();
            $table->date('date_of_test')->nullable();
            $table->string('condition_upon_receipt')->default('Normal');
            $table->string('condition_at_test')->default('Saturated');

            $table->decimal('dim_l', 8, 1)->nullable();
            $table->decimal('dim_w', 8, 1)->nullable();
            $table->decimal('dim_h', 8, 1)->nullable();

            $table->decimal('mass_kg', 8, 3)->nullable();
            $table->decimal('density', 8, 0)->nullable();
            $table->decimal('max_load_kn', 10, 0)->nullable();
            $table->decimal('comp_strength', 8, 1)->nullable();
            $table->string('type_of_fracture')->default('SF');

            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
