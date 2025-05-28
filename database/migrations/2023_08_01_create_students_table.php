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
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                // Add new columns only if they don't exist
                if (!Schema::hasColumn('students', 'name')) {
                    $table->string('name')->nullable();
                }
                if (!Schema::hasColumn('students', 'gender')) {
                    $table->enum('gender', ['L', 'P'])->nullable();
                }
                if (!Schema::hasColumn('students', 'birth_date')) {
                    $table->date('birth_date')->nullable();
                }
                if (!Schema::hasColumn('students', 'address')) {
                    $table->text('address')->nullable();
                }
                if (!Schema::hasColumn('students', 'phone_number')) {
                    $table->string('phone_number')->nullable();
                }
                if (!Schema::hasColumn('students', 'email')) {
                    $table->string('email')->nullable();
                }
                if (!Schema::hasColumn('students', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active');
                }

                // If the students table has classroom_id but not class_id, we might need to add a class_id column
                if (!Schema::hasColumn('students', 'class_id') && Schema::hasColumn('students', 'classroom_id')) {
                    // We won't rename because it might break existing relationships
                    // Just add the new column and let app logic handle both
                    $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                // Drop all the columns we might have added
                $columns = [
                    'name', 'gender', 'birth_date', 'address', 
                    'phone_number', 'email', 'status'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('students', $column)) {
                        $table->dropColumn($column);
                    }
                }
                
                if (Schema::hasColumn('students', 'class_id')) {
                    $table->dropConstrainedForeignId('class_id');
                }
            });
        }
    }
};
