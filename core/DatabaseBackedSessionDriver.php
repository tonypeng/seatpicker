<?php

class DatabaseBackedSessionDriver implements ISessionDriver
{
    public function lookup($session_key)
    {
        $stmt = db()->prepare('SELECT t2.* FROM `sessions` t1
            JOIN `students` t2 ON t2.id=t1.user_id
            WHERE session_key = :key AND expire_time >= :expire');
        $stmt->bindValue(':key', $session_key);
        $stmt->bindValue(':expire', time());

        try {
            $stmt->execute();
        } catch (PDOException $pdoe) {
            throw new DatabaseException();
        }

        $row = $stmt->fetch();

        return new Session($row['id'], $row['student_id'], $row['first_name'], $row['last_name'], $row['phonetic_name']);
    }

    public function write($user_id, $username, $expire_time)
    {
        $session_key = sha1(md5($user_id.'|'.uniqid('', true).'|'.time()));

        $stmt = db()->prepare('INSERT INTO `sessions` (session_key, create_time, expire_time, user_id)
            VALUES (:key, :create, :expire, :user)');
        $stmt->bindValue(':key', $session_key);
        $stmt->bindValue(':create', time());
        $stmt->bindValue(':expire', $expire_time);
        $stmt->bindValue(':user', $user_id);

        try {
            $stmt->execute();
        } catch (PDOException $pdoe) {
            throw new DatabaseException();
        }

        return $session_key;
    }

    public function signout($user_id)
    {
        $stmt = db()->prepare('UPDATE `sessions` SET expire_time=0 WHERE user_id=:uid');
        $stmt->bindValue(':uid', $user_id);
        $stmt->execute();
    }
}