<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                // Ensure all required columns exist with correct types
                if (!Schema::hasColumn('students', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    $table->foreign('user_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                }

                if (!Schema::hasColumn('students', 'nis')) {
                    $table->string('nis')->unique();
                }

                // Standardize on class_id instead of classroom_id
                if (Schema::hasColumn('students', 'classroom_id') && !Schema::hasColumn('students', 'class_id')) {
                    // Copy data from classroom_id to class_id
                    $table->foreignId('class_id')->nullable()->after('nis');
                    DB::statement('UPDATE students SET class_id = classroom_id');
                    
                    // Drop the old column and its foreign key
                    $table->dropForeign(['classroom_id']);
                    $table->dropColumn('classroom_id');
                }

                // Add foreign key for class_id if it doesn't exist
                if (Schema::hasColumn('students', 'class_id')) {
                    try {
                        $table->foreign('class_id')
                            ->references('id')
                            ->on('school_classes')
                            ->onDelete('set null');
                    } catch (\Exception $e) {
                        // Foreign key might already exist
                    }
                }

                // Ensure all other required columns exist
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

                // Ensure timestamps exist
                if (!Schema::hasColumn('students', 'created_at')) {
                    $table->timestamps();
                }
            });
        } else {
            // Create students table if it doesn't exist
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('nis')->unique();
                $table->foreignId('class_id')->nullable();
                $table->string('name')->nullable();
                $table->enum('gender', ['L', 'P'])->nullable();
                $table->date('birth_date')->nullable();
                $table->text('address')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
                    
                $table->foreign('class_id')
                    ->references('id')
                    ->on('school_classes')
                    ->onDelete('set null');
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
                // We don't want to completely drop the table in down()
                // Just revert any changes that this migration made
                
                // If we switched from classroom_id to class_id
                if (Schema::hasColumn('students', 'class_id') && !Schema::hasColumn('students', 'classroom_id')) {
                    $table->dropForeign(['class_id']);
                    $table->renameColumn('class_id', 'classroom_id');
                    $table->foreign('classroom_id')
                        ->references('id')
                        ->on('school_classes')
                        ->onDelete('set null');
                }
            });
        }
    }
};