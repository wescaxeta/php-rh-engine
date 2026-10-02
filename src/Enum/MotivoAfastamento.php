<?php declare(strict_types=1);

namespace RhEngine\Enum;

enum MotivoAfastamento: int
{
    case LicencaMedica      = 1;
    case LicencaMaternidade = 2;
    case LicencaPaternidade = 3;
    case LicencaPremio      = 4;
    case AfastamentoServico = 5;
    case Obito              = 6;

    /** Afastamentos a serviço não bloqueiam o registro de ponto do servidor. */
    public function desbloqueiaHorario(): bool
    {
        return $this === self::AfastamentoServico;
    }

    public function rotulo(): string
    {
        return match ($this) {
            self::LicencaMedica      => 'Licença médica',
            self::LicencaMaternidade => 'Licença-maternidade',
            self::LicencaPaternidade => 'Licença-paternidade',
            self::LicencaPremio      => 'Licença-prêmio',
            self::AfastamentoServico => 'Afastamento a serviço',
            self::Obito              => 'Óbito (luto)',
        };
    }
}
