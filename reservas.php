<?php
header('Content-Type: application/json');
$dataFile = 'data.json';

function readData() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        file_put_contents($dataFile, json_encode(['reservas' => []]));
    }
    return json_decode(file_get_contents($dataFile), true);
}

function saveData($data) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}

$data = readData();

switch ($_GET['action']) {
    case 'read':
        echo json_encode($data['reservas']);
        break;

    case 'create':
        $input = json_decode(file_get_contents('php://input'), true);
        $input['id'] = time();
        $data['reservas'][] = $input;
        saveData($data);
        echo json_encode(['success' => true]);
        break;

    case 'update':
        $input = json_decode(file_get_contents('php://input'), true);
        foreach ($data['reservas'] as &$reserva) {
            if ($reserva['id'] == $input['id']) {
                $reserva = $input;
                break;
            }
        }
        saveData($data);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = $_GET['id'];
        $data['reservas'] = array_filter($data['reservas'], fn($reserva) => $reserva['id'] != $id);
        saveData($data);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
