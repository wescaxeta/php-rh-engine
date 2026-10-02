<?php declare(strict_types=1);

namespace RhEngine\Model\Diaria;

use RhEngine\Exception\DadoInvalidoException;
use RhEngine\ValueObject\Money;

final class Calculator
{
    private const int PERCENTUAL_SEM_PERNOITE = 50;

    public function calcularValor(
        Money $valorDiariaPadrao,
        int $quantidadeDias,
        bool $comPernoite,
    ): Money {
        if ($quantidadeDias <= 0) {
            throw new DadoInvalidoException('Quantidade de dias deve ser maior que zero.');
        }

        $valorUnitario = $comPernoite
            ? $valorDiariaPadrao
            : $valorDiariaPadrao->percentual(self::PERCENTUAL_SEM_PERNOITE);

        return $valorUnitario->multiplicar($quantidadeDias);
    }
}
