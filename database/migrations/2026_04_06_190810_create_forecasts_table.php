
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

        Schema::create('forecasts', function (Blueprint $table) {

            $table->id();

            $table->date('forecast_date');           // What date we're predicting

            $table->decimal('predicted_balance', 12, 2);  // Predicted money amount

            $table->decimal('confidence_score', 5, 2);    // How sure? (0-100)

            $table->string('method')->default('arima');   // AI method used

            $table->text('notes')->nullable();       // Extra information

            $table->timestamps();

        });

    }

    /**

     * Reverse the migrations.

     */

    public function down(): void

    {

        Schema::dropIfExists('forecasts');

    }

};

