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
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->string('image')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('instructor_id')->nullable()->constrained()->onDelete('set null');
        });

        // Data migration logic: Create instructors from existing course data
        $courses = DB::table('courses')->get();
        $instructorMap = [];

        foreach ($courses as $course) {
            if ($course->instructor_name) {
                $key = $course->instructor_name;
                
                if (!isset($instructorMap[$key])) {
                    $instructorId = DB::table('instructors')->insertGetId([
                        'name' => $course->instructor_name,
                        'title' => $course->instructor_title,
                        'bio' => $course->instructor_bio,
                        'image' => $course->instructor_image,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $instructorMap[$key] = $instructorId;
                }

                DB::table('courses')->where('id', $course->id)->update([
                    'instructor_id' => $instructorMap[$key]
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
        });
        Schema::dropIfExists('instructors');
    }
};
