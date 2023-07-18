<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../database/class_user.php');

    class Message {
        public int $id;
        public string $date;
        public string $content;
        public User $author;

        public function __construct(int $id, string $date, string $content, User $author) {
            $this->id = $id;
            $this->date = $date;
            $this->content = $content;
            $this->author = $author;
        }

        public function getId() : int {
            return $this->id;
        }

        public function getDate() : string {
            return $this->date;
        }

        public function getContent() : string {
            return $this->content;
        }

        public function getAuthor() : User {
            return $this->author;
        }

        public static function getMessage(PDO $db, int $id) : ?Message {
            $stmt = $db->prepare('
                SELECT idMessage, date, content, idUser
                FROM Message
                WHERE idMessage = ?
            ');

            $stmt->execute(array($id));
            $message = $stmt->fetch();

            if (!$message) return null;

            return new Message(
                (int) $message['idMessage'],
                $message['date'],
                $message['content'],
                User::getUser($db, (int) $message['idUser'])
            );
        }

        public function delete(PDO $db) : bool {
            $stmt = $db->prepare('
                DELETE
                FROM Message
                WHERE idMessage = ?
            ');

            try {
                $stmt->execute(array($this->id));
            } catch (PDOException $e) {
                return false;
            }

            return true;
        }
    }
?>
