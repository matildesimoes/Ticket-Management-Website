<?php
    declare(strict_types = 1);

    class Priority {
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

        public static function getPriorities(PDO $db) : array {
            $stmt = $db->prepare('
                SELECT idPriority, name
                FROM Priority
                ORDER BY 1
            ');

            $stmt->execute();
            $result = $stmt->fetchAll();

            $priorities = array();

            foreach ($result as $row)
                $priorities[] = new Priority(
                    (int) $row['idPriority'],
                    $row['name']
                );

            return $priorities;
        }

        public static function getPriority(PDO $db, int $id) : ?Priority {
            $stmt = $db->prepare('
                SELECT idPriority, name
                FROM Priority
                WHERE idPriority = ?
            ');

            $stmt->execute(array($id));
            $priority = $stmt->fetch();

            if (!$priority) return null;

            return new Priority(
                (int) $priority['idPriority'],
                $priority['name']
            );
        }

        public static function getPriorityByName(PDO $db, string $name) : ?Priority {
            $stmt = $db->prepare('
                SELECT idPriority, name
                FROM Priority
                WHERE name = ?
            ');

            $stmt->execute(array($name));
            $priority = $stmt->fetch();

            if (!$priority) return null;

            return new Priority(
                (int) $priority['idPriority'],
                $priority['name']
            );
        }

        public static function addPriority(PDO $db, string $name) : bool {
            $stmt = $db->prepare('
                INSERT INTO Priority (name)
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
                FROM Priority
                WHERE idPriority = ?
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
