<?php declare(strict_types=1);

namespace RhEngine\Exception;

final class ParcelaFeriasInvalidaException extends RhException
{
    public static function limiteDeParcelasAtingido(int $maximo): self
    {
        return new self(sprintf('Limite de parcelas de férias atingido (máximo: %d).', $maximo));
    }

    public static function abaixoDoMinimo(int $minimo): self
    {
        return new self(sprintf('Parcela mínima é de %d dias.', $minimo));
    }

    public static function saldoInsuficiente(int $disponivel, int $solicitado): self
    {
        return new self(sprintf(
            'Saldo insuficiente. Disponível: %d dias, solicitado: %d dias.',
            $disponivel,
            $solicitado,
        ));
    }
}
