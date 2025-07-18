<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\SectionPosition;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('scrollable')->default(false);
            $table->enum('position', SectionPosition::values())->default(SectionPosition::MAIN_CONTENT->value);
            $table->timestamps();

            $table->string('tr_name')->nullable();
            $table->string('ar_name')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('sections');
    }
};
