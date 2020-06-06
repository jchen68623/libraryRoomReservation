<?php

class DatabaseAdapter
{

    private $DB;

    public function __construct()
    {
        $db = 'mysql:dbname=LibraryRoom; host=127.0.0.1; charset=utf8';
        $user = 'root';
        $password = '';
        try {
            $this->DB = new PDO($db, $user, $password);
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo ('Error establishing Connection');
            exit();
        }
    }

    // add student info / sign up
    public function addInfo($studentId, $firstname, $lastname, $password)
    {
        $stmt = $this->DB->prepare("
                INSERT into students
                values(:studentId, :firstname, :lastname, :password);
                ");
        $stmt->bindParam(':studentId', $studentId);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
    }

    // check if student is in the database
    public function readData($studentId, $password)
    {
        $stmt = $this->DB->prepare("
                select
                *
                from students
                WHERE id = :studentId;
                ");
        $stmt->bindParam(':studentId', $studentId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkAvailRooms()
    {
        $stmt = $this->DB->prepare("
                select
                *
                from rooms;
                ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tryMakeReservation($studentId, $roomName, $start_time, $end_time, $date)
    {
        $stmt0 = $this->DB->prepare("
                select
                *
                from rooms
                WHERE
                name = :roomName
                ");
        $stmt0->bindParam(':roomName', $roomName);
        $stmt0->execute();
        $no_room = $stmt0->fetchAll(PDO::FETCH_ASSOC);
        if (count($no_room) == 0) {
            // exactly same reservation found, return
            return "Room Not Existed";
        }

        if ($start_time > $end_time) {
            return "Start Time is after End Time. You cannot time travel. No reservation is made.";
        }

        $stmt = $this->DB->prepare("
                select
                *
                from reservations
                WHERE
                    room_name = :roomName
                    AND start_time = :start_time
                    AND end_time = :end_time
                    AND date = :date;
                ");
        $stmt->bindParam(':roomName', $roomName);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $exact_match = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($exact_match) > 0) {
            // exactly same reservation found, return
            return "The room your are trying to book is not available. Please check the availability of the room.";
        }

        $stmt2 = $this->DB->prepare("
            select
            *
            from reservations
            WHERE
                room_name = :roomName
                AND date = :date
                AND start_time < :start_time 
                AND end_time > :start_time;
            ");
        $stmt2->bindParam(':roomName', $roomName);
        $stmt2->bindParam(':date', $date);
        $stmt2->bindParam(':start_time', $start_time);
        $stmt2->execute();
        $start_conflict = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        if (count($start_conflict) > 0) {
            // exactly same reservation found, return
            return "Start time and End time cover existing reserved time. Please check the availability of the room.";
        }

        $stmt3 = $this->DB->prepare("
            select
            *
            from reservations
            WHERE
                room_name = :roomName
                AND date = :date
                AND  start_time >= :start_time 
                AND  end_time <= :end_time;
            ");
        $stmt3->bindParam(':roomName', $roomName);
        $stmt3->bindParam(':date', $date);
        $stmt3->bindParam(':start_time', $start_time);
        $stmt3->bindParam(':end_time', $end_time);
        $stmt3->execute();
        $end_conflict = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        if (count($end_conflict) > 0) {
            // exactly same reservation found, return
            return "Start time and End time cover existing reserved time. Please check the availability of the room.";
        }

        $stmt4 = $this->DB->prepare("
            select
            *
            from reservations
            WHERE
                room_name = :roomName
                AND date = :date
                AND  start_time >= :start_time
                AND  start_time < :end_time
                AND  end_time > :end_time;
            ");
        $stmt4->bindParam(':roomName', $roomName);
        $stmt4->bindParam(':date', $date);
        $stmt4->bindParam(':start_time', $start_time);
        $stmt4->bindParam(':end_time', $end_time);
        $stmt4->execute();
        $end_conflict = $stmt4->fetchAll(PDO::FETCH_ASSOC);
        if (count($end_conflict) > 0) {
            // exactly same reservation found, return
            return "Start time and End time cover existing reserved time. Please check the availability of the room.";
        }

        $stmt5 = $this->DB->prepare("
            INSERT
            INTO reservations
            (student_id, room_name, start_time, end_time, date)
            VALUES(:studentId, :roomName, :start_time, :end_time, :date)
            ");
        $stmt5->bindParam(':studentId', $studentId);
        $stmt5->bindParam(':roomName', $roomName);
        $stmt5->bindParam(':date', $date);
        $stmt5->bindParam(':start_time', $start_time);
        $stmt5->bindParam(':end_time', $end_time);
        $stmt5->execute();
        return "Congratulations! Your reservation has been made!";
    }

    public function findAllRooms()
    {
        $stmt4 = $this->DB->prepare("
            select
            *
            from rooms
            ");
        $stmt4->execute();
        return $stmt4->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkIfAvailable($roomName, $start_time, $end_time, $date)
    {
        $stmt0 = $this->DB->prepare("
                select
                *
                from rooms
                WHERE
                name = :roomName
                ");
        $stmt0->bindParam(':roomName', $roomName);
        $stmt0->execute();
        $no_room = $stmt0->fetchAll(PDO::FETCH_ASSOC);
        if (count($no_room) == 0) {
            // exactly same reservation found, return
            return "Not Available";
        }

        $stmt = $this->DB->prepare("
                select
                *
                from reservations
                WHERE
                    room_name = :roomName
                    AND start_time = :start_time
                    AND end_time = :end_time
                    AND date = :date;
                ");
        $stmt->bindParam(':roomName', $roomName);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $exact_match = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($exact_match) > 0) {
            // exactly same reservation found, return
            return "Not Available";
        }

        $stmt2 = $this->DB->prepare("
            select
            *
            from reservations
            WHERE
                room_name = :roomName
                AND date = :date
                AND start_time < :start_time
                AND end_time > :start_time;
            ");
        $stmt2->bindParam(':roomName', $roomName);
        $stmt2->bindParam(':date', $date);
        $stmt2->bindParam(':start_time', $start_time);
        $stmt2->execute();
        $start_conflict = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        if (count($start_conflict) > 0) {
            // exactly same reservation found, return
            return "Not Available";
        }

        $stmt3 = $this->DB->prepare("
            select
            *
            from reservations
            WHERE
                room_name = :roomName
                AND date = :date
                AND  start_time >= :start_time
                AND  end_time <= :end_time;
            ");
        $stmt3->bindParam(':roomName', $roomName);
        $stmt3->bindParam(':date', $date);
        $stmt3->bindParam(':start_time', $start_time);
        $stmt3->bindParam(':end_time', $end_time);
        $stmt3->execute();
        $end_conflict = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        if (count($end_conflict) > 0) {
            // exactly same reservation found, return
            return "Not Available";
        }

        $stmt4 = $this->DB->prepare("
            select
            *
            from reservations
            WHERE
                room_name = :roomName
                AND date = :date
                AND  start_time >= :start_time
                AND  start_time < :end_time
                AND  end_time > :end_time;
            ");
        $stmt4->bindParam(':roomName', $roomName);
        $stmt4->bindParam(':date', $date);
        $stmt4->bindParam(':start_time', $start_time);
        $stmt4->bindParam(':end_time', $end_time);
        $stmt4->execute();
        $end_conflict = $stmt4->fetchAll(PDO::FETCH_ASSOC);
        if (count($end_conflict) > 0) {
            // exactly same reservation found, return
            return "Not Available";
        }

        return "Available";
    }

    public function getAllReservations()
    {
        $stmt4 = $this->DB->prepare("
            SELECT reservations.room_name, reservations.start_time, reservations.end_time, 
                    reservations.date,rooms.type
            FROM reservations
            JOIN rooms
            ON reservations.room_name = rooms.name;
            ");
        $stmt4->execute();
        return $stmt4->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>