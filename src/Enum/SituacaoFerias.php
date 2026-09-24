<?php declare(strict_types=1);

namespace RhEngine\Enum;

enum SituacaoFerias: string
{
    case Solicitada    = 'solicitada';
    case Aprovada      = 'aprovada';
    case EmAndamento   = 'em_andamento';
    case Concluida     = 'concluida';
    case Cancelada     = 'cancelada';
}
