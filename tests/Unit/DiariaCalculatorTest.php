<?php declare(strict_types=1);

namespace RhEngine\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RhEngine\Enum\SituacaoDiaria;
use RhEngine\Exception\RhException;
use RhEngine\Model\Diaria\Calculator;

class DiariaCalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    public function testValorComPernoiteMultiplicaPorDias(): void
    {
        $valor = $this->calculator->calcularValor(
            valorDiariaPadrao: 200.00,
            quantidadeDias: 3,
            comPernoite: true
        );

        $this->assertEquals(600.00, $valor);
    }

    public function testValorSemPernoiteEMetadeDoValorDiario(): void
    {
        $valor = $this->calculator->calcularValor(
            valorDiariaPadrao: 200.00,
            quantidadeDias: 1,
            comPernoite: false
        );

        $this->assertEquals(100.00, $valor);
    }

    public function testFluxoDeAprovacaoCompleto(): void
    {
        $situacao = SituacaoDiaria::Solicitada;

        $situacao = $this->calculator->proximaSituacao($situacao);
        $this->assertEquals(SituacaoDiaria::AprovadaNucleo, $situacao);

        $situacao = $this->calculator->proximaSituacao($situacao);
        $this->assertEquals(SituacaoDiaria::Aprovada, $situacao);

        $situacao = $this->calculator->proximaSituacao($situacao);
        $this->assertEquals(SituacaoDiaria::Paga, $situacao);
    }

    public function testNaoPodeAvancarDiariaPaga(): void
    {
        $this->expectException(RhException::class);
        $this->calculator->proximaSituacao(SituacaoDiaria::Paga);
    }

    public function testNaoPodeAvancarDiariaRejeitada(): void
    {
        $this->expectException(RhException::class);
        $this->calculator->proximaSituacao(SituacaoDiaria::Rejeitada);
    }

    public function testPodeAvancarSituacoesIntermediarias(): void
    {
        $this->assertTrue($this->calculator->podeAvancar(SituacaoDiaria::Solicitada));
        $this->assertTrue($this->calculator->podeAvancar(SituacaoDiaria::AprovadaNucleo));
        $this->assertTrue($this->calculator->podeAvancar(SituacaoDiaria::Aprovada));
    }

    public function testNaoPodeAvancarSituacoesFinais(): void
    {
        $this->assertFalse($this->calculator->podeAvancar(SituacaoDiaria::Paga));
        $this->assertFalse($this->calculator->podeAvancar(SituacaoDiaria::Rejeitada));
    }
}
