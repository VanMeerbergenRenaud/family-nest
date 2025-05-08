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

    public bool $isMobile = false;

    public bool $hasInitializedMobileColumns = false;

    public function initializeColumnPreferencesTrait()
    {
        // Détecte si l'appareil est mobile
        $this->detectDevice();
    }

    public function detectDevice()
    {
        $userAgent = request()->header('User-Agent');
        $this->isMobile = preg_match('/(android|iphone|ipod|mobile|phone)/i', $userAgent);

        // N'initialise les colonnes mobile que si ça n'a pas déjà été fait
        if ($this->isMobile && ! $this->hasInitializedMobileColumns) {
            $this->setMobileColumns();
            $this->hasInitializedMobileColumns = true;
        }
    }

    public function setMobileColumns()
    {
        // Sur mobile, n'affiche que la colonne nom par défaut
        foreach ($this->visibleColumns as $column => $value) {
            $this->visibleColumns[$column] = ($column === 'name');
        }
    }

    public function toggleColumn($column): void
    {
        if (isset($this->visibleColumns[$column])) {
            $this->visibleColumns[$column] = ! $this->visibleColumns[$column];
        }
    }

    public function resetColumns(): void
    {
        if ($this->isMobile) {
            $this->setMobileColumns();
        } else {
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
    }

    public function isColumnVisible($column): bool
    {
        return isset($this->visibleColumns[$column]) && $this->visibleColumns[$column];
    }
}
