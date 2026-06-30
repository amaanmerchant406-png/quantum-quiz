<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelController extends Controller
{
    /**
     * Downloads the standard Excel template for bulk question imports.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Quiz Template');

        // Headers
        $headers = [
            'A1' => 'Question Text',
            'B1' => 'Option A',
            'C1' => 'Option B',
            'D1' => 'Option C',
            'E1' => 'Option D',
            'F1' => 'Correct Option (A, B, C, or D)',
            'G1' => 'Points (e.g. 10)'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Add some styling to headers to make it look premium
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Segoe UI'
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '6200EE'] // Premium violet color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        $sheet->getRowDimension('1')->setRowHeight(28);

        // Add an example row
        $sheet->setCellValue('A2', 'What is the speed of light in a vacuum?');
        $sheet->setCellValue('B2', '299,792 km/s');
        $sheet->setCellValue('C2', '150,000 km/s');
        $sheet->setCellValue('D2', '3,000 km/s');
        $sheet->setCellValue('E2', '300,000 km/s');
        $sheet->setCellValue('F2', 'A');
        $sheet->setCellValue('G2', '15');

        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output to response as Excel download
        $fileName = 'QuantumQuiz_Question_Template.xlsx';
        
        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Imports questions in bulk to a specific quiz.
     */
    public function import(Request $request, Quiz $quiz)
    {
        if ($quiz->creator_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:4096'
        ]);

        $file = $request->file('excel_file');
        
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
            
            // Validate headers
            $headers = array_map('trim', array_map('strtolower', array_values($rows[1])));
            
            // Expected headers
            // $headers[0] should contain question, $headers[5] correct, etc.
            if (count($rows) < 2) {
                return back()->with('error', 'The spreadsheet is empty.');
            }

            $importedCount = 0;

            // Start loop from second row
            for ($i = 2; $i <= count($rows); $i++) {
                $row = $rows[$i];

                $questionText = trim($row['A'] ?? '');
                $optA = trim($row['B'] ?? '');
                $optB = trim($row['C'] ?? '');
                $optC = trim($row['D'] ?? '');
                $optD = trim($row['E'] ?? '');
                $correctOptionLetter = strtoupper(trim($row['F'] ?? ''));
                $points = intval(trim($row['G'] ?? '10'));

                if (empty($questionText) || empty($optA) || empty($optB)) {
                    // Skip incomplete rows
                    continue;
                }

                if ($points <= 0) {
                    $points = 10;
                }

                // Create Question
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionText,
                    'points' => $points,
                ]);

                // Create Options
                $optionsData = [
                    'A' => $optA,
                    'B' => $optB,
                    'C' => $optC,
                    'D' => $optD
                ];

                foreach ($optionsData as $letter => $text) {
                    if (empty($text)) {
                        continue;
                    }

                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $text,
                        'is_correct' => ($correctOptionLetter === $letter)
                    ]);
                }

                $importedCount++;
            }

            return redirect()->route('creator.edit', $quiz->id)->with('success', "Successfully imported {$importedCount} questions from Excel!");

        } catch (\Exception $e) {
            return back()->with('error', 'Error parsing file: ' . $e->getMessage());
        }
    }

    /**
     * Exports attempt analytics and leaderboard for a specific quiz.
     */
    public function exportResults(Quiz $quiz)
    {
        if ($quiz->creator_id !== Auth::id()) {
            abort(403);
        }

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->with('user')
            ->orderByDesc('score')
            ->orderBy('time_taken')
            ->get();

        $spreadsheet = new Spreadsheet();
        
        // 1. Leaderboard Sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leaderboard');

        // Header Title Block
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', "QuantumQuiz - Performance Leaderboard: {$quiz->title}");
        
        $titleStyle = [
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Segoe UI'
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '102A43'] // Dark navy
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($titleStyle);
        $sheet->getRowDimension('1')->setRowHeight(35);

        // Column Headers
        $headers = [
            'A2' => 'Rank',
            'B2' => 'User Name',
            'C2' => 'Email',
            'D2' => 'Score',
            'E2' => 'Accuracy (Correct / Total)',
            'F2' => 'Time Taken (seconds)'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Segoe UI'
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '486581'] // Lighter slate
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '102A43']
                ]
            ]
        ];
        $sheet->getStyle('A2:F2')->applyFromArray($headerStyle);
        $sheet->getRowDimension('2')->setRowHeight(24);

        // Populate Data
        $rowNum = 3;
        foreach ($attempts as $index => $attempt) {
            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $attempt->user->name);
            $sheet->setCellValue('C' . $rowNum, $attempt->user->email);
            $sheet->setCellValue('D' . $rowNum, $attempt->score);
            $sheet->setCellValue('E' . $rowNum, "{$attempt->correct_answers} / {$attempt->total_questions}");
            $sheet->setCellValue('F' . $rowNum, $attempt->time_taken);

            // Row zebra striping
            if ($rowNum % 2 == 1) {
                $sheet->getStyle("A{$rowNum}:F{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F0F4F8');
            }

            // Alignments
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $rowNum++;
        }

        // Summary row (Average Score)
        if ($attempts->count() > 0) {
            $sheet->setCellValue('C' . $rowNum, 'Average:');
            $sheet->setCellValue('D' . $rowNum, "=AVERAGE(D3:D" . ($rowNum - 1) . ")");
            $sheet->setCellValue('F' . $rowNum, "=AVERAGE(F3:F" . ($rowNum - 1) . ")");

            $summaryStyle = [
                'font' => [
                    'bold' => true,
                    'name' => 'Segoe UI'
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];
            $sheet->getStyle("C{$rowNum}:F{$rowNum}")->applyFromArray($summaryStyle);
            $sheet->getStyle("D{$rowNum}")->getNumberFormat()->setFormatCode('0.0');
            $sheet->getStyle("F{$rowNum}")->getNumberFormat()->setFormatCode('0.0');
        }

        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'QuantumQuiz_Leaderboard_' . str_replace(' ', '_', $quiz->title) . '.xlsx';
        
        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
