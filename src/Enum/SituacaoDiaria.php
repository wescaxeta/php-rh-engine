<?php declare(strict_types=1);

namespace RhEngine\Enum;

enum SituacaoDiaria: string
{
    case Solicitada     = 'solicitada';
    case AprovadaNucleo = 'aprovada_nucleo';
    case Aprovada       = 'aprovada';
    case Paga           = 'paga';
    case Rejeitada      = 'rejeitada';
}
