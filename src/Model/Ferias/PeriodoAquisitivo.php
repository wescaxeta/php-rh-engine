<?php declare(strict_types=1);

namespace RhEngine\Model\Ferias;

use DateTimeImmutable;
use RhEngine\Exception\DadoInvalidoException;
use RhEngine\Support\Datas;

final readonly class PeriodoAquisitivo
{
    private const int MESES_PERIODO_CONCESSIVO = 12;

    public function __construct(
        public DateTimeImmutable $inicio,
        public DateTimeImmutable $fim,
        public int $numero,
    ) {
        if ($fim < $inicio) {
            throw new DadoInvalidoException('Fim do período aquisitivo não pode ser anterior ao início.');
        }

        if ($numero < 1) {
            throw new DadoInvalidoException('Número do período aquisitivo deve ser positivo.');
        }
    }

    public function estaVencido(DateTimeImmutable $referencia): bool
    {
        return $referencia > $this->fim;
    }

    public function estaAtivo(DateTimeImmutable $referencia): bool
    {
        return $referencia >= $this->inicio && $referencia <= $this->fim;
    }

    /**
     * Último dia do período concessivo: os 12 meses seguintes ao aquisitivo,
     * prazo em que as férias devem ser concedidas.
     */
    public function fimPeriodoConcessivo(): DateTimeImmutable
    {
        return Datas::adicionarMeses($this->fim->modify('+1 day'), self::MESES_PERIODO_CONCESSIVO)
            ->modify('-1 day');
    }

    public function concessivoExpirado(DateTimeImmutable $referencia): bool
    {
        return $referencia > $this->fimPeriodoConcessivo();
    }
}
