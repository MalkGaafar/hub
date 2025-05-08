<?php


class ReportController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    private function generatePDF($title, $stats, $tableHeaders, $tableData, $filename, $subtitle = '') {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetRightMargin(10);
        $pdf->SetLeftMargin(10);

        // Title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(190, 10, $title, 0, 1, 'C');
        $pdf->Ln(10);

        // Subtitle
        if ($subtitle) {
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(190, 10, $subtitle, 0, 1);
            $pdf->Ln(10);
        }

        // Stats
        if ($stats) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(190, 10, 'إحصائيات', 0, 1);
            $pdf->SetFont('Arial', '', 12);
            foreach ($stats as $stat) {
                $pdf->Cell(100, 10, $stat, 0, 1);
            }
            $pdf->Ln(10);
        }

        // Table
        if ($tableHeaders && $tableData) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(190, 10, 'تفاصيل', 0, 1);
            $pdf->SetFont('Arial', 'B', 10);
            foreach ($tableHeaders as $header) {
                $pdf->Cell($header['width'], 10, $header['label'], 1);
            }
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 10);
            foreach ($tableData as $row) {
                foreach ($row as $col => $val) {
                    $pdf->Cell($tableHeaders[$col]['width'], 10, $val, 1);
                }
                $pdf->Ln();
            }
        }

        $pdf->Output('F', 'reports/' . $filename);
        return $filename;
    }

    public function generateUserReport() {
        $statsQuery = "SELECT 
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as new_users_week,
            (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_users_month,
            (SELECT AVG(reputation) FROM users) as avg_reputation";
        $stats = $this->db->select($statsQuery)[0];
        $statsText = [
            "إجمالي المستخدمين: {$stats['total_users']}",
            "مستخدمين جدد (أسبوع): {$stats['new_users_week']}",
            "مستخدمين جدد (شهر): {$stats['new_users_month']}",
            "متوسط السمعة: " . round($stats['avg_reputation'], 2)
        ];

        $topUsersQuery = "SELECT username, email, reputation, created_at FROM users ORDER BY reputation DESC LIMIT 10";
        $topUsers = $this->db->select($topUsersQuery);
        $tableHeaders = [
            ['label' => 'اسم المستخدم', 'width' => 50],
            ['label' => 'البريد الإلكتروني', 'width' => 90],
            ['label' => 'السمعة', 'width' => 25],
            ['label' => 'تاريخ التسجيل', 'width' => 25]
        ];
        $tableData = array_map(function($user) {
            return [
                $user['username'],
                $user['email'],
                $user['reputation'],
                date('Y-m-d', strtotime($user['created_at']))
            ];
        }, $topUsers);

        return $this->generatePDF('تقرير المستخدمين', $statsText, $tableHeaders, $tableData, 'user_report_' . date('Y-m-d') . '.pdf');
    }

    public function generateContentReport() {
        $statsQuery = "SELECT 
            (SELECT COUNT(*) FROM questions) as total_questions,
            (SELECT COUNT(*) FROM questions WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as new_questions_week,
            (SELECT COUNT(*) FROM answers) as total_answers,
            (SELECT COUNT(*) FROM answers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as new_answers_week,
            (SELECT COUNT(*) FROM tags) as total_tags";
        $stats = $this->db->select($statsQuery)[0];
        $statsText = [
            "إجمالي الأسئلة: {$stats['total_questions']}",
            "أسئلة جديدة (أسبوع): {$stats['new_questions_week']}",
            "إجمالي الإجابات: {$stats['total_answers']}",
            "إجابات جديدة (أسبوع): {$stats['new_answers_week']}",
            "إجمالي التصنيفات: {$stats['total_tags']}"
        ];

        $topQuestionsQuery = "SELECT title, username, view_count FROM questions q JOIN users u ON q.user_id = u.id ORDER BY q.view_count DESC LIMIT 10";
        $topQuestions = $this->db->select($topQuestionsQuery);
        $tableHeaders = [
            ['label' => 'عنوان السؤال', 'width' => 100],
            ['label' => 'الكاتب', 'width' => 45],
            ['label' => 'عدد المشاهدات', 'width' => 45]
        ];
        $tableData = array_map(function($q) {
            return [$q['title'], $q['username'], $q['view_count']];
        }, $topQuestions);

        return $this->generatePDF('تقرير المحتوى', $statsText, $tableHeaders, $tableData, 'content_report_' . date('Y-m-d') . '.pdf');
    }

    public function generateActivityReport($days = 30) {
        $questionsQuery = "SELECT DATE(created_at) as date, COUNT(*) as count FROM questions 
                           WHERE created_at >= DATE_SUB(NOW(), INTERVAL $days DAY) GROUP BY DATE(created_at)";
        $answersQuery = "SELECT DATE(created_at) as date, COUNT(*) as count FROM answers 
                         WHERE created_at >= DATE_SUB(NOW(), INTERVAL $days DAY) GROUP BY DATE(created_at)";
        $questions = $this->db->select($questionsQuery);
        $answers = $this->db->select($answersQuery);

        $statsText = ["النشاط خلال الـ {$days} يوم الماضية"];
        $tableHeaders = [
            ['label' => 'التاريخ', 'width' => 95],
            ['label' => 'عدد النشاط', 'width' => 95]
        ];
        $questionsTable = array_map(function($q) {
            return [$q['date'], $q['count']];
        }, $questions);
        $answersTable = array_map(function($a) {
            return [$a['date'], $a['count']];
        }, $answers);

        $filename = 'activity_report_' . date('Y-m-d') . '.pdf';
        $this->generatePDF('تقرير النشاط - أسئلة', $statsText, $tableHeaders, $questionsTable, $filename);
        return $this->generatePDF('تقرير النشاط - إجابات', [], $tableHeaders, $answersTable, $filename);
    }
}
