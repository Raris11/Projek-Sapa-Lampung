<?php

class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $email = $this->escape($email);
        $query = mysqli_query($this->db, "SELECT * FROM users WHERE email = '$email' LIMIT 1");

        if ($query && mysqli_num_rows($query) === 1) {
            return mysqli_fetch_assoc($query);
        }

        return null;
    }

    public function findPetugasProfile(int $userId): ?array
    {
        $userId = (int) $userId;
        $query = mysqli_query($this->db, "SELECT * FROM petugas WHERE id_user = '$userId' LIMIT 1");

        if ($query && mysqli_num_rows($query) === 1) {
            return mysqli_fetch_assoc($query);
        }

        return null;
    }

    public function updatePasswordHash(int $userId, string $hash): bool
    {
        $userId = (int) $userId;
        $hash = $this->escape($hash);

        return (bool) mysqli_query($this->db, "UPDATE users SET password = '$hash' WHERE id_user = '$userId'");
    }
}

