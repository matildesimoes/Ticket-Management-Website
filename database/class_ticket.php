<?php
    declare(strict_types = 1);

    require_once(__DIR__ . '/../database/class_user.php');
    require_once(__DIR__ . '/../database/class_department.php');
    require_once(__DIR__ . '/../database/class_priority.php');
    require_once(__DIR__ . '/../database/class_status.php');
    require_once(__DIR__ . '/../database/class_faq.php');
    require_once(__DIR__ . '/../database/class_tag.php');
    require_once(__DIR__ . '/../database/class_change.php');
    require_once(__DIR__ . '/../database/class_message.php');

    class Ticket {
        public int $id;
        public User $author;
        public string $title;
        public string $description;
        public string $dateOpened;
        public ?string $dateClosed;
        public ?User $agent;
        public ?Department $department;
        public ?Priority $priority;
        public ?Status $status;
        public ?FAQ $faq;
        public ?string $filename;

        public function __construct(int $id, User $author, string $title, string $description, string $dateOpened, ?string $dateClosed, ?User $agent, ?Department $department, ?Priority $priority, ?Status $status, ?FAQ $faq, ?string $filename) {
            $this->id = $id;
            $this->author = $author;
            $this->title = $title;
            $this->description = $description;
            $this->dateOpened = $dateOpened;
            $this->dateClosed = $dateClosed;
            $this->agent = $agent;
            $this->department = $department;
            $this->priority = $priority;
            $this->status = $status;
            $this->faq = $faq;
            $this->filename = $filename;
        }

        public function __toString() {
            return $this->title;
        }

        public function getId() : int {
            return $this->id;
        }

        public function getAuthor() : User {
            return $this->author;
        }

        public function getTitle() : string {
            return $this->title;
        }

        public function getDescription() : string {
            return $this->description;
        }

        public function getDateOpened() : string {
            return $this->dateOpened;
        }

        public function getDateClosed() : ?string {
            return $this->dateClosed;
        }

        public function getAgent() : ?User {
            return $this->agent;
        }

        public function getDepartment() : ?Department {
            return $this->department;
        }

        public function getPriority() : ?Priority {
            return $this->priority;
        }

        public function getStatus() : ?Status {
            return $this->status;
        }

        public function getFAQ() : ?FAQ {
            return $this->faq;
        }

        public function getFilename() : ?string {
            return $this->filename;
        }

        private static function parseTicket(PDO $db, $ticket) : Ticket {
            return new Ticket(
                (int) $ticket['idTicket'],
                User::getUser($db, (int) $ticket['idUser']),
                $ticket['title'],
                $ticket['description'],
                $ticket['dateOpened'],
                $ticket['dateClosed'],
                User::getUser($db, (int) $ticket['idAgent']),
                Department::getDepartment($db, (int) $ticket['idDepartment']),
                Priority::getPriority($db, (int) $ticket['idPriority']),
                Status::getStatus($db, (int) $ticket['idStatus']),
                FAQ::getFAQ($db, (int) $ticket['idFAQ']),
                $ticket['filename']
            );
        }

        public static function getTicket(PDO $db, int $id) : ?Ticket {
            $stmt = $db->prepare('
                SELECT idTicket, idUser, title, description, dateOpened, dateClosed, idAgent, idDepartment, idPriority, idStatus, idFAQ, filename
                FROM Ticket
                WHERE idTicket = ?
            ');

            $stmt->execute(array($id));
            $ticket = $stmt->fetch();

            if (!$ticket) return null;

            return self::parseTicket($db, $ticket);
        }

        public static function getTicketsClient(PDO $db, int $id, string $after, string $before, int $department, int $priority, int $status, int $agent, int $tag) : array {
            $stmt = $db->prepare("
                SELECT idTicket, idUser, title, description, dateOpened, dateClosed, idAgent, idDepartment, idPriority, idStatus, idFAQ, filename
                FROM Ticket
                WHERE (idUser = ?) 
                AND (? = '' OR dateOpened > ?) 
                AND (? = '' OR dateOpened < ?)
                AND (? = '0' OR idDepartment = ?) 
                AND (? = '0' OR idPriority = ?) 
                AND (? = '0' OR idStatus = ?) 
                AND (? = '0' OR idAgent = ?)
                AND (? = '0' OR idTicket IN (SELECT idTicket FROM TicketTag WHERE idTag = ?))
                ORDER BY 5 DESC, 9 DESC, 3
            ");

            $stmt->execute(array($id, $after, $after, $before, $before, $department, $department, $priority, $priority, $status, $status, $agent, $agent, $tag, $tag));
            $result = $stmt->fetchAll();

            $tickets = array();

            foreach ($result as $row)
                $tickets[] = self::parseTicket($db, $row);
            
            return $tickets;
        }

        public static function getTicketsAgent(PDO $db, int $id, string $after, string $before, int $department, int $priority, int $status, int $agent, int $tag) : array {
            $stmt = $db->prepare("
                SELECT idTicket, idUser, title, description, dateOpened, dateClosed, idAgent, idDepartment, idPriority, idStatus, idFAQ, filename
                FROM Ticket
                WHERE (idUser = ? OR idAgent = ? OR idDepartment IS NULL OR idDepartment IN (SELECT idDepartment FROM AgentDepartment WHERE idAgent = ?))
                AND (? = '' OR dateOpened > ?) 
                AND (? = '' OR dateOpened < ?)
                AND (? = '0' OR idDepartment = ?) 
                AND (? = '0' OR idPriority = ?) 
                AND (? = '0' OR idStatus = ?) 
                AND (? = '0' OR idAgent = ?)
                AND (? = '0' OR idTicket IN (SELECT idTicket FROM TicketTag WHERE idTag = ?))
                ORDER BY 5 DESC, 9 DESC, 3
            ");

            $stmt->execute(array($id, $id, $id, $after, $after, $before, $before, $department, $department, $priority, $priority, $status, $status, $agent, $agent, $tag, $tag));
            $result = $stmt->fetchAll();

            $tickets = array();

            foreach ($result as $row)
                $tickets[] = self::parseTicket($db ,$row);

            return $tickets;
        }

        public static function getTickets(PDO $db, string $after, string $before, int $department, int $priority, int $status, int $agent, int $tag) : array {
            $stmt = $db->prepare("
                SELECT idTicket, idUser, title, description, dateOpened, dateClosed, idAgent, idDepartment, idPriority, idStatus, idFAQ, filename
                FROM Ticket
                WHERE (? = '' OR dateOpened > ?) 
                AND (? = '' OR dateOpened < ?)
                AND (? = '0' OR idDepartment = ?) 
                AND (? = '0' OR idPriority = ?) 
                AND (? = '0' OR idStatus = ?) 
                AND (? = '0' OR idAgent = ?)
                AND (? = '0' OR idTicket IN (SELECT idTicket FROM TicketTag WHERE idTag = ?))
                ORDER BY 5 DESC, 9 DESC, 3
            ");

            $stmt->execute(array($after, $after, $before, $before, $department, $department, $priority, $priority, $status, $status, $agent, $agent, $tag, $tag));
            $result = $stmt->fetchAll();

            $tickets = array();

            foreach ($result as $row)
                $tickets[] = self::parseTicket($db, $row);

            return $tickets;
        }

        public static function getTicketsCountByStatus(PDO $db, int $id, int $status) : int {
            $stmt = $db->prepare('
                SELECT idTicket
                FROM Ticket
                WHERE idUser = ? AND idStatus = ?
            ');

            $stmt->execute(array($id, $status));
            $result = $stmt->fetchAll();

            return count($result);
        }

        public static function addTicket(PDO $db, int $idUser, string $title, string $description, string $dateOpened, ?int $department, int $tag, ?string $filename) : bool {
            $stmt = $db->prepare('
                INSERT INTO Ticket (idUser, title, description, dateOpened, idDepartment, filename)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            try {
                $stmt->execute(array($idUser, $title, $description, $dateOpened, $department, $filename));
            } catch (PDOException $e)  {
                return false;
            }

            $stmt = $db->prepare('
                SELECT max(idTicket)
                FROM Ticket
            ');
            $stmt->execute();
            $result = $stmt->fetch();
            $id = (int) $result['max(idTicket)'];

            $ticket = self::getTicket($db, $id);

            $ticket->addTag($db, $tag);

            return true;
        }

        public function edit(PDO $db, string $title, string $description) : bool {
            $stmt = $db->prepare('
                UPDATE Ticket
                SET title = ?, description = ?
                WHERE idTicket = ?
            ');

            try {
                $stmt->execute(array($title, $description, $this->id));
            } catch (PDOException $e) {
                return false;
            }

            return true;
        }

        public function editProperties(PDO $db, int $status, ?int $priority, ?int $department, ?int $agent, array $tags) : bool {
            $stmt = $db->prepare('
                UPDATE Ticket
                SET idStatus = ?, idPriority = ?, idDepartment = ?, idAgent = ?
                WHERE idTicket = ?
            ');

            try {
                $stmt->execute(array($status, $priority, $department, $agent, $this->id));
            } catch (PDOException $e) {
                return false;
            }

            foreach ($tags as $tag)
                $this->addTag($db, $tag->id);

            return true;
        }

        public function delete(PDO $db) : bool {
            $stmt = $db->prepare('
                DELETE
                FROM Ticket
                WHERE idTicket = ?
            ');

            try {
                $stmt->execute(array($this->id));
            } catch (PDOException $e) {
                return false;
            }

            return true;
        }

        public function getTags(PDO $db) : ?array {
            $stmt = $db->prepare('
                SELECT idTag, name
                FROM TicketTag NATURAL JOIN Tag
                WHERE idTicket = ?
                ORDER BY 2
            ');

            $stmt->execute(array($this->id));
            $result = $stmt->fetchAll();

            $tags = array();

            foreach ($result as $row)
                $tags[] = new Tag(
                    (int) $row['idTag'],
                    $row['name']
                );

            return $tags;
        }

        public function getChanges(PDO $db) : ?array {
            $stmt = $db->prepare('
                SELECT idChange, date, description
                FROM Change
                WHERE idTicket = ?
                ORDER BY 2
            ');

            $stmt->execute(array($this->id));
            $result = $stmt->fetchAll();

            $changes = array();

            foreach ($result as $row)
                $changes[] = new Change(
                    (int) $row['idChange'],
                    $row['date'],
                    $row['description']
                );

            return $changes;
        }

        public function getMessages(PDO $db) : ?array {
            $stmt = $db->prepare('
                SELECT idMessage, date, content, idUser
                FROM Message
                WHERE idTicket = ?
                ORDER BY 2
            ');

            $stmt->execute(array($this->id));
            $result = $stmt->fetchAll();

            $messages = array();

            foreach ($result as $row)
                $messages[] = new Message(
                    (int) $row['idMessage'],
                    $row['date'],
                    $row['content'],
                    User::getUser($db, (int) $row['idUser'])
                );

            return $messages;
        }

        public function addMessage(PDO $db, string $date, string $content, int $author) : bool {
            $stmt = $db->prepare('
                INSERT INTO Message (date, content, idTicket, idUser)
                VALUES (?, ?, ?, ?)
            ');

            try {
                $stmt->execute(array($date, $content, $this->id, $author));
            } catch (PDOException $e) {
                return false;
            }

            return true;
        }
            
        public function addTag(PDO $db, int $tag) : void {
            $stmt = $db->prepare('
                INSERT INTO TicketTag (idTicket, idTag)
                VALUES (?, ?)
            ');
            try {
                $stmt->execute(array($this->id, $tag));
            } catch (PDOException $e) {}
        }

        public function deleteTag(PDO $db, int $tag) : bool {
            $stmt = $db->prepare('
                DELETE
                FROM TicketTag
                WHERE idTicket = ? AND idTag = ? 
            ');

            try {
                $stmt->execute(array($this->id, $tag));
            } catch (PDOException $e) {
                return false;
            }

            return true;
        }
    }
?>
