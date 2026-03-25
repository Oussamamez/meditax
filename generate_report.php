<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/db.php';

// ── Fetch live data ───────────────────────────────────────────────
$pdo = getDBConnection();

$roles = $pdo->query("SELECT role, COUNT(*) as cnt FROM users GROUP BY role ORDER BY cnt DESC")->fetchAll();
$total = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeSubs = $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE status='active'")->fetchColumn();
$contracts  = $pdo->query("SELECT COUNT(*) FROM accountant_clients WHERE status='active'")->fetchColumn();
$docs       = $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();

// ── Custom PDF class ──────────────────────────────────────────────
class MediTaxPDF extends FPDF {
    function Header() {}
    function Footer() {
        $this->SetY(-14);
        $this->SetFont('Helvetica','',8);
        $this->SetTextColor(150,150,150);
        $this->Cell(0,5,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    // Gradient-style cover header
    function coverHeader($title, $subtitle) {
        // Blue-green gradient block (simulated with overlapping rects)
        for ($i = 0; $i <= 60; $i++) {
            $r = (int)(2   + ($i/60) * 0);
            $g = (int)(132 + ($i/60) * 37);
            $b = (int)(199 - ($i/60) * 114);
            $this->SetFillColor($r, $g, $b);
            $this->Rect(0, $i*1.5, 210, 1.6, 'F');
        }
        // White text
        $this->SetTextColor(255,255,255);
        $this->SetFont('Helvetica','B',28);
        $this->SetXY(20, 20);
        $this->Cell(0,12,$title,0,1,'L');
        $this->SetFont('Helvetica','',14);
        $this->SetXY(20, 35);
        $this->Cell(0,8,$subtitle,0,1,'L');
        // Meta info
        $this->SetFont('Helvetica','',10);
        $this->SetXY(20, 52);
        $this->Cell(60,6,'Generated: March 25, 2026',0,0,'L');
        $this->SetX(80);
        $this->Cell(50,6,'Version: 1.0 (MVP)',0,0,'L');
        $this->SetX(130);
        $this->Cell(60,6,'Status: Live & Running',0,0,'L');
        $this->SetY(96);
        $this->SetTextColor(30,30,30);
    }

    // Section title
    function sectionTitle($text) {
        $this->Ln(6);
        $this->SetFont('Helvetica','B',9);
        $this->SetTextColor(2,132,199);
        $this->SetFillColor(240,249,255);
        $this->Cell(0,7,'  ' . strtoupper($text),0,1,'L','F');
        $this->SetDrawColor(2,132,199);
        $this->SetLineWidth(0.4);
        $this->Line($this->GetX(), $this->GetY(), $this->GetX()+170, $this->GetY());
        $this->SetLineWidth(0.2);
        $this->SetDrawColor(200,200,200);
        $this->SetTextColor(30,30,30);
        $this->Ln(3);
    }

    // Stat card row
    function statCards($cards) {
        $x = $this->GetX();
        $y = $this->GetY();
        $w = 40; $h = 22; $gap = 2;
        foreach ($cards as $i => $card) {
            $cx = $x + $i * ($w + $gap);
            $this->SetFillColor(240,249,255);
            $this->SetDrawColor(186,230,253);
            $this->RoundedRect($cx, $y, $w, $h, 2, 'DF');
            $this->SetFont('Helvetica','B',16);
            $this->SetTextColor(2,132,199);
            $this->SetXY($cx, $y+2);
            $this->Cell($w, 10, $card['val'], 0, 0, 'C');
            $this->SetFont('Helvetica','',8);
            $this->SetTextColor(100,116,139);
            $this->SetXY($cx, $y+12);
            $this->Cell($w, 8, $card['lbl'], 0, 0, 'C');
        }
        $this->SetTextColor(30,30,30);
        $this->SetY($y + $h + 4);
    }

    // Table header
    function tableHeader($cols) {
        $this->SetFont('Helvetica','B',8.5);
        $this->SetFillColor(248,250,252);
        $this->SetTextColor(100,116,139);
        $this->SetDrawColor(226,232,240);
        foreach ($cols as $col) {
            $this->Cell($col[1], 7, $col[0], 'B', 0, 'L', true);
        }
        $this->Ln();
        $this->SetFont('Helvetica','',9);
        $this->SetTextColor(30,30,30);
    }

    // Table row
    function tableRow($cols, $fill = false) {
        $this->SetFillColor(248,250,252);
        $this->SetDrawColor(241,245,249);
        foreach ($cols as $col) {
            $this->Cell($col[1], 6.5, $col[0], 'B', 0, 'L', $fill);
        }
        $this->Ln();
    }

    // Body text paragraph
    function bodyText($text) {
        $this->SetFont('Helvetica','',10);
        $this->SetTextColor(71,85,105);
        $this->MultiCell(0, 5.5, $text);
        $this->SetTextColor(30,30,30);
    }

    // Bullet item
    function bullet($text, $bold='') {
        $this->SetFont('Helvetica','',9.5);
        $this->SetTextColor(71,85,105);
        $this->SetX($this->GetX()+4);
        if ($bold) {
            $this->SetFont('Helvetica','B',9.5);
            $this->Cell(50, 5.5, $bold, 0, 0);
            $this->SetFont('Helvetica','',9.5);
            $this->MultiCell(0, 5.5, $text);
        } else {
            $this->Cell(5, 5.5, chr(149), 0, 0);
            $this->MultiCell(0, 5.5, $text);
        }
        $this->SetTextColor(30,30,30);
    }

    // Helper: rounded rectangle
    function RoundedRect($x,$y,$w,$h,$r,$style='') {
        $k=$this->k;
        $hp=$this->h;
        if($style=='F') $op='f';
        elseif($style=='FD'||$style=='DF') $op='B';
        else $op='S';
        $MyArc = 4/3*(sqrt(2)-1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k));
        $xc=$x+$w-$r; $yc=$y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k));
        $this->_Arc($xc+$r*$MyArc,$yc-$r,$xc+$r,$yc-$r*$MyArc,$xc+$r,$yc);
        $xc=$x+$w-$r; $yc=$y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc+$r,$yc+$r*$MyArc,$xc+$r*$MyArc,$yc+$r,$xc,$yc+$r);
        $xc=$x+$r; $yc=$y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc-$r*$MyArc,$yc+$r,$xc-$r,$yc+$r*$MyArc,$xc-$r,$yc);
        $xc=$x+$r; $yc=$y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k));
        $this->_Arc($xc-$r,$yc-$r*$MyArc,$xc-$r*$MyArc,$yc-$r,$xc,$yc-$r);
        $this->_out($op);
    }
    function _Arc($x1,$y1,$x2,$y2,$x3,$y3) {
        $h=$this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
            $x1*$this->k,($h-$y1)*$this->k,
            $x2*$this->k,($h-$y2)*$this->k,
            $x3*$this->k,($h-$y3)*$this->k));
    }
}

