<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInitialTables extends Migration
{
    public function up()
    {
        Schema::create('departments', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('users', function(Blueprint $table){
            $table->id();
            $table->string('employee_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('staff');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->string('employee_level')->nullable();
            $table->date('hire_date')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('leave_types', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->boolean('is_paid')->default(true);
            $table->integer('days_allowed_management')->default(20);
            $table->integer('days_allowed_staff')->default(15);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('leave_balances', function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('leave_type_id')->constrained('leave_types');
            $table->integer('year');
            $table->integer('total_days')->default(0);
            $table->integer('used_days')->default(0);
            $table->integer('remaining_days')->default(0);
            $table->timestamps();
        });

        Schema::create('leave_requests', function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('leave_type_id')->constrained('leave_types');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days')->default(0);
            $table->text('reason')->nullable();
            $table->string('documentation_path')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('users');
        Schema::dropIfExists('departments');
    }
}
