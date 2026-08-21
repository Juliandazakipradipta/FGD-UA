<?php

namespace App\Hashing;

use Illuminate\Contracts\Hashing\Hasher;

class Sha256Hasher implements Hasher
{
    public function info($hashedValue)
    {
        return [
            'algo' => 'sha256',
            'algoName' => 'sha256',
            'options' => [],
        ];
    }

    public function make($value, array $options = [])
    {
        $salt = 'ululalbab_cai47_fgd_salt';
        return hash('sha256', $value . $salt);
    }

    public function check($value, $hashedValue, array $options = [])
    {
        if (str_starts_with($hashedValue, '$2')) {
            try {
                return password_verify($value, $hashedValue);
            } catch (\Throwable $e) {
                // If bcrypt is not supported, we check using our fallback hashing format
                // In production, we will seed/save passwords using sha256 to avoid this.
            }
        }
        
        return $this->make($value) === $hashedValue;
    }

    public function needsRehash($hashedValue, array $options = [])
    {
        return false;
    }
}