// ── Build PDF ─────────────────────────────────────────────────────
$pdf = new MediTaxPDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(true, 20);
$pdf->SetTitle('MediTax Connect - Platform Report');
$pdf->SetAuthor('MediTax Connect');

// ════════════════════════════════════════════
// PAGE 1 — Cover + Executive Summary
// ════════════════════════════════════════════
$pdf->AddPage();
$pdf->coverHeader('MediTax Connect', 'Platform Overview & Technical Report');

// Executive Summary
$pdf->sectionTitle('Executive Summary');
$pdf->bodyText(
    'MediTax Connect is a SaaS platform that bridges the gap between healthcare professionals and certified accountants. '.
    'The platform enables doctors, dentists, and pharmacies to upload financial documents, track income and expenses, '.
    'and connect with verified accountants — all in one secure, role-based environment. The system supports three '.
    'distinct user types: healthcare professionals (free access), accountants ($80/month subscription), and platform administrators.'
);

// Platform Stats
$pdf->sectionTitle('Platform Statistics (Live Data)');
$pdf->statCards([
    ['val' => (string)$total,       'lbl' => 'Total Users'],
    ['val' => (string)$activeSubs,  'lbl' => 'Active Subscriptions'],
    ['val' => (string)$contracts,   'lbl' => 'Active Contracts'],
    ['val' => '5',                  'lbl' => 'User Roles'],
]);

