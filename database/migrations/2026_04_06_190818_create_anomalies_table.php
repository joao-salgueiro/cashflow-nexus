
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

        Schema::create('anomalies', function (Blueprint $table) {

            $table->id();

            $table->foreignId('cash_flow_id')        // Link to cash_flows table

                ->constrained()

                ->onDelete('cascade');

            $table->string('anomaly_type');          // Spending spike, Income drop, etc

            $table->enum('severity', ['low', 'medium', 'high']); // How unusual?

            $table->decimal('expected_amount', 12, 2);   // What we expected

            $table->decimal('actual_amount', 12, 2);     // What actually happened

            $table->text('description')->nullable(); // Explanation

            $table->timestamps();

        });

    }

    /**

     * Reverse the migrations.

     */

    public function down(): void

    {

        Schema::dropIfExists('anomalies');

    }

};

