<?php

namespace App\Imports;

use App\Models\Subscription;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class SubscriptionImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * 
     * @return Model|array|null
     */
    public function model(array $row): Model|array|null
    {
        if (empty($row['account'])) {
            return null;
        }

        return new Subscription([
            'subscription_title' => $row['subscription'] ?? null, 
            'account_name'       => $row['account'],
            // Pass the values through our new helper method
            'since_date'         => $this->parseDate($row['since'] ?? null),
            'next_due_date'      => $this->parseDate($row['next_due'] ?? null),
        ]);
    }

    /**
     * Safely parse Excel dates whether they are numeric or strings.
     */
    private function parseDate($value)
    {
        // If the cell is empty, return null
        if (empty($value)) {
            return null;
        }

        // If it's a numeric Excel date (e.g., 45274)
        if (is_numeric($value)) {
            return Carbon::instance(Date::excelToDateTimeObject($value));
        }

        // If it's a string date (e.g., "2024-05-12" or "12/05/2024")
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            // If it's completely unreadable text, default to null so it doesn't crash
            return null; 
        }
    }
}