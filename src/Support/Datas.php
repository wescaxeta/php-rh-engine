<?php declare(strict_types=1);

namespace RhEngine\Support;

use DateTimeImmutable;

final class Datas
{
    /**
     * Soma meses sem o "estouro" do DateTime::modify('+1 month'): 31/01 + 1 mês
     * resulta em 28/02 (ou 29/02), e não em 03/03.
     */
    public static function adicionarMeses(DateTimeImmutable $data, int $meses): DateTimeImmutable
    {
        $primeiroDoMes = $data->modify('first day of this month')->modify(sprintf('%+d months', $meses));
        $dia           = min((int) $data->format('j'), (int) $primeiroDoMes->format('t'));

        return $primeiroDoMes->setDate(
            (int) $primeiroDoMes->format('Y'),
            (int) $primeiroDoMes->format('n'),
            $dia,
        );
    }
}
