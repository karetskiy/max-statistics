<?php
// save.php - скрипт для сохранения данных в Excel файл
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обработка предварительных запросов CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Получаем данные из POST-запроса
$input = json_decode(file_get_contents('php://input'), true);

// Тестовый режим
if (isset($input['action']) && $input['action'] === 'test') {
    echo json_encode(['success' => true, 'message' => 'Сервер работает']);
    exit();
}

// Проверяем наличие данных
if (!$input || !isset($input['action']) || $input['action'] !== 'save_to_excel') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Неверный формат данных']);
    exit();
}

$filename = 'base.xlsx';
$data = $input['data'];

// Функция для создания Excel файла
function createExcelFile($filename, $data) {
    // Если файл не существует, создаем новый с заголовками
    if (!file_exists($filename)) {
        $headers = ['Дата', 'Филиал', 'Работа по формированию', 'Загрузили документы', 
                   'Создали ID и загрузили', 'Считывания QR'];
        $content = implode("\t", $headers) . "\n";
        file_put_contents($filename, $content);
    }
    
    // Добавляем новые данные
    $lines = [];
    foreach ($data as $row) {
        $lines[] = implode("\t", [
            $row['date'],
            $row['branch'],
            $row['workFormation'],
            $row['uploadedDocs'],
            $row['createdIdAndUploaded'],
            $row['qrScans']
        ]);
    }
    
    file_put_contents($filename, implode("\n", $lines) . "\n", FILE_APPEND);
    return true;
}

// Сохраняем данные
try {
    createExcelFile($filename, $data);
    echo json_encode(['success' => true, 'message' => 'Данные добавлены в base.xlsx']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>