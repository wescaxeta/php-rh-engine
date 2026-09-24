<?php declare(strict_types=1);

namespace RhEngine\Model\Ferias;

use DateTimeImmutable;
use RhEngine\Exception\RhException;

class Calculator
{
    private const DIAS_POR_PERIODO   = 30;
    private const MESES_PARA_AQUISICAO = 12;
    private const MAX_PARCELAS       = 3;
    private const MINIMO_DIAS_PARCELA = 10;

    public function calcularPeriodoAquisitivo(
        DateTimeImmutable $dataAdmissao,
        DateTimeImmutable $referencia
    ): PeriodoAquisitivo {
        $mesesTrabalhados  = $this->mesesEntreDatas($dataAdmissao, $referencia);
        $periodosCompletos = intdiv($mesesTrabalhados, self::MESES_PARA_AQUISICAO);

        $inicioPeriodo = $dataAdmissao->modify(
            sprintf('+%d months', $periodosCompletos * self::MESES_PARA_AQUISICAO)
        );
        $fimPeriodo = $inicioPeriodo->modify('+12 months -1 day');

        return new PeriodoAquisitivo(
            inicio: $inicioPeriodo,
            fim: $fimPeriodo,
            numero: $periodosCompletos + 1,
        );
    }

    public function calcularSaldo(int $diasGozados, int $periodosVencidos = 1): int
    {
        $totalDireito = $periodosVencidos * self::DIAS_POR_PERIODO;
        return max(0, $totalDireito - $diasGozados);
    }

    public function validarParcela(int $diasSolicitados, int $saldoAtual, int $parcelasUsadas): void
    {
        if ($parcelasUsadas >= self::MAX_PARCELAS) {
            throw new RhException('Limite de parcelas de férias atingido.');
        }

        if ($diasSolicitados < self::MINIMO_DIAS_PARCELA) {
            throw new RhException(sprintf(
                'Parcela mínima é de %d dias.',
                self::MINIMO_DIAS_PARCELA
            ));
        }

        if ($diasSolicitados > $saldoAtual) {
            throw new RhException(sprintf(
                'Saldo insuficiente. Disponível: %d dias, solicitado: %d dias.',
                $saldoAtual,
                $diasSolicitados
            ));
        }
    }

    private function mesesEntreDatas(DateTimeImmutable $inicio, DateTimeImmutable $fim): int
    {
        $diff = $inicio->diff($fim);
        return $diff->y * 12 + $diff->m;
    }
}
