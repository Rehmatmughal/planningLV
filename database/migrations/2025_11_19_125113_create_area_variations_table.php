<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('area_variations', function (Blueprint $table) {
            $table->id();

            // Related plot
            $table->unsignedBigInteger('plot_id');

            /* Plot area before this variation */
            $table->decimal('previous_area', 10, 2);

            /*Actual measured area after this variation */
            // $table->decimal('measured_area', 10, 2)->default(0)->nullable();
            $table->decimal('measured_area', 10, 2)->nullable();

            // start
            /* Plot condition at the time of measurement */
            
            $table->enum('road_status_at_time', ['complete', 'not_complete'])
                ->nullable();

            $table->enum('sewer_status_at_time', ['constructed', 'not_constructed'])
                ->nullable();

            $table->enum('lop_status_at_time', ['lop', 'non_lop', 'mortgaged'])
                ->nullable();

            $table->enum('overall_status_at_time', ['developed', 'under_development', 'not_developed'])  
                ->nullable();

            /*
             * Possession status at the time of this area variation.
             * Values:
             * possessionable
             * non_lop_possessionable
             * under_development_possessionable
             * not_possessionable
             */
            $table->string('possession_status')->nullable();

            //  * Person who measured the plot

            $table->string('measured_by')->nullable();

            
            //  * Date of measurement

            $table->date('measured_date')->nullable();

            //  * Additional notes / comments
             
            $table->text('remarks')->nullable();

            //  * Origin of the record:
            //  * survey, old_record, etc.
    
            $table->string('source')->default('survey');

        
            //  * Area Variation workflow status:
             
            //  * 1 = Pending Review
            //  * 2 = Ready for Print
            //  * 3 = Printed
             
            $table->unsignedTinyInteger('workflow_status')->default(1);

            /*
             * Created / Updated timestamps
             */
            $table->timestamps();

            /*
             * Soft Delete
             *
             * Area Variation records will not be
             * permanently deleted during normal use.
             */
            $table->softDeletes();

            /*
             * Foreign key
             */
            $table->foreign('plot_id')
                ->references('id')
                ->on('plots')
                ->onDelete('cascade');

            /*
             * Indexes
             */
            $table->index([
                'plot_id',
                'measured_date'
            ]);

            $table->index([
                'plot_id',
                'workflow_status'
            ]);
            // end
            // Optional notes
            // $table->string('measured_by')->nullable();   // Surveyor name optional
            // $table->date('measured_date')->nullable();   // Date of measurement
            // $table->text('remarks')->nullable();
            // // source indicates origin: 'survey' (normal), 'old_record' (imported historical), or any other string
            // $table->string('source')->default('survey');

            // $table->timestamps();

            // $table->foreign('plot_id')
            //     ->references('id')->on('plots')
            //     ->onDelete('cascade');
            // $table->index(['plot_id', 'measured_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_variations');
    }
};
