<?php
require_once __DIR__ . '/../../Models/User.php';

class ProfileController {
    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getProfile($user_id){
        return User::getProfile($this->conn, $user_id);
    }

    public function updateProfile($user_id, $postData, $fileData){

        if (!isset($postData['fullname']) || trim($postData['fullname']) === '') {
            return false;
        }

        $avatar = $postData['current_avatar'] ?? '';

        if (isset($fileData['avatar']) && $fileData['avatar']['error'] === 0) {
            $allowed = ['jpg','jpeg','png'];
            $ext = strtolower(pathinfo($fileData['avatar']['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowed) && $fileData['avatar']['size'] <= 2*1024*1024) {
                $uploadDir = __DIR__ . '/../../../public/uploads';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $newName = $uploadDir . "/avatar_{$user_id}.".$ext;
                if (move_uploaded_file($fileData['avatar']['tmp_name'], $newName)) {
                    $avatar = 'uploads/avatar_'.$user_id.'.'.$ext;
                }
            }
        }

        $data = [
            'fullname'  => trim($postData['fullname']),
            'birthdate' => $postData['birthdate'] ?: null,
            'address'   => trim($postData['address'] ?? ''),
            'phone'     => trim($postData['phone'] ?? ''),
            'avatar'    => $avatar
        ];

        return User::updateProfile($this->conn, $user_id, $data);
    }

}
