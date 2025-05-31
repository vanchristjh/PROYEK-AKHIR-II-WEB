<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // Get teacher users
        $teachers = User::whereHas('role', function($query) {
            $query->where('slug', 'guru');
        })->get();

        // Get subjects
        $subjects = Subject::all();

        $exams = [
            [
                'title' => 'Ujian Tengah Semester Matematika',
                'description' => 'UTS Matematika Semester 1',
                'duration' => 120, // in minutes
                'start_time' => now()->addDays(14),
                'end_time' => now()->addDays(14)->addHours(2),
                'passing_score' => 75,
                'exam_type' => 'mid',
                'questions' => [
                    [
                        'content' => 'Selesaikan persamaan: 3x + 5 = 20',
                        'question_type' => 'multiple_choice',
                        'points' => 20,
                        'options' => [
                            ['content' => '5', 'is_correct' => true],
                            ['content' => '6', 'is_correct' => false],
                            ['content' => '7', 'is_correct' => false],
                            ['content' => '8', 'is_correct' => false],
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Ujian Akhir Semester Fisika',
                'description' => 'UAS Fisika Semester 1',
                'duration' => 120,
                'start_time' => now()->addDays(21),
                'end_time' => now()->addDays(21)->addHours(2),
                'passing_score' => 70,
                'exam_type' => 'final',
                'questions' => [
                    [
                        'content' => 'Berapakah percepatan gravitasi bumi?',
                        'question_type' => 'multiple_choice',
                        'points' => 25,
                        'options' => [
                            ['content' => '9.8 m/s²', 'is_correct' => true],
                            ['content' => '8.9 m/s²', 'is_correct' => false],
                            ['content' => '10.8 m/s²', 'is_correct' => false],
                            ['content' => '7.8 m/s²', 'is_correct' => false],
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Ujian Biologi - Genetika',
                'description' => 'Ujian tentang genetika dasar',
                'duration' => 90,
                'start_time' => now()->addDays(10),
                'end_time' => now()->addDays(10)->addHours(1)->addMinutes(30),
                'passing_score' => 75,
                'exam_type' => 'mid',
                'questions' => [
                    [
                        'content' => 'Apa yang dimaksud dengan alel?',
                        'question_type' => 'multiple_choice',
                        'points' => 15,
                        'options' => [
                            ['content' => 'Bentuk alternatif dari suatu gen', 'is_correct' => true],
                            ['content' => 'Bagian dari kromosom', 'is_correct' => false],
                            ['content' => 'Proses pembelahan sel', 'is_correct' => false],
                            ['content' => 'Jenis DNA', 'is_correct' => false],
                        ]
                    ]
                ]
            ]
        ];

        foreach ($exams as $examData) {
            // Get random subject and teacher
            $subject = $subjects->random();
            $teacher = $teachers->random();

            // Get questions data and remove from exam data
            $questions = $examData['questions'];
            unset($examData['questions']);

            // Create exam
            $exam = Exam::create(array_merge($examData, [
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id
            ]));

            // Create questions for the exam
            foreach ($questions as $questionData) {
                $options = $questionData['options'];
                unset($questionData['options']);

                $question = Question::create(array_merge($questionData, [
                    'exam_id' => $exam->id
                ]));

                // Create options for the question
                foreach ($options as $optionData) {
                    Option::create(array_merge($optionData, [
                        'question_id' => $question->id
                    ]));
                }
            }
        }
    }
}
