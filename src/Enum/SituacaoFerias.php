<?php declare(strict_types=1);

namespace RhEngine\Enum;

enum SituacaoFerias: string
{
    case Solicitada  = 'solicitada';
    case Aprovada    = 'aprovada';
    case EmAndamento = 'em_andamento';
    case Concluida   = 'concluida';
    case Cancelada   = 'cancelada';

    public function ehFinal(): bool
    {
        return match ($this) {
            self::Concluida, self::Cancelada => true,
            default                          => false,
        };
    }

    /** Gozos já iniciados ou encerrados consomem saldo; cancelados não. */
    public function consomeSaldo(): bool
    {
        return match ($this) {
            self::Aprovada, self::EmAndamento, self::Concluida => true,
            default                                            => false,
        };
    }
}
