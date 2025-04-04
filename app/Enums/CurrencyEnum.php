<?php

namespace App\Enums;

use Illuminate\Support\Number;

enum CurrencyEnum: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case CHF = 'CHF';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case CNY = 'CNY';
    case INR = 'INR';
    case BRL = 'BRL';
    case RUB = 'RUB';
    case KRW = 'KRW';
    case MXN = 'MXN';
    case SGD = 'SGD';
    case NZD = 'NZD';
    case HKD = 'HKD';
    case SEK = 'SEK';
    case NOK = 'NOK';
    case DKK = 'DKK';
    case TRY = 'TRY';
    case ZAR = 'ZAR';
    case AED = 'AED';
    case PLN = 'PLN';
    case SAR = 'SAR';

    public function symbol(): string
    {
        return match ($this) {
            self::EUR => '€',
            self::USD, self::CAD, self::AUD, self::SGD, self::NZD, self::HKD => '$',
            self::GBP => '£',
            self::JPY => '¥',
            self::CHF => 'CHF',
            self::CNY => '¥',
            self::INR => '₹',
            self::BRL => 'R$',
            self::RUB => '₽',
            self::KRW => '₩',
            self::MXN => '$',
            self::SEK, self::DKK, self::NOK => 'kr',
            self::TRY => '₺',
            self::ZAR => 'R',
            self::AED => 'د.إ',
            self::PLN => 'zł',
            self::SAR => '﷼',
        };
    }

    public function symbolWithIndicator(): string
    {
        return match ($this) {
            self::EUR => '€',
            self::USD => '$',
            self::CAD => 'CA$',
            self::AUD => 'A$',
            self::SGD => 'S$',
            self::NZD => 'NZ$',
            self::HKD => 'HK$',
            self::GBP => '£',
            self::JPY => '¥',
            self::CHF => 'CHF',
            self::CNY => '¥',
            self::INR => '₹',
            self::BRL => 'R$',
            self::RUB => '₽',
            self::KRW => '₩',
            self::MXN => 'MX$',
            self::SEK => 'SEK',
            self::DKK => 'DKK',
            self::NOK => 'NOK',
            self::TRY => '₺',
            self::ZAR => 'R',
            self::AED => 'AED',
            self::PLN => 'zł',
            self::SAR => 'SAR',
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::EUR => 'Euro',
            self::USD => 'Dollar américain',
            self::GBP => 'Livre sterling',
            self::JPY => 'Yen japonais',
            self::CHF => 'Franc suisse',
            self::CAD => 'Dollar canadien',
            self::AUD => 'Dollar australien',
            self::CNY => 'Yuan chinois',
            self::INR => 'Roupie indienne',
            self::BRL => 'Real brésilien',
            self::RUB => 'Rouble russe',
            self::KRW => 'Won sud-coréen',
            self::MXN => 'Peso mexicain',
            self::SGD => 'Dollar de Singapour',
            self::NZD => 'Dollar néo-zélandais',
            self::HKD => 'Dollar de Hong Kong',
            self::SEK => 'Couronne suédoise',
            self::NOK => 'Couronne norvégienne',
            self::DKK => 'Couronne danoise',
            self::TRY => 'Livre turque',
            self::ZAR => 'Rand sud-africain',
            self::AED => 'Dirham des Émirats',
            self::PLN => 'Złoty polonais',
            self::SAR => 'Riyal saoudien',
        };
    }

    public function flag(): string
    {
        return match ($this) {
            self::EUR => '🇪🇺',
            self::USD => '🇺🇸',
            self::GBP => '🇬🇧',
            self::JPY => '🇯🇵',
            self::CHF => '🇨🇭',
            self::CAD => '🇨🇦',
            self::AUD => '🇦🇺',
            self::CNY => '🇨🇳',
            self::INR => '🇮🇳',
            self::BRL => '🇧🇷',
            self::RUB => '🇷🇺',
            self::KRW => '🇰🇷',
            self::MXN => '🇲🇽',
            self::SGD => '🇸🇬',
            self::NZD => '🇳🇿',
            self::HKD => '🇭🇰',
            self::SEK => '🇸🇪',
            self::NOK => '🇳🇴',
            self::DKK => '🇩🇰',
            self::TRY => '🇹🇷',
            self::ZAR => '🇿🇦',
            self::AED => '🇦🇪',
            self::PLN => '🇵🇱',
            self::SAR => '🇸🇦',
        };
    }

    public function labelWithEmoji(): string
    {
        return $this->flag().'&nbsp;&nbsp;'.$this->value.' - '.$this->symbolWithIndicator();
    }

    public function format(float $amount, string $locale = 'fr_FR'): string
    {
        return Number::currency($amount, $this->value, locale: $locale);
    }

    public static function getCurrencyOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value.' - '.$case->name();
        }

        return $options;
    }

    public static function getCurrencyOptionsWithEmojis(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->labelWithEmoji();
        }

        return $options;
    }
}
