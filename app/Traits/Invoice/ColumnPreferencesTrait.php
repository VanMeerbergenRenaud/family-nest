<?php

namespace App\Traits\Invoice;

trait ColumnPreferencesTrait
{
    public array $visibleColumns = [
        'name' => true,
        'reference' => false,
        'type' => false,
        'category' => false,
        'issuer_name' => true,
        'amount' => true,
        'issued_date' => false,
        'payment_status' => false,
        'payment_due_date' => false,
        'tags' => true,
    ];

    public function toggleColumn($column): void
    {
        if (isset($this->visibleColumns[$column])) {
            $this->visibleColumns[$column] = !$this->visibleColumns[$column];
        }
    }

    public function resetColumns(): void
    {
        $this->visibleColumns = [
            'name' => true,
            'reference' => false,
            'type' => false,
            'category' => false,
            'issuer_name' => true,
            'amount' => true,
            'issued_date' => false,
            'payment_status' => false,
            'payment_due_date' => false,
            'tags' => true,
        ];
    }

    public function isColumnVisible($column): bool
    {
        return isset($this->visibleColumns[$column]) && $this->visibleColumns[$column];
    }
}
