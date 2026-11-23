<?php

/**
 * Модель токенов восстановления пароля (таблица password_resets).
 *
 * В базе хранится не сам токен, а его SHA-256 хэш — даже если базу украдут,
 * готовыми ссылками для сброса пароля воспользоваться не получится.
 */
class PasswordReset extends Model
{
    /** Сколько минут действует ссылка для сброса пароля */
    private const LIFETIME_MINUTES = 60;

    /**
     * Создаёт новый токен для email (старые токены этого email удаляются).
     *
     * @param string $email Email пользователя.
     * @return string Токен в открытом виде — он попадёт в ссылку из письма.
     */
    public function create(string $email): string
    {
        $this->deleteByEmail($email);

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::LIFETIME_MINUTES * 60);

        $stmt = $this->db->prepare(
            'INSERT INTO password_resets (email, token_hash, expires_at) VALUES (?, ?, ?)'
        );
        $stmt->execute([$email, hash('sha256', $token), $expiresAt]);

        return $token;
    }

    /**
     * Находит email по действующему (не просроченному) токену.
     *
     * @param string $token Токен из ссылки.
     * @return string|null Email или null, если токен неверный или просрочен.
     */
    public function findEmailByToken(string $token): ?string
    {
        $stmt = $this->db->prepare(
            'SELECT email FROM password_resets WHERE token_hash = ? AND expires_at > ?'
        );
        $stmt->execute([hash('sha256', $token), date('Y-m-d H:i:s')]);
        $email = $stmt->fetchColumn();
        return $email === false ? null : $email;
    }

    /**
     * Удаляет все токены для email.
     *
     * @param string $email Email пользователя.
     * @return void
     */
    public function deleteByEmail(string $email): void
    {
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE email = ?');
        $stmt->execute([$email]);
    }
}
