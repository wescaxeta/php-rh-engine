<?php declare(strict_types=1);

namespace RhEngine\Model\Ferias;

use DateTimeImmutable;
use RhEngine\Exception\DadoInvalidoException;
use RhEngine\Exception\ParcelaFeriasInvalidaException;
use RhEngine\Support\Datas;

final class Calculator
{
    private const int DIAS_POR_PERIODO     = 30;
    private const int MESES_PARA_AQUISICAO = 12;
    private const int MAX_PARCELAS         = 3;
    private const int MINIMO_DIAS_PARCELA  = 10;

    public function calcularPeriodoAquisitivo(
        DateTimeImmutable $dataAdmissao,
        DateTimeImmutable $referencia,
    ): PeriodoAquisitivo {
        if ($referencia < $dataAdmissao) {
            throw new DadoInvalidoException('Data de referência não pode ser anterior à admissão.');
        }

        // Avança período a período a partir da admissão (e não a partir do início do
        // período anterior) para não acumular o ajuste de fim de mês — ex.: admissão em 31/01.
        $numero = 1;
        while (Datas::adicionarMeses($dataAdmissao, $numero * self::MESES_PARA_AQUISICAO) <= $referencia) {
            $numero++;
        }

        $inicio = Datas::adicionarMeses($dataAdmissao, ($numero - 1) * self::MESES_PARA_AQUISICAO);
        $fim    = Datas::adicionarMeses($dataAdmissao, $numero * self::MESES_PARA_AQUISICAO)->modify('-1 day');

        return new PeriodoAquisitivo(inicio: $inicio, fim: $fim, numero: $numero);
    }

    public function calcularSaldo(int $diasGozados, int $periodosVencidos = 1): int
    {
        if ($diasGozados < 0 || $periodosVencidos < 0) {
            throw new DadoInvalidoException('Dias gozados e períodos vencidos não podem ser negativos.');
        }

        return max(0, $periodosVencidos * self::DIAS_POR_PERIODO - $diasGozados);
    }

    public function validarParcela(int $diasSolicitados, int $saldoAtual, int $parcelasUsadas): void
    {
        if ($parcelasUsadas >= self::MAX_PARCELAS) {
            throw ParcelaFeriasInvalidaException::limiteDeParcelasAtingido(self::MAX_PARCELAS);
        }

        if ($diasSolicitados < self::MINIMO_DIAS_PARCELA) {
            throw ParcelaFeriasInvalidaException::abaixoDoMinimo(self::MINIMO_DIAS_PARCELA);
        }

        if ($diasSolicitados > $saldoAtual) {
            throw ParcelaFeriasInvalidaException::saldoInsuficiente($saldoAtual, $diasSolicitados);
        }
    }
}
