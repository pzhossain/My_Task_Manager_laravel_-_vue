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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['new', 'in_progress', 'completed', 'canceled'])->default('new');
            $table->softDeletes(); // Enable soft deletes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

// আমার টাক্স ম্যানেজার এপ্লিকেশনের TaskController এর জন্য index, store, show, update, destroy (এখানে soft delete হবে), trashed (সফ্ট ডিলিট করা টাস্ক গুলো রিটান করবে), restore (soft delete করা টাক্স ), forceDelete, filter(  $table->enum('status', ['new', 'in_progress', 'completed', 'canceled'])->default('new'); এই স্ট্যাটাস অনুযায়ী টাক্স গুলো ফিল্টার হবে), updateStatus (স্ট্যাটাস অপডেট হবে), summary (কোন স্ট্যাটাসের কতগুলো টাস্ক আছে সেটা রিটান করবে, না থাকলে 0(শুন্য) রিটান করবে ), এই ফাংশনগুলো তৈরি করতে হবে । আমার Request ফাইলের নাম- CreateTaskRequest ও UpdateTaskRequest । আমার Policy ফাইলের নাম TaskPolicy যা AuthServiceProvider এ রেজিস্টার করা হয়েছে। এখন তুমি আমার জন্য laravel latest version এর কনভেনশন ও লারাভেলের বেস্ট প্রাকটিস অনুযায়ী এই ফাংশনগুলো লিখে দাও। আমি try-catch ব্যাবহার করছি এবং try {            
//             return response()->json([
//                 'status' => 'success',
//                 'data' => $task
//             ], 200);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Task not found',
//                 'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
//             ], 404);
//         } এভাবে রিটার্ন করছি ।
 