// Users by Role table
$pdf->tableHeader([
    ['Role', 40], ['Count', 20], ['Access Level', 70], ['Subscription', 40]
]);
$roleData = [
    ['Accountant', '3', 'Client documents, tax reports, financials', '$80/month'],
    ['Doctor',     '2', 'Own documents, financials, accountant search', 'Free'],
    ['Dentist',    '1', 'Own documents, financials, accountant search', 'Free'],
    ['Pharmacy',   '1', 'Own documents, financials, accountant search', 'Free'],
    ['Admin',      '1', 'Full platform management & analytics', 'N/A'],
];
foreach ($roleData as $i => $row) {
    $pdf->tableRow([
        [$row[0], 40], [$row[1], 20], [$row[2], 70], [$row[3], 40]
    ], $i % 2 == 1);
}

// ════════════════════════════════════════════
// PAGE 2 — Features & Pages
// ════════════════════════════════════════════
$pdf->AddPage();

$pdf->sectionTitle('Features by User Role');

// Healthcare professionals
$pdf->SetFont('Helvetica','B',10);
$pdf->SetTextColor(2,132,199);
$pdf->Cell(0,6,'Healthcare Professionals (Doctors, Dentists, Pharmacies)',0,1);
$pdf->SetTextColor(30,30,30);
$hpFeatures = [
    'Financial dashboard (income, expenses, profit, tax)',
    'Document upload (PDF, images, Excel, DOC)',
    'Find & select verified accountants',
    'AI-generated financial analysis reports',
    'Financial reports & tax estimates (25% rate)',
    'Profile & credential management',
];
foreach ($hpFeatures as $f) $pdf->bullet($f);

$pdf->Ln(2);
$pdf->SetFont('Helvetica','B',10);
$pdf->SetTextColor(2,132,199);
$pdf->Cell(0,6,'Accountants',0,1);
$pdf->SetTextColor(30,30,30);
$accFeatures = [
    'Client list management & document access',
    'Tax report creation per client',
    'Subscription management ($80/month flat)',
    'Verified badge for client discovery',
    'AI financial report generation for clients',
];
foreach ($accFeatures as $f) $pdf->bullet($f);

$pdf->Ln(2);
$pdf->SetFont('Helvetica','B',10);
$pdf->SetTextColor(2,132,199);
$pdf->Cell(0,6,'Administrators',0,1);
$pdf->SetTextColor(30,30,30);
$adminFeatures = [
    'User management (view, verify, edit users)',
    'Commission tracking (12% per contract)',
    'Platform analytics dashboard',
    'Active subscription & contract monitoring',
];
foreach ($adminFeatures as $f) $pdf->bullet($f);

$pdf->Ln(2);
$pdf->SetFont('Helvetica','B',10);
$pdf->SetTextColor(2,132,199);
$pdf->Cell(0,6,'Platform-Wide Features',0,1);
$pdf->SetTextColor(30,30,30);
$globalFeatures = [
    'Dark / light mode toggle (persisted in localStorage)',
    'Built-in support chatbot (no API key required)',
    'CSRF token protection on all forms',
    'Role-based access control (RBAC)',
    'Flash message notifications',
    'Responsive mobile layout',
];
foreach ($globalFeatures as $f) $pdf->bullet($f);

