<?php declare(strict_types=1);

namespace RhEngine\Model\Ferias;

use DateTimeImmutable;

readonly class PeriodoAquisitivo
{
    public function __construct(
        public DateTimeImmutable $inicio,
        public DateTimeImmutable $fim,
        public int $numero,
    ) {}

    public function estaVencido(DateTimeImmutable $referencia): bool
    {
        return $referencia > $this->fim;
    }

    public function estaAtivo(DateTimeImmutable $referencia): bool
    {
        return $referencia >= $this->inicio && $referencia <= $this->fim;
    }
}
