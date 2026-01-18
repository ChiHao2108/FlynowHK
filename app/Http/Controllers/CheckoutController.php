<?php
session_start();
require __DIR__ . '/../../Models/Flight.php';
require __DIR__ . '/../../Models/User.php';
require __DIR__ . '/../../Models/Booking.php';
require __DIR__ . '/../../Models/Promotion.php';
require __DIR__ . '/../../../db_connect.php';

class CheckoutController
{
    public $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        if (!isset($_SESSION['user_id'])) {
            echo "<script>alert('Bạn cần đăng nhập để đặt vé!');window.location.href='login.php';</script>";
            exit;
        }
    }

    public function getCheckoutData($flight_id)
    {
        $user_id = $_SESSION['user_id'];
        $flight = Flight::getById($this->conn, $flight_id);
        $bookedSeats = Flight::getBookedSeats($this->conn, $flight_id);
        $promotions = Promotion::all($this->conn);
        $defaultName = User::getFullname($this->conn, $user_id);

        return compact('flight', 'bookedSeats', 'promotions', 'defaultName', 'user_id');
    }

    public function handlePost($data)
    {
        $flight_id = intval($data['flight_id']);
        $user_id   = intval($data['user_id']);
        $ticketType = ($data['ticketType'] ?? 'normal') === 'premium' ? 'Cao cấp' : 'Thường';
        $adult  = intval($data['adult'] ?? 1);
        $child  = intval($data['child'] ?? 0);
        $baby   = intval($data['baby'] ?? 0);
        $people_count = $adult + $child + $baby;

        $selectedSeats = array_values(array_filter($data['selectedSeats'] ?? [], fn($s) => strlen(trim((string)$s))>0));
        $seat_numbers = implode(',', $selectedSeats);

        $flightRow = Flight::getById($this->conn, $flight_id);
        $basePrice = intval($flightRow['base_price'] ?? 0);
        $baggage_limit = intval($flightRow['baggage_limit'] ?? 0);
        $feePerKg = 50000;

        $child_limit = floor($baggage_limit*0.75);
        $baggage_extra_kg = 0;
        foreach ($data['passengers'] ?? [] as $p) {
            $type = $p['type'] ?? 'adult';
            $baggage = intval($p['baggage'] ?? 0);
            $limit = $type==='child'?$child_limit:($type==='baby'?0:$baggage_limit);
            $baggage_extra_kg += max(0, $baggage-$limit);
        }

        $mult = $ticketType==='Cao cấp'?1.5:1.0;
        $tickets_sum = $adult*$basePrice + $child*($basePrice*0.75) + $baby*($basePrice*0.5);
        $total_price = round($mult*$tickets_sum)+($baggage_extra_kg*$feePerKg);

        $bookingData = [
            'flight_id' => $flight_id,
            'user_id' => $user_id,
            'booking_code' => 'FN-'.date('Ymd-His').'-'.rand(1000,9999),
            'ticket_type' => $ticketType,
            'people_count' => $people_count,
            'baggage_extra' => $baggage_extra_kg,
            'total_price' => $total_price,
            'seat_numbers' => $seat_numbers,
            'contact_name' => $data['contactName'] ?? '',
            'contact_phone'=> $data['contactPhone'] ?? '',
            'contact_email'=> $data['contactEmail'] ?? '',
            'promo_code'=> $data['promoCode'] ?? '',
            'passengers'=> json_encode($data['passengers'] ?? [])
        ];

        if (Booking::create($this->conn, $bookingData)) {
            Booking::updateFlightSeats($this->conn, $flight_id, $people_count, $ticketType);
            echo json_encode(['success'=>true,'message'=>'Đặt vé thành công','server_total'=>$total_price,'baggage_extra_kg'=>$baggage_extra_kg]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Không thể lưu đặt vé']);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) { echo json_encode(['success'=>false,'message'=>'Dữ liệu không hợp lệ']); exit; }
    $ctrl = new CheckoutController($conn);
    $ctrl->handlePost($data);
}