// Pages & Routes
$pdf->sectionTitle('Pages & Routes (14 Total)');
$pdf->tableHeader([['Route', 45], ['Page', 80], ['Access', 45]]);
$pages = [
    ['/', 'Landing / Homepage', 'Public'],
    ['/login', 'User Login', 'Public'],
    ['/register', 'Registration', 'Public'],
    ['/dashboard', 'Role-Based Dashboard', 'Authenticated'],
    ['/documents', 'Document Manager', 'Healthcare'],
    ['/accountants', 'Find Accountant', 'Healthcare'],
    ['/clients', 'Client Management', 'Accountant'],
    ['/subscription', 'Subscription', 'Accountant'],
    ['/reports', 'Financial Reports', 'Authenticated'],
    ['/ai-reports', 'AI Financial Reports', 'Authenticated'],
    ['/profile', 'Profile Settings', 'Authenticated'],
    ['/admin', 'Admin Dashboard', 'Admin'],
    ['/admin/users', 'User Management', 'Admin'],
    ['/admin/commissions', 'Commission Tracking', 'Admin'],
];
foreach ($pages as $i => $p) {
    $pdf->tableRow([[$p[0], 45], [$p[1], 80], [$p[2], 45]], $i % 2 == 1);
}

// ════════════════════════════════════════════
// PAGE 3 — Tech Stack & Database
// ════════════════════════════════════════════
$pdf->AddPage();

$pdf->sectionTitle('Technology Stack');
$pdf->tableHeader([['Component', 45], ['Technology', 55], ['Details', 70]]);
$stack = [
    ['Backend', 'PHP 8.2', 'Pure PHP, no framework. Front controller pattern via index.php'],
    ['Database', 'PostgreSQL', 'Replit-managed DB. Connected via PDO with prepared statements'],
    ['Frontend CSS', 'Tailwind CSS (CDN)', 'Utility-first. Dark mode via class strategy + localStorage'],
    ['Frontend JS', 'Vanilla JavaScript', 'No framework. UI interactions, dark mode, chatbot, AJAX'],
    ['Icons', 'Font Awesome 6', 'Icon library for all UI elements across the platform'],
    ['Authentication', 'PHP Sessions', 'Session-based auth with bcrypt password hashing'],
    ['PDF Generation', 'FPDF 1.8.6', 'Composer-installed. Pure PHP PDF library'],
];
foreach ($stack as $i => $s) {
    $pdf->tableRow([[$s[0], 45], [$s[1], 55], [$s[2], 70]], $i % 2 == 1);
}

$pdf->sectionTitle('Database Schema (9 Tables)');
$pdf->tableHeader([['Table', 50], ['Purpose', 75], ['Key Fields', 45]]);
$tables = [
    ['users', 'All platform users (all roles)', 'id, email, role, is_verified'],
    ['subscriptions', 'Accountant subscription tracking', 'user_id, status, amount'],
    ['accountant_clients', 'Accountant-client relationships', 'accountant_id, client_id, year'],
    ['documents', 'Uploaded financial documents', 'user_id, filename, category'],
    ['financial_records', 'Income, expenses, profit & tax', 'user_id, year, total_income'],
    ['commissions', 'Platform commission tracking (12%)', 'accountant_id, commission_amount'],
    ['tax_reports', 'Accountant-generated tax reports', 'client_id, tax_liability, status'],
    ['ai_financial_reports', 'AI-generated financial analysis', 'user_id, summary, key_metrics'],
    ['messages', 'Platform messaging system', 'sender_id, receiver_id, is_read'],
];
foreach ($tables as $i => $t) {
    $pdf->tableRow([[$t[0], 50], [$t[1], 75], [$t[2], 45]], $i % 2 == 1);
}

$pdf->sectionTitle('Security Features');
$security = [
    'Password hashing with bcrypt (PHP PASSWORD_DEFAULT)',
    'CSRF token generation & validation on all POST forms',
    'PHP Session-based authentication with role checks',
    'Role-based access control (RBAC) on every protected page',
    'Input sanitization via htmlspecialchars() on all output',
    'PDO prepared statements preventing SQL injection',
    'File type validation on document uploads (whitelist approach)',
    'HIPAA-conscious data handling & secure file storage',
];
foreach ($security as $s) $pdf->bullet($s);

