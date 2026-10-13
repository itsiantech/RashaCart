<?php

namespace App\Services\Tracking;

use Illuminate\Support\Str;

class HashingService
{
    /**
     * Normalize and hash email address
     */
    public function hashEmail(?string $email): ?string
    {
        if (empty($email)) {
            return null;
        }

        // Normalize: trim, lowercase
        $normalized = trim(strtolower($email));

        // Hash with SHA256
        return hash('sha256', $normalized);
    }

    /**
     * Normalize and hash phone number (Bangladesh format)
     * Accepts: 01XXXXXXXXX, +8801XXXXXXXXX, 8801XXXXXXXXX
     */
    public function hashPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove all non-digit characters
        $digits = preg_replace('/[^0-9]/', '', $phone);

        // Normalize to 8801XXXXXXXXX format
        if (strlen($digits) === 11 && str_starts_with($digits, '01')) {
            // 01XXXXXXXXX -> 8801XXXXXXXXX (drop the leading 0)
            $digits = '880'.substr($digits, 1);
        } elseif (strlen($digits) === 13 && str_starts_with($digits, '880')) {
            // Already 8801XXXXXXXXX
            // Keep as is
        } else {
            // Invalid format, return null
            return null;
        }

        // Ensure it starts with 8801
        if (!str_starts_with($digits, '8801')) {
            return null;
        }

        // Hash with SHA256
        return hash('sha256', $digits);
    }

    /**
     * Normalize and hash first name
     */
    public function hashFirstName(?string $firstName): ?string
    {
        if (empty($firstName)) {
            return null;
        }

        // Normalize: lowercase, trim, remove punctuation
        $normalized = strtolower(trim($firstName));
        $normalized = preg_replace('/[^\p{L}\s]/u', '', $normalized);

        if (empty($normalized)) {
            return null;
        }

        return hash('sha256', $normalized);
    }

    /**
     * Normalize and hash last name
     */
    public function hashLastName(?string $lastName): ?string
    {
        if (empty($lastName)) {
            return null;
        }

        // Normalize: lowercase, trim, remove punctuation
        $normalized = strtolower(trim($lastName));
        $normalized = preg_replace('/[^\p{L}\s]/u', '', $normalized);

        if (empty($normalized)) {
            return null;
        }

        return hash('sha256', $normalized);
    }

    /**
     * Normalize and hash city
     */
    public function hashCity(?string $city): ?string
    {
        if (empty($city)) {
            return null;
        }

        // Normalize: lowercase, trim, remove punctuation
        $normalized = strtolower(trim($city));
        $normalized = preg_replace('/[^\p{L}\s]/u', '', $normalized);

        if (empty($normalized)) {
            return null;
        }

        return hash('sha256', $normalized);
    }

    /**
     * Normalize and hash state
     */
    public function hashState(?string $state): ?string
    {
        if (empty($state)) {
            return null;
        }

        // Normalize: lowercase, trim, remove punctuation
        $normalized = strtolower(trim($state));
        $normalized = preg_replace('/[^\p{L}\s]/u', '', $normalized);

        if (empty($normalized)) {
            return null;
        }

        return hash('sha256', $normalized);
    }

    /**
     * Normalize and hash zip/postal code
     */
    public function hashZip(?string $zip): ?string
    {
        if (empty($zip)) {
            return null;
        }

        // Normalize: trim, uppercase, remove spaces
        $normalized = strtoupper(trim($zip));
        $normalized = preg_replace('/\s/', '', $normalized);

        if (empty($normalized)) {
            return null;
        }

        return hash('sha256', $normalized);
    }

    /**
     * Hash country code (default: bd for Bangladesh)
     */
    public function hashCountry(?string $country = 'bd'): ?string
    {
        if (empty($country)) {
            $country = 'bd';
        }

        // Normalize: lowercase, trim
        $normalized = strtolower(trim($country));

        return hash('sha256', $normalized);
    }

    /**
     * Hash external ID (customer ID or phone hash)
     */
    public function hashExternalId(?string $externalId): ?string
    {
        if (empty($externalId)) {
            return null;
        }

        // Normalize: trim
        $normalized = trim($externalId);

        return hash('sha256', $normalized);
    }

    /**
     * Hash gender (m/f)
     */
    public function hashGender(?string $gender): ?string
    {
        if (empty($gender)) {
            return null;
        }

        // Normalize: lowercase, first character only
        $normalized = strtolower(trim($gender));
        $normalized = substr($normalized, 0, 1);

        if (!in_array($normalized, ['m', 'f'])) {
            return null;
        }

        return hash('sha256', $normalized);
    }

    /**
     * Hash date of birth (YYYY-MM-DD format)
     */
    public function hashDateOfBirth(?string $dob): ?string
    {
        if (empty($dob)) {
            return null;
        }

        // Normalize: ensure YYYY-MM-DD format
        try {
            $date = \Carbon\Carbon::parse($dob);
            $normalized = $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }

        return hash('sha256', $normalized);
    }

    /**
     * Build user data array with hashed PII
     */
    public function buildUserData(array $data): array
    {
        $userData = [];

        // Hash email
        if (!empty($data['email'])) {
            $userData['em'] = $this->hashEmail($data['email']);
        }

        // Hash phone
        if (!empty($data['phone'])) {
            $userData['ph'] = $this->hashPhone($data['phone']);
        }

        // Hash first name
        if (!empty($data['first_name'])) {
            $userData['fn'] = $this->hashFirstName($data['first_name']);
        }

        // Hash last name
        if (!empty($data['last_name'])) {
            $userData['ln'] = $this->hashLastName($data['last_name']);
        }

        // Hash city
        if (!empty($data['city'])) {
            $userData['ct'] = $this->hashCity($data['city']);
        }

        // Hash state
        if (!empty($data['state'])) {
            $userData['st'] = $this->hashState($data['state']);
        }

        // Hash zip
        if (!empty($data['zip'])) {
            $userData['zp'] = $this->hashZip($data['zip']);
        }

        // Hash country
        $userData['country'] = $this->hashCountry($data['country'] ?? 'bd');

        // Hash external ID
        if (!empty($data['external_id'])) {
            $userData['external_id'] = $this->hashExternalId($data['external_id']);
        }

        // Hash gender
        if (!empty($data['gender'])) {
            $userData['ge'] = $this->hashGender($data['gender']);
        }

        // Hash date of birth
        if (!empty($data['date_of_birth'])) {
            $userData['db'] = $this->hashDateOfBirth($data['date_of_birth']);
        }

        // Non-hashed fields (Meta Conversions API)
        if (!empty($data['client_ip_address'])) {
            $userData['client_ip_address'] = $data['client_ip_address'];
        }

        if (!empty($data['client_user_agent'])) {
            $userData['client_user_agent'] = $data['client_user_agent'];
        }

        if (!empty($data['fbp'])) {
            $userData['fbp'] = $data['fbp'];
        }

        if (!empty($data['fbc'])) {
            $userData['fbc'] = $data['fbc'];
        }

        if (!empty($data['fbclid'])) {
            $userData['fbclid'] = $data['fbclid'];
        }

        return $userData;
    }

    /**
     * Generate a random event ID
     */
    public function generateEventId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Generate deterministic event ID for purchase
     */
    public function generatePurchaseEventId(int $orderId): string
    {
        return 'purchase_' . $orderId;
    }
}
