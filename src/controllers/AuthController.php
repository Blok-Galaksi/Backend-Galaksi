<?php
declare(strict_types=1);

final class AuthController
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function register(array $input): never
    {
        $name = trim(is_string($input['name'] ?? null) ? $input['name'] : '');
        $email = strtolower(trim(is_string($input['email'] ?? null) ? $input['email'] : ''));
        $password = is_string($input['password'] ?? null) ? $input['password'] : '';

        if (
            !preg_match('/\A.{1,100}\z/us', $name)
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || strlen($email) > 150
            || strlen($password) < 8
            || strlen($password) > 72
        ) {
            respond(422, [
                'error' => 'Data tidak valid',
                'details' => [
                    'name' => 'Nama wajib diisi dan maksimal 100 karakter',
                    'email' => 'Email harus valid dan maksimal 150 karakter',
                    'password' => 'Password harus 8-72 byte',
                ],
            ]);
        }

        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)'
            );
            $statement->execute([
                'name' => $name,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $userId = (int) $this->pdo->lastInsertId();
            $token = $this->issueToken($userId);
            $this->pdo->commit();
        } catch (PDOException $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            if ($exception->getCode() === '23000') {
                respond(409, ['error' => 'Email sudah terdaftar']);
            }
            throw $exception;
        }

        respond(201, [
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => ['id' => $userId, 'name' => $name, 'email' => $email],
                ...$token,
            ],
        ]);
    }

    public function login(array $input): never
    {
        $email = strtolower(trim(is_string($input['email'] ?? null) ? $input['email'] : ''));
        $password = is_string($input['password'] ?? null) ? $input['password'] : '';

        $statement = $this->pdo->prepare(
            'SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            respond(401, ['error' => 'Email atau password salah']);
        }

        $token = $this->issueToken((int) $user['id']);
        respond(200, [
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => (int) $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                ],
                ...$token,
            ],
        ]);
    }

    private function issueToken(int $userId): array
    {
        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $expiresAt = new DateTimeImmutable('+30 days', new DateTimeZone('UTC'));

        $statement = $this->pdo->prepare(
            'INSERT INTO api_tokens (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, :expires_at)'
        );
        $statement->execute([
            'user_id' => $userId,
            'token_hash' => hash('sha256', $token),
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->format(DateTimeInterface::ATOM),
        ];
    }
}
