<?php
    declare(strict_types = 1);

    class Session {
        private array $messages;

        public function __construct() {
            session_set_cookie_params(0, '/', 'localhost', true, true);
            session_start();

            if (!isset($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(32));

            $this->messages = $_SESSION['messages'] ?? array();
            unset($_SESSION['messages']);
        }

        public function isLoggedIn() : bool {
            return isset($_SESSION['id']);
        }

        public function logout() : void {
            session_destroy();
        }

        public function getId() : ?int {
            return $_SESSION['id'] ?? null;
        }

        public function getName() : ?string {
            return $_SESSION['name'] ?? null;
        }

        public function isAgent() : ?bool {
            return $_SESSION['agent'] ?? null;
        }

        public function isAdmin() : ?bool {
            return $_SESSION['admin'] ?? null;
        }

        public function setId(int $id) : void {
            $_SESSION['id'] = $id;
        }

        public function setName(string $name) : void {
            $_SESSION['name'] = $name;
        }

        public function setAgent(bool $agent) : void {
            $_SESSION['agent'] = $agent;
        }

        public function setAdmin(bool $admin) : void {
            $_SESSION['admin'] = $admin;
        }

        public function getMessages() : array {
            return $this->messages;
        }

        public function addMessage(bool $success, string $text) : void {
            $_SESSION['messages'][] = array('type' => $success, 'text' => $text);
        }

        public function checkCSRF() : void {
            if (!isset($_POST['csrf']) || $_SESSION['csrf'] !== $_POST['csrf']) {
                $this->addMessage(false, 'Request does not appear to be legitimate');
                $location = $_SERVER['HTTP_REFERER'] ?? '../pages/index.php';
                header("Location: $location");
                die();
            }
        }

        public function redirect() : void {
            $location = $_SERVER['HTTP_REFERER'] ?? '../pages/index.php';
            header("Location: $location");
            die();
        }
    }
?>
