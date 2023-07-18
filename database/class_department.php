<?php
    declare(strict_types = 1);

    class Department {
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

        public static function getDepartments(PDO $db) : array {
            $stmt = $db->prepare('
                SELECT idDepartment, name
                FROM Department
                ORDER BY 2
            ');

            $stmt->execute();
            $result = $stmt->fetchAll();

            $departments = array();

            foreach ($result as $row)
                $departments[] = new Department(
                    (int) $row['idDepartment'],
                    $row['name']
                );

            return $departments;
        }

        public static function getAgentDepartments(PDO $db, int $id) : array {
            $stmt = $db->prepare('
                SELECT idDepartment, name
                FROM Department NATURAL JOIN AgentDepartment
                WHERE idAgent = ?
                ORDER BY 2
            ');

            $stmt->execute(array($id));
            $result = $stmt->fetchAll();

            $departments = array();

            foreach ($result as $row)
                $departments[] = new Department(
                    (int) $row['idDepartment'],
                    $row['name']
                );

            return $departments;
        }

        public static function getClientDepartments(PDO $db, int $id) : array {
            $stmt = $db->prepare('
                SELECT idDepartment, name
                FROM Department NATURAL JOIN Ticket
                WHERE idUser = ?
                ORDER BY 2
            ');

            $stmt->execute(array($id));
            $result = $stmt->fetchAll();

            $departments = array();

            foreach ($result as $row)
                $departments[] = new Department(
                    (int) $row['idDepartment'],
                    $row['name']
                );

            return $departments;
        }

        public static function getDepartment(PDO $db, int $id) : ?Department {
            $stmt = $db->prepare('
                SELECT idDepartment, name
                FROM Department
                WHERE idDepartment = ?
            ');

            $stmt->execute(array($id));
            $department = $stmt->fetch();

            if (!$department) return null;

            return new Department(
                (int) $department['idDepartment'],
                $department['name']
            );
        }

        public static function getDepartmentByName(PDO $db, string $name) : ?Department {
            $stmt = $db->prepare('
                SELECT idDepartment, name
                FROM Department
                WHERE name = ?
            ');

            $stmt->execute(array($name));
            $department = $stmt->fetch();

            if (!$department) return null;

            return new Department(
                (int) $department['idDepartment'],
                $department['name']
            );
        }

        public static function addDepartment(PDO $db, string $name) : bool {
            $stmt = $db->prepare('
                INSERT INTO Department (name)
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
                FROM Department
                WHERE idDepartment = ?
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
