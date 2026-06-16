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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('report_ref')->nullable();
            $table->string('client_request_ref')->nullable();
            $table->date('date_reported')->nullable();
            $table->date('date_received')->nullable();

            $table->string('client_name')->nullable();
            $table->string('project')->nullable();
            $table->string('project_client')->nullable();
            $table->string('contractor')->default('Not Given');
            $table->string('consultant')->default('Not Given');
            $table->string('concrete_supplier')->default('Not Given');
            $table->string('mix_grade')->nullable();
            $table->string('slump_mm')->nullable();
            $table->string('air_content')->default('Not Given');
            $table->string('sampling_method')->default('Not Given');
            $table->text('other_information')->default('Not Given');
            $table->string('pour_location')->nullable();

            $table->integer('total_cubes')->nullable();
            $table->date('date_of_casting')->nullable();
            $table->string('sampled_by')->default('Client');
            $table->string('sampling_location')->nullable();
            $table->string('sampling_cert_ref')->default('Not Given');
            $table->string('compaction_method')->default('Manual');

            $table->string('lab_curing_method')->default('BS EN 12390-2:2019');
            $table->string('test_method')->default('BS EN 12390-3:2019');
            $table->string('density_method')->default('BS EN 12390-7:2019');
            $table->string('volume_determination')->default('By Calculation');
            $table->string('test_location')->nullable();
            $table->string('method_variation')->default('None');
            $table->string('cubes_delivered_by')->default('Client');
            $table->string('dimensions')->default('Checked Nominal');
            $table->string('nominal_size')->default('150X150X150');
            $table->string('removal_of_fins')->default('Manual-Using steel file');
            $table->string('curing_at_lab')->default('Water 20±2°C');
            $table->string('tested_by')->nullable();

            $table->enum('report_type', ['final', 'interim'])->default('final');
            $table->string('structural_reference')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
