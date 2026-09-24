<?php declare(strict_types=1);

namespace RhEngine\Enum;

enum MotivoAfastamento: int
{
    case LicencaMedica       = 1;
    case LicencaMaternidade  = 2;
    case LicencaPaternidade  = 3;
    case LicencaPremio       = 4;
    case AfastamentoServico  = 5;
    case Obito               = 6;
}
