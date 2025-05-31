<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // Get teacher users
        $teachers = User::whereHas('role', function($query) {
            $query->where('slug', 'guru');
        })->get();

        // Get subjects
        $subjects = Subject::all();

        $quizzes = [
            [
                'title' => 'Kuis Matematika Bab 1',
                'description' => 'Kuis tentang aljabar dasar',
                'duration' => 60, // in minutes
                'start_time' => now(),
                'end_time' => now()->addDays(7),
                'is_active' => true,
                'max_attempts' => 1,
                'randomize_questions' => false,
                'show_result' => true,
                'passing_score' => 70,
                'questions' => [
                    [
                        'content' => 'Berapakah hasil dari 2x + 3 jika x = 5?',
                        'question_type' => 'multiple_choice',
                        'points' => 10,
                        'options' => [
                            ['content' => '13', 'is_correct' => true],
                            ['content' => '10', 'is_correct' => false],
                            ['content' => '8', 'is_correct' => false],
                            ['content' => '15', 'is_correct' => false],
                        ]
                    ],
                    [
                        'content' => 'Tentukan akar dari persamaan x² - 4 = 0',
                        'question_type' => 'multiple_choice',
                        'points' => 10,
                        'options' => [
                            ['content' => '+2 dan -2', 'is_correct' => true],
                            ['content' => '+4 dan -4', 'is_correct' => false],
                            ['content' => '2 saja', 'is_correct' => false],
                            ['content' => '-2 saja', 'is_correct' => false],
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Kuis Fisika - Hukum Newton',
                'description' => 'Kuis tentang hukum-hukum Newton',
                'duration' => 45,
                'start_time' => now(),
                'end_time' => now()->addDays(5),
                'is_active' => true,
                'max_attempts' => 1,
                'randomize_questions' => false,
                'show_result' => true,
                'passing_score' => 75,
                'questions' => [
                    [
                        'content' => 'Sebuah benda diam akan tetap diam dan benda bergerak akan tetap bergerak dengan kecepatan konstan kecuali ada gaya yang bekerja padanya. Ini adalah pernyataan dari:',
                        'question_type' => 'multiple_choice',
                        'points' => 20,
                        'options' => [
                            ['content' => 'Hukum I Newton', 'is_correct' => true],
                            ['content' => 'Hukum II Newton', 'is_correct' => false],
                            ['content' => 'Hukum III Newton', 'is_correct' => false],
                            ['content' => 'Hukum Gravitasi Newton', 'is_correct' => false],
                        ]
                    ],
                    [
                        'content' => 'Hukum III Newton menyatakan bahwa:',
                        'question_type' => 'multiple_choice',
                        'points' => 20,
                        'options' => [
                            ['content' => 'Setiap aksi memiliki reaksi yang sama besar dan berlawanan arah', 'is_correct' => true],
                            ['content' => 'Gaya sama dengan massa dikali percepatan', 'is_correct' => false],
                            ['content' => 'Benda cenderung mempertahankan keadaannya', 'is_correct' => false],
                            ['content' => 'Semua benda jatuh dengan percepatan yang sama', 'is_correct' => false],
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Kuis Biologi - Sistem Tubuh',
                'description' => 'Kuis tentang sistem tubuh manusia',
                'duration' => 30,
                'start_time' => now(),
                'end_time' => now()->addDays(3),
                'is_active' => true,
                'max_attempts' => 1,
                'randomize_questions' => false,
                'show_result' => true,
                'passing_score' => 80,
                'questions' => [
                    [
                        'content' => 'Organ apa yang bertanggung jawab untuk memompa darah?',
                        'question_type' => 'multiple_choice',
                        'points' => 20,
                        'options' => [
                            ['content' => 'Paru-paru', 'is_correct' => false],
                            ['content' => 'Jantung', 'is_correct' => true],
                            ['content' => 'Hati', 'is_correct' => false],
                            ['content' => 'Ginjal', 'is_correct' => false],
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Kuis Kimia - Atom',
                'description' => 'Kuis tentang struktur atom',
                'duration' => 40,
                'start_time' => now(),
                'end_time' => now()->addDays(4),
                'is_active' => true,
                'max_attempts' => 1,
                'randomize_questions' => false,
                'show_result' => true,
                'passing_score' => 70,
                'questions' => [
                    [
                        'content' => 'Partikel apa yang bermuatan positif dalam atom?',
                        'question_type' => 'multiple_choice',
                        'points' => 15,
                        'options' => [
                            ['content' => 'Elektron', 'is_correct' => false],
                            ['content' => 'Proton', 'is_correct' => true],
                            ['content' => 'Neutron', 'is_correct' => false],
                            ['content' => 'Foton', 'is_correct' => false],
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Kuis Bahasa Indonesia',
                'description' => 'Kuis pemahaman teks naratif',
                'duration' => 50,
                'start_time' => now(),
                'end_time' => now()->addDays(6),
                'is_active' => true,
                'max_attempts' => 1,
                'randomize_questions' => false,
                'show_result' => true,
                'passing_score' => 75,
                'questions' => [
                    [
                        'content' => 'Apa ciri utama teks naratif?',
                        'question_type' => 'multiple_choice',
                        'points' => 25,
                        'options' => [
                            ['content' => 'Memiliki alur cerita', 'is_correct' => true],
                            ['content' => 'Berisi data statistik', 'is_correct' => false],
                            ['content' => 'Berisi argumentasi', 'is_correct' => false],
                            ['content' => 'Berisi prosedur', 'is_correct' => false],
                        ]
                    ]
                ]
            ]
        ];

        foreach ($quizzes as $quizData) {
            $subject = $subjects->random();
            $teacher = $teachers->random();
            
            $questions = $quizData['questions'];
            unset($quizData['questions']);
            
            $quiz = Quiz::create(array_merge($quizData, [
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id
            ]));

            foreach ($questions as $questionData) {
                $options = $questionData['options'];
                unset($questionData['options']);
                
                $question = Question::create(array_merge($questionData, [
                    'quiz_id' => $quiz->id
                ]));

                foreach ($options as $optionData) {
                    Option::create(array_merge($optionData, [
                        'question_id' => $question->id
                    ]));
                }
            }
        }
    }
}
