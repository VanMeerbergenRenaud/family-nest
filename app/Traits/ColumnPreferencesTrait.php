<?php

namespace App\Traits;

use Illuminate\Support\Facades\Session;

trait ColumnPreferencesTrait
{
    public array $visibleColumns = [];

    protected array $defaultColumns = [
        'name' => true,
        'type' => false,
        'category' => false,
        'issuer_name' => false,
        'amount' => true,
        'issued_date' => true,
        'payment_status' => false,
        'payment_due_date' => false,
        'tags' => true,
    ];

    protected string $columnSessionPrefix = 'columns';

    public function initializeColumnPreferences(): void
    {
        $sessionKey = $this->getColumnSessionKey();

        $sessionColumns = Session::get($sessionKey);

        if ($sessionColumns) {
            $this->visibleColumns = $sessionColumns;
        } elseif (! empty($this->defaultColumns)) {
            $this->visibleColumns = $this->defaultColumns;
        }
    }

    public function toggleColumn($column): void
    {
        if (isset($this->visibleColumns[$column])) {
            $this->visibleColumns[$column] = ! $this->visibleColumns[$column];

            // Sauvegarder les préférences en session
            Session::put($this->getColumnSessionKey(), $this->visibleColumns);
        }
    }

    public function resetColumns(): void
    {
        $this->visibleColumns = $this->defaultColumns;

        Session::put($this->getColumnSessionKey(), $this->visibleColumns);
    }

    public function isColumnVisible($column): bool
    {
        return isset($this->visibleColumns[$column]) && $this->visibleColumns[$column];
    }

    protected function getColumnSessionKey(): string
    {
        $className = class_basename($this);

        return "{$this->columnSessionPrefix}_{$className}";
    }
}
