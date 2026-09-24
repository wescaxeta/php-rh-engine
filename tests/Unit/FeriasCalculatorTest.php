<?php declare(strict_types=1);

namespace RhEngine\Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use RhEngine\Exception\RhException;
use RhEngine\Model\Ferias\Calculator;

class FeriasCalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    public function testPrimeiroPeriodoIniciaComAdmissao(): void
    {
        $admissao   = new DateTimeImmutable('2025-01-01');
        $referencia = new DateTimeImmutable('2025-06-01');

        $periodo = $this->calculator->calcularPeriodoAquisitivo($admissao, $referencia);

        $this->assertEquals('2025-01-01', $periodo->inicio->format('Y-m-d'));
        $this->assertEquals(1, $periodo->numero);
    }

    public function testSegundoPeriodoIniciaAposDoze(): void
    {
        $admissao   = new DateTimeImmutable('2024-01-01');
        $referencia = new DateTimeImmutable('2025-06-01');

        $periodo = $this->calculator->calcularPeriodoAquisitivo($admissao, $referencia);

        $this->assertEquals(2, $periodo->numero);
        $this->assertEquals('2025-01-01', $periodo->inicio->format('Y-m-d'));
    }

    public function testSaldoCompletoSemGozos(): void
    {
        $this->assertEquals(30, $this->calculator->calcularSaldo(diasGozados: 0));
    }

    public function testSaldoAposGozoParcial(): void
    {
        $this->assertEquals(15, $this->calculator->calcularSaldo(diasGozados: 15));
    }

    public function testSaldoNaoFicaNegativo(): void
    {
        $this->assertEquals(0, $this->calculator->calcularSaldo(diasGozados: 35));
    }

    public function testParcelaAbaixoDoMinimoLancaExcecao(): void
    {
        $this->expectException(RhException::class);
        $this->expectExceptionMessage('Parcela mínima');

        $this->calculator->validarParcela(diasSolicitados: 5, saldoAtual: 30, parcelasUsadas: 0);
    }

    public function testParcelaSemSaldoLancaExcecao(): void
    {
        $this->expectException(RhException::class);
        $this->expectExceptionMessage('Saldo insuficiente');

        $this->calculator->validarParcela(diasSolicitados: 20, saldoAtual: 10, parcelasUsadas: 0);
    }

    public function testLimiteDeParcelasAtingidoLancaExcecao(): void
    {
        $this->expectException(RhException::class);
        $this->expectExceptionMessage('Limite de parcelas');

        $this->calculator->validarParcela(diasSolicitados: 10, saldoAtual: 30, parcelasUsadas: 3);
    }

    public function testPeriodoVencidoQuandoReferenciaPassouDoFim(): void
    {
        $admissao   = new DateTimeImmutable('2023-01-01');
        $referencia = new DateTimeImmutable('2026-01-01');

        $periodo = $this->calculator->calcularPeriodoAquisitivo($admissao, $referencia);

        $this->assertTrue($periodo->estaVencido($referencia));
    }
}
