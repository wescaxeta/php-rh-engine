<?php declare(strict_types=1);

namespace RhEngine\Enum;

enum SituacaoDiaria: string
{
    case Solicitada     = 'solicitada';
    case AprovadaNucleo = 'aprovada_nucleo';
    case Aprovada       = 'aprovada';
    case Paga           = 'paga';
    case Rejeitada      = 'rejeitada';

    public function ehFinal(): bool
    {
        return match ($this) {
            self::Paga, self::Rejeitada => true,
            default                     => false,
        };
    }

    public function proxima(): ?self
    {
        return match ($this) {
            self::Solicitada            => self::AprovadaNucleo,
            self::AprovadaNucleo        => self::Aprovada,
            self::Aprovada              => self::Paga,
            self::Paga, self::Rejeitada => null,
        };
    }

    /** Uma diária só pode ser rejeitada antes da aprovação final. */
    public function podeSerRejeitada(): bool
    {
        return match ($this) {
            self::Solicitada, self::AprovadaNucleo => true,
            default                                => false,
        };
    }

    public function rotulo(): string
    {
        return match ($this) {
            self::Solicitada     => 'Solicitada',
            self::AprovadaNucleo => 'Aprovada pelo núcleo',
            self::Aprovada       => 'Aprovada',
            self::Paga           => 'Paga',
            self::Rejeitada      => 'Rejeitada',
        };
    }
}
