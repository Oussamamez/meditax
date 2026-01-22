<?php
// Document upload/delete API
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$user = getCurrentUser();

// Only healthcare professionals can upload/delete their own documents
if (!isHealthcarePro()) {
    echo json_encode(['success' => false, 'error' => 'Only healthcare professionals can manage documents']);
    exit;
}

switch ($action) {
    case 'upload':
        if (!isset($_FILES['document'])) {
            echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            exit;
        }
        
        $file = $_FILES['document'];
        $category = $_POST['category'] ?? 'other';
        $year = (int)($_POST['year'] ?? date('Y'));
        $description = sanitize($_POST['description'] ?? '');
        
        // Validate category
        $allowedCategories = ['income', 'expense', 'invoice', 'tax_report', 'other'];
        if (!in_array($category, $allowedCategories)) {
            $category = 'other';
        }
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'Upload error']);
            exit;
        }
        
        if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
            echo json_encode(['success' => false, 'error' => 'File too large (max 10MB)']);
            exit;
        }
        
        if (!isAllowedFileType($file['name'])) {
            echo json_encode(['success' => false, 'error' => 'File type not allowed']);
            exit;
        }
        
        // Generate unique filename and move file
        $newFilename = generateUniqueFilename($file['name']);
        $uploadPath = __DIR__ . '/../uploads/documents/' . $newFilename;
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            echo json_encode(['success' => false, 'error' => 'Failed to save file']);
            exit;
        }
        
        // Save to database
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO documents (user_id, filename, original_name, file_type, file_size, category, year, description)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        try {
            $stmt->execute([
                $user['id'],
                $newFilename,
                basename($file['name']), // Sanitize filename
                getFileExtension($file['name']),
                $file['size'],
                $category,
                $year,
                $description
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            // Remove uploaded file if database insert fails
            if (file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            error_log("Document upload error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Failed to save document']);
        }
        break;
        
    case 'delete':
        $input = json_decode(file_get_contents('php://input'), true);
        $docId = (int)($input['id'] ?? 0);
        
        if (!$docId) {
            echo json_encode(['success' => false, 'error' => 'Document ID required']);
            exit;
        }
        
        $pdo = getDBConnection();
        
        // Get document to verify ownership and get filename
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
        $stmt->execute([$docId, $user['id']]);
        $doc = $stmt->fetch();
        
        if (!$doc) {
            echo json_encode(['success' => false, 'error' => 'Document not found']);
            exit;
        }
        
        // Delete file
        $filePath = __DIR__ . '/../uploads/documents/' . $doc['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
        $stmt->execute([$docId]);
        
        echo json_encode(['success' => true]);
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
