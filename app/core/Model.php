<?php

abstract class Model
{
    protected mysqli $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function escape(?string $value): string
    {
        return mysqli_real_escape_string($this->db, (string) $value);
    }
}
