<?php
    declare(strict_types = 1);

    class Change {
        public int $id;
        public string $date;
        public string $description;

        public function __construct(int $id, string $date, string $description) {
            $this->id = $id;
            $this->date = $date;
            $this->description = $description;
        }

        public function __toString() {
            return $this->description;
        }

        public function getId() : int {
            return $this->id;
        }

        public function getDate() : string {
            return $this->date;
        }

        public function getDescription() : string {
            return $this->description;
        }
    }
?>
