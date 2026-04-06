
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

        Schema::create('scenarios', function (Blueprint $table) {

            $table->id();

            $table->string('name');                  // Scenario name

            $table->text('description')->nullable(); // What did we change?

            $table->json('parameters');              // The changes (JSON format)

            $table->decimal('projected_balance', 12, 2);  // Result

            $table->string('projected_period')->default('monthly'); // Monthly/Yearly

            $table->timestamps();

        });

    }

    /**

     * Reverse the migrations.

     */

    public function down(): void

    {

        Schema::dropIfExists('scenarios');

    }

};

