<?php

namespace App\Traits\Invoice;

use Illuminate\Support\Facades\Session;

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

    protected string $columnSessionPrefix = 'columns';

    public function initializeColumnPreferences(): void
    {
        $sessionKey = $this->getColumnSessionKey();
        $this->visibleColumns = Session::get($sessionKey, $this->visibleColumns);
    }

    public function toggleColumn($column): void
    {
        if (isset($this->visibleColumns[$column])) {
            $this->visibleColumns[$column] = ! $this->visibleColumns[$column];
            Session::put($this->getColumnSessionKey(), $this->visibleColumns);
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

        Session::put($this->getColumnSessionKey(), $this->visibleColumns);
    }

    public function isColumnVisible($column): bool
    {
        return isset($this->visibleColumns[$column]) && $this->visibleColumns[$column];
    }

    protected function getColumnSessionKey(): string
    {
        return "{$this->columnSessionPrefix}_".class_basename($this);
    }
}
