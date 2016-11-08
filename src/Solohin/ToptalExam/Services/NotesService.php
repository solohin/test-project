<?php

namespace Solohin\ToptalExam\Services;

use PDO;

class NotesService extends BaseService
{
    public function getOne($id, $userIdFilter = null)
    {
        $sql = "SELECT id, text, calories, user_id, date, time FROM notes WHERE id=?";
        $params = [(int)$id];

        if ($userIdFilter !== null) {
            $sql .= ' AND user_id = ?';
            $params[] = $userIdFilter;
        }
        $result = $this->db->fetchAssoc($sql, $params);

        return $this->readFormat($result);
    }

    public function getAll($userIdFilter = null, $fromDate = null, $toDate = null, $fromTime = null, $toTime = null, $page = 1)
    {
        $limit = 500;//Hardcoded
        $sql = "SELECT id, text, calories, user_id, date, time FROM notes WHERE 1=1";
        $params = [];

        //filters

        if ($userIdFilter !== null) {
            $sql .= ' AND user_id = ?';
            $params[] = [(int)$userIdFilter, PDO::PARAM_INT];
        }
        if ($fromDate !== null) {
            $sql .= ' AND date >= ?';
            $params[] = [$this->dateToTimestamp($fromDate), PDO::PARAM_INT];
        }
        if ($toDate !== null) {
            $sql .= ' AND date <= ?';
            $params[] = [$this->dateToTimestamp($toDate), PDO::PARAM_INT];
        }
        if ($fromTime !== null) {
            $sql .= ' AND time >= ?';
            $params[] = [$this->timeStringToSeconds($fromTime), PDO::PARAM_INT];
        }
        if ($toTime !== null) {
            $sql .= ' AND time <= ?';
            $params[] = [$this->timeStringToSeconds($toTime), PDO::PARAM_INT];
        }

        //Limits
        $limit = intval($limit);
        if ($limit > 500 || $limit < 1) {
            $limit = 500;
        }
        $page = intval($page);
        if ($page < 1) {
            $page = 1;
        }

        $sql .= ' LIMIT ? OFFSET ?';
        $params[] = [$limit, PDO::PARAM_INT];
        $params[] = [($page - 1) * $limit, PDO::PARAM_INT];

        //sql

        $statement = $this->db->prepare($sql, $params);
        foreach ($params as $index => $param) {
            $statement->bindValue($index + 1, $param[0], $param[1]);
        }
        $statement->execute();
        $rows = $statement->fetchAll();

        $result = [];
        foreach ($rows as $line) {
            $result[] = $this->readFormat($line);
        }
        return $result;
    }

    public function insert($note)
    {
        $note = $this->writeFormat($note);
        $this->db->insert('notes', $note);
        return $this->db->lastInsertId();
    }

    public function update($id, $note, $userIdFilter = null)
    {
        $note = $this->writeFormat($note);
        $identifier = ['id' => $id];
        if ($userIdFilter !== null) {
            $identifier['user_id'] = $userIdFilter;
        }

        return !!$this->db->update('notes', $note, $identifier);
    }

    public function delete($id, $userIdFilter = null)
    {
        $identifier = ['id' => $id];
        if ($userIdFilter !== null) {
            $identifier['user_id'] = $userIdFilter;
        }

        return !!$this->db->delete('notes', $identifier);
    }

    private function dateToTimestamp($dateString)
    {
        $date = \DateTime::createFromFormat('d.m.Y', $dateString);
        if ($date instanceof \DateTime) {
            return $date->getTimestamp();
        } else {
            throw new \Exception($dateString . ' is incorrect date');
        }
    }

    private function timestampToDate($timestamp)
    {
        return date('d.m.Y', $timestamp);
    }

    private function writeFormat($note)
    {
        if (isset($note['time'])) {
            $note['time'] = $this->timeStringToSeconds($note['time']);
        }
        if (isset($note['date'])) {
            $note['date'] = $this->dateToTimestamp($note['date']);
        }
        return $note;
    }

    private function readFormat($note)
    {
        if ($note === false) {
            return $note;
        }

        $note['time'] = $this->secondsToTimeString($note['time']);
        $note['date'] = $this->timestampToDate($note['date']);
        return $note;
    }

    private function timeStringToSeconds($timeStr)
    {
        $tempTime = explode(':', $timeStr);

        if (count($tempTime) !== 2) {
            throw new \Exception('Time have to be in 23:59 format. You pass ' . $timeStr);
        }

        $hours = intval($tempTime[0]);
        $minutes = intval($tempTime[1]);

        if ($hours > 23 || $hours < 0 || $minutes > 59 || $minutes < 0) {
            throw new \Exception('Time have to be in 23:59 format. You pass ' . $timeStr);
        }

        return $hours * 60 * 60 + $minutes * 60;
    }

    private function secondsToTimeString($seconds)
    {
        $hours = floor($seconds / (60 * 60));
        $minutes = $seconds % (60 * 60);

        return sprintf("%02d", $hours) . ':' . sprintf("%02d", $minutes);
    }
}
