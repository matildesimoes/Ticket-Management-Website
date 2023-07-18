<?php
    declare(strict_types = 1);

    class Status {
        public int $id;
        public string $name;

        public function __construct(int $id, string $name) {
            $this->id = $id;
            $this->name = $name;
        }

        public function __toString() {
            return $this->name;
        }

        public function getId() : int {
            return $this->id;
        }

        public function getName() : string {
            return $this->name;
        }

        public static function getStatuses(PDO $db) : array {
            $stmt = $db->prepare('
                SELECT idStatus, name
                FROM Status
                ORDER BY 1
            ');

            $stmt->execute();
            $result = $stmt->fetchAll();

            $statuses = array();

            foreach ($result as $row)
                $statuses[] = new Status(
                    (int) $row['idStatus'],
                    $row['name']
                );

            return $statuses;
        }
        
        public static function getStatus(PDO $db, int $id) : ?Status {
            $stmt = $db->prepare('
                SELECT idStatus, name
                FROM Status
                WHERE idStatus = ?
            ');

            $stmt->execute(array($id));
            $status = $stmt->fetch();

            if (!$status) return null;

            return new Status(
                (int) $status['idStatus'],
                $status['name']
            );
        }

        public static function getStatusByName(PDO $db, string $name) : ?Status {
            $stmt = $db->prepare('
                SELECT idStatus, name
                FROM Status
                WHERE name = ?
            ');

            $stmt->execute(array($name));
            $status = $stmt->fetch();

            if (!$status) return null;

            return new Status(
                (int) $status['idStatus'],
                $status['name']
            );
        }

        public static function addStatus(PDO $db, string $name) : bool {
            $stmt = $db->prepare('
                INSERT INTO Status (name)
                VALUES (?)
            ');

            try {
                $stmt->execute(array($name));
            } catch (PDOException $e) {
                return false;
            }
            
            return true;
        }

        public function delete(PDO $db) : bool {
            $stmt = $db->prepare('
                DELETE
                FROM Status
                WHERE idStatus = ?
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
