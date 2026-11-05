<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\AuthRoles;

class LoginModel extends Model
{
    protected $db;
    protected $roles;

    public function __construct()
    {
        parent::__construct();

        $this->db = \Config\Database::connect();
        $this->roles = config(AuthRoles::class);
    }

    /**
     * Get role mapping.
     */
    private function getMapping(string $role, string $type = 'otp')
    {
        $mappings = $type === 'password'
            ? $this->roles->passwordRoles
            : $this->roles->otpRoles;

        return $mappings[$role] ?? null;
    }

    /**
     * Find active user.
     */
    public function findActiveUser(
        string $identifier,
        string $role,
        string $type = 'otp'
    ): ?array {

        $mapping = $this->getMapping($role, $type);
         
        if (!$mapping) {
            return null;
        }

        $builder = $this->db->table($mapping['table']);

        $builder->where($mapping['column'], $identifier);
        $builder->where('isActive', 1);

        if (isset($mapping['dbRole'])) {
            $builder->where('role', $mapping['dbRole']);
        }

        return $builder->get()->getRowArray() ?: null;
    }

    /**
     * Save OTP hash and expiry.
     */
    public function saveOtp(
        string $identifier,
        string $role,
        string $otpHash,
        string $otp
    ): bool {

        $mapping = $this->getMapping($role);

        if (!$mapping) {
            return false;
        }

        $data = [
            'otp'       => $otp,
            'otp_hash'       => $otpHash,
            'otp_expires_at' => date(
                'Y-m-d H:i:s',
                time() + 300
            ),
            'otp_attempts'   => 0,
        ];

        $builder = $this->db->table($mapping['table']);

        $builder->where($mapping['column'], $identifier);
        $builder->where('isActive', 1);

        if (isset($mapping['dbRole'])) {
            $builder->where('role', $mapping['dbRole']);
        }

        return $builder->update($data);
    }

    /**
     * Verify OTP.
     */
    public function verifyOtp(
        string $identifier,
        string $role,
        string $otp
    ): ?array {

        $mapping = $this->getMapping($role);

        if (!$mapping) {
            return null;
        }

        $builder = $this->db->table($mapping['table']);

        $builder->where($mapping['column'], $identifier);
        $builder->where('isActive', 1);

        if (isset($mapping['dbRole'])) {
            $builder->where('role', $mapping['dbRole']);
        }
         
        $user = $builder->get()->getRowArray();
        
        if (!$user || empty($user['otp_hash'])) {
            return null;
        }

        if (
            empty($user['otp_expires_at']) ||
            strtotime($user['otp_expires_at']) < time()
        ) {
            return null;
        }

        if ((int) $user['otp_attempts'] >= 5) {
            return null;
        }
       
        if (!password_verify($otp, $user['otp_hash'])) {

            $builder = $this->db->table($mapping['table']);

            $builder->where($mapping['column'], $identifier);

            if (isset($mapping['dbRole'])) {
                $builder->where('role', $mapping['dbRole']);
            }

            $builder->set(
                'otp_attempts',
                'otp_attempts + 1',
                false
            );

            $builder->update();

            return null;
        }
        
        // Clear OTP after successful verification.
        $builder = $this->db->table($mapping['table']);

        $builder->where($mapping['column'], $identifier);

        if (isset($mapping['dbRole'])) {
            $builder->where('role', $mapping['dbRole']);
        }

        $builder->update([
            'otp_hash'       => null,
            'otp_expires_at' => null,
            'otp_attempts'   => 0,
        ]);

        return $user;
    }

    /**
     * Set password.
     */
    public function setUserPassword(
        string $identifier,
        string $role,
        string $password
    ): bool {

        $mapping = $this->getMapping($role, 'password');

        if (!$mapping) {
            return false;
        }
        
        $builder = $this->db->table($mapping['table']);

        $builder->where($mapping['column'], $identifier);
        $builder->where('isActive', 1);

        if (isset($mapping['dbRole'])) {
            $builder->where('role', $mapping['dbRole']);
        }

        return $builder->update([
            'password' => password_hash(
                $password,
                PASSWORD_DEFAULT
            ),
        ]);
    }

    /**
     * Password authentication.
     */
    public function authenticatePassword(
        string $identifier,
        string $role,
        string $password
    ): ?array {

        $user = $this->findActiveUser(
            $identifier,
            $role,
            'password'
        );
       
        if (!$user || empty($user['password'])) {
            return null;
        }
         
         $passwordHash = password_hash(
            $user['password'],
            PASSWORD_DEFAULT
        );
        if (!password_verify($password, $passwordHash)) {
            return null;
        }
        return $user;
    }

    /**
     * Check whether password is configured.
     */
    public function hasPassword(array $user): bool
    {
        return !empty($user['password']);
    }
}
