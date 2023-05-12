<?php
namespace system {
    function cifrar($plain) {
        global $_CONFIG;
        $key = hash('sha256', $_CONFIG['key'], true);
        $cifrado = openssl_encrypt($plain, "AES-256-ECB", $key);
    
        return $cifrado;
    }
    
    function descifrar($data) {
        global $_CONFIG;
        $key = hash('sha256', $_CONFIG['key'], true);
        $plain = openssl_decrypt($data, "AES-256-ECB", $key);

        return $plain;
    }

    function edad(\DateTime $nacimiento) {
        return $nacimiento->diff(new \DateTime('now'))->y;
    }

    /**
     * Envia respuesta json al cliente
     *
     * @param int [$status] El estado que se respondará al cliente
     * @param mixed [$data] Los datos a enviar en la respuesta
     * @param boolean [$exit] Si se termina la ejecución tras responder
     * @param boolean [$http] Codigo de error devuelto
     * @return void
     */
    function respond(int|bool $status=false, mixed $data=false, bool $exit=true, int $http=200) {
        http_response_code($http);
        $resp = [];
        if ($status !== false)  { $resp['status'] = $status; }
        if ($data !== false)    { $resp['data'] = $data; }
        if ( !!$resp ) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($resp);
        }
        if ($exit) { exit; }
    }

    function createSquareThumb($origImg, $size) {
        $origW = imagesx($origImg);
        $origH = imagesy($origImg);
        $ratio = $origW / $origH;

        if ($ratio > 1) { // Horizontal
            $targetH = $size;
            $targetW = $size * $ratio;
        } else { // Vertical
            $targetW = $size;
            $targetH = $size / $ratio;
        }

        $targetImg = imagecreatetruecolor($size, $size); 
        imagealphablending($targetImg, false); imagesavealpha($targetImg, true);
        imagecopyresampled($targetImg, $origImg, 0, 0, 0, 0, (int)$targetW, (int)$targetH, $origW, $origH);
        imagedestroy($origImg);
        return $targetImg;
    }

    function makeImgFromFile (&$file) {
        switch( strtoupper( pathinfo($file, PATHINFO_EXTENSION ) ) ) {
            case 'JPG': return imagecreatefromjpeg($file);
            case 'PNG': return imagecreatefrompng($file);
            case 'GIF': return imagecreatefromgif($file);
            case 'WEBP': return imagecreatefromwebp($file);
            default: return false;
        }
    }


    require(\ROOT_DIR.'/lib/PHPmailer/Exception.php');
    require(\ROOT_DIR.'/lib/PHPmailer/PHPMailer.php');
    require(\ROOT_DIR.'/lib/PHPmailer/SMTP.php');

    class mailer extends \PHPMailer\PHPMailer\PHPMailer {
        public function __construct($exceptions) {
            parent::__construct($exceptions);
            global $_CONFIG;
            $this->isHTML(true);
            $this->isSMTP(); $this->SMTPAuth = true;
            $this->SMTPSecure = static::ENCRYPTION_SMTPS;
            $this->Port       = 465;
            $this->Host       = $_CONFIG['smtpHost'];
            $this->Username   = $_CONFIG['smtpUser'];
            $this->Password   = $_CONFIG['smtpPass'];
            $this->setFrom( $_CONFIG['smtpUser'], $_CONFIG['smtpName'] );
        }
      }
}
?>