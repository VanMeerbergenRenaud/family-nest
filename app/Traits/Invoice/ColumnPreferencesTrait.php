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

    public bool $isTablet = false;

    public bool $isDesktop = true;

    public bool $hasInitializedDeviceColumns = false;

    public function initializeColumnPreferencesTrait(): void
    {
        $this->detectDevice();
    }

    public function detectDevice(): void
    {
        $userAgent = request()->header('User-Agent');

        $this->isTablet = preg_match('/(ipad|tablet|playbook|silk)/i', $userAgent);
        $this->isMobile = ! $this->isTablet && preg_match('/(android|iphone|ipod|mobile|phone)/i', $userAgent);
        $this->isDesktop = ! $this->isMobile && ! $this->isTablet;

        if (! $this->hasInitializedDeviceColumns) {
            if ($this->isMobile) {
                $this->setMobileColumns();
            } elseif ($this->isTablet) {
                $this->setTabletColumns();
            } else {
                $this->setDesktopColumns();
            }
            $this->hasInitializedDeviceColumns = true;
        }
    }

    public function setMobileColumns(): void
    {
        foreach ($this->visibleColumns as $column => $value) {
            $this->visibleColumns[$column] = ($column === 'name');
        }
    }

    public function setTabletColumns(): void
    {
        foreach ($this->visibleColumns as $column => $value) {
            $this->visibleColumns[$column] = in_array($column, ['name', 'issuer_name', 'amount']);
        }
    }

    public function setDesktopColumns(): void
    {
        foreach ($this->visibleColumns as $column => $value) {
            $this->visibleColumns[$column] = in_array($column, ['name', 'issuer_name', 'amount', 'tags']);
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
        } elseif ($this->isTablet) {
            $this->setTabletColumns();
        } else {
            $this->setDesktopColumns();
        }
    }

    public function isColumnVisible($column): bool
    {
        return isset($this->visibleColumns[$column]) && $this->visibleColumns[$column];
    }
}
