<?php
session_start();
require __DIR__ . '/../../Models/Flight.php';
require __DIR__ . '/../../Models/Airport.php';
require __DIR__ . '/../../../db_connect.php';

class CheapTicketController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function extractAirportCode($str) {
        if (preg_match('/\((\w+)\)/', $str, $matches)) {
            return $matches[1];
        }
        return $str;
    }

    public function searchFlights($from = null, $to = null, $dateGo = null, $dateReturn = null) {
        $fromCode = !empty($from) ? $this->extractAirportCode($from) : null;
        $toCode   = !empty($to) ? $this->extractAirportCode($to) : null;

        if (empty($dateGo)) {
            $dateGo = date('Y-m-d'); 
        }
        if (empty($dateReturn)) {
            $dateReturn = date('Y-m-d', strtotime('+30 days'));
        }

        return Flight::search($this->conn, $fromCode, $toCode, $dateGo, $dateReturn);
    }


    public function getAirports() {
        return Airport::all($this->conn);
    }
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: /admin/add_ticket.php');
    exit;
}

$from = $_POST['from'] ?? null;
$to = $_POST['to'] ?? null;
$date_go = $_POST['date_go'] ?? null;
$date_return = $_POST['date_return'] ?? null;

$controller = new CheapTicketController($conn);
$flights = $controller->searchFlights($from, $to, $date_go, $date_return);
$airports = $controller->getAirports();

$conn->close();