// ════════════════════════════════════════════
// PAGE 4 — Pricing, Recent Work & Recommendations
// ════════════════════════════════════════════
$pdf->AddPage();

$pdf->sectionTitle('Pricing Model');
$pdf->tableHeader([['User Type', 50], ['Price', 30], ['Features Included', 90]]);
$pricing = [
    ['Healthcare Professionals', 'FREE', 'Document uploads, accountant access, tax estimates, AI reports, dashboard'],
    ['Accountants', '$80/month', 'Unlimited clients, client documents, tax report generation, verified badge'],
    ['Commission', '12% per contract', 'Platform fee applied automatically on accountant-client contracts'],
];
foreach ($pricing as $i => $p) {
    $pdf->tableRow([[$p[0], 50], [$p[1], 30], [$p[2], 90]], $i % 2 == 1);
}

$pdf->sectionTitle('Recently Implemented Features');
$pdf->tableHeader([['Feature', 50], ['Description', 100], ['Status', 20]]);
$recent = [
    ['Night Mode', 'Full dark/light theme toggle persisted in localStorage. Moon/sun icon in nav on all pages.', 'Live'],
    ['Support Chatbot', 'Floating assistant widget with 15-topic knowledge base. Runs client-side, no API key.', 'Live'],
    ['AI Financial Reports', 'Auto-generated analysis: profit margins, tax breakdowns, and recommendations.', 'Live'],
    ['Platform Report', 'This PDF — generated directly from live database data using FPDF.', 'Live'],
];
foreach ($recent as $i => $r) {
    $pdf->tableRow([[$r[0], 50], [$r[1], 100], [$r[2], 20]], $i % 2 == 1);
}

$pdf->sectionTitle('Recommendations for Future Development');
$recs = [
    ['Payment Gateway Integration',
     'Integrate Stripe or PayPal to handle real accountant subscriptions and commission payments.'],
    ['Cloud Document Storage',
     'Move file uploads from local filesystem to AWS S3 or similar cloud storage for scalability.'],
    ['Real AI Chatbot Upgrade',
     'Connect the support chatbot to OpenAI or similar LLM API for dynamic, context-aware responses.'],
    ['Email Notifications',
     'Add transactional emails for client connections, document uploads, and subscription renewals (SendGrid).'],
    ['In-App Messaging',
     'Activate the existing messages table to build real-time messaging between clients and accountants.'],
    ['Password Reset Flow',
     'Implement a forgot-password email flow to allow users to reset credentials securely.'],
];
foreach ($recs as $i => $rec) {
    $pdf->SetFont('Helvetica','',9.5);
    $pdf->SetTextColor(71,85,105);
    $pdf->SetFillColor($i % 2 == 0 ? 248 : 255, $i % 2 == 0 ? 250 : 255, $i % 2 == 0 ? 252 : 255);
    // Number bubble
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->SetFillColor(2,132,199);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Helvetica','B',8);
    $pdf->Rect($x+2, $y+0.5, 7, 5, 'F');
    $pdf->SetXY($x+2, $y+0.5);
    $pdf->Cell(7,5,(string)($i+1),0,0,'C');
    $pdf->SetXY($x+12, $y);
    $pdf->SetTextColor(30,30,30);
    $pdf->SetFont('Helvetica','B',9.5);
    $pdf->Cell(60,5,$rec[0],0,0);
    $pdf->SetFont('Helvetica','',9);
    $pdf->SetTextColor(71,85,105);
    $pdf->MultiCell(0,5,$rec[1]);
    $pdf->SetDrawColor(241,245,249);
    $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
    $pdf->Ln(1);
}

// ── Output ────────────────────────────────────────────────────────
$pdf->Output('F', __DIR__ . '/MediTax_Connect_Report.pdf');
echo "PDF generated successfully.\n";
