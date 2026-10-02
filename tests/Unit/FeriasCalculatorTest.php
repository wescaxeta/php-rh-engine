<?php declare(strict_types=1);

namespace RhEngine\Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RhEngine\Exception\DadoInvalidoException;
use RhEngine\Exception\ParcelaFeriasInvalidaException;
use RhEngine\Model\Ferias\Calculator;
use RhEngine\Model\Ferias\PeriodoAquisitivo;
use RhEngine\Support\Datas;

#[CoversClass(Calculator::class)]
#[CoversClass(PeriodoAquisitivo::class)]
#[CoversClass(Datas::class)]
#[CoversClass(ParcelaFeriasInvalidaException::class)]
final class FeriasCalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    /**
     * @return iterable<string, array{string, string, int, string, string}>
     */
    public static function periodosAquisitivos(): iterable
    {
        yield 'primeiro período começa na admissão' => ['2025-01-01', '2025-06-01', 1, '2025-01-01', '2025-12-31'];
        yield 'segundo período começa após 12 meses' => ['2024-01-01', '2025-06-01', 2, '2025-01-01', '2025-12-31'];
        yield 'aniversário da admissão abre novo período' => ['2024-03-15', '2025-03-15', 2, '2025-03-15', '2026-03-14'];
        yield 'véspera do aniversário ainda é o período anterior' => ['2024-03-15', '2025-03-14', 1, '2024-03-15', '2025-03-14'];
        yield 'admissão em 31/01 não estoura o mês' => ['2023-01-31', '2025-02-10', 3, '2025-01-31', '2026-01-30'];
        yield 'admissão em 29/02 ajusta em ano não bissexto' => ['2024-02-29', '2025-03-01', 2, '2025-02-28', '2026-02-27'];
    }

    #[Test]
    #[DataProvider('periodosAquisitivos')]
    public function calculaPeriodoAquisitivo(
        string $admissao,
        string $referencia,
        int $numeroEsperado,
        string $inicioEsperado,
        string $fimEsperado,
    ): void {
        $periodo = $this->calculator->calcularPeriodoAquisitivo(
            new DateTimeImmutable($admissao),
            new DateTimeImmutable($referencia),
        );

        self::assertSame($numeroEsperado, $periodo->numero);
        self::assertSame($inicioEsperado, $periodo->inicio->format('Y-m-d'));
        self::assertSame($fimEsperado, $periodo->fim->format('Y-m-d'));
    }

    #[Test]
    public function referenciaAnteriorAAdmissaoLancaExcecao(): void
    {
        $this->expectException(DadoInvalidoException::class);

        $this->calculator->calcularPeriodoAquisitivo(
            new DateTimeImmutable('2025-01-01'),
            new DateTimeImmutable('2024-12-31'),
        );
    }

    /**
     * @return iterable<string, array{int, int, int}>
     */
    public static function saldos(): iterable
    {
        yield 'sem gozos' => [0, 1, 30];
        yield 'após gozo parcial' => [15, 1, 15];
        yield 'não fica negativo' => [35, 1, 0];
        yield 'dois períodos acumulados' => [10, 2, 50];
    }

    #[Test]
    #[DataProvider('saldos')]
    public function calculaSaldo(int $diasGozados, int $periodosVencidos, int $saldoEsperado): void
    {
        self::assertSame($saldoEsperado, $this->calculator->calcularSaldo($diasGozados, $periodosVencidos));
    }

    #[Test]
    public function saldoComDiasNegativosLancaExcecao(): void
    {
        $this->expectException(DadoInvalidoException::class);

        $this->calculator->calcularSaldo(diasGozados: -1);
    }

    /**
     * @return iterable<string, array{int, int, int, string}>
     */
    public static function parcelasInvalidas(): iterable
    {
        yield 'abaixo do mínimo' => [5, 30, 0, 'Parcela mínima'];
        yield 'sem saldo' => [20, 10, 0, 'Saldo insuficiente'];
        yield 'limite de parcelas' => [10, 30, 3, 'Limite de parcelas'];
    }

    #[Test]
    #[DataProvider('parcelasInvalidas')]
    public function parcelaInvalidaLancaExcecao(
        int $diasSolicitados,
        int $saldoAtual,
        int $parcelasUsadas,
        string $mensagem,
    ): void {
        $this->expectException(ParcelaFeriasInvalidaException::class);
        $this->expectExceptionMessage($mensagem);

        $this->calculator->validarParcela($diasSolicitados, $saldoAtual, $parcelasUsadas);
    }

    #[Test]
    public function parcelaValidaNaoLancaExcecao(): void
    {
        $this->expectNotToPerformAssertions();

        $this->calculator->validarParcela(diasSolicitados: 10, saldoAtual: 30, parcelasUsadas: 2);
    }

    #[Test]
    public function periodoVencidoQuandoReferenciaPassouDoFim(): void
    {
        $periodo = $this->calculator->calcularPeriodoAquisitivo(
            new DateTimeImmutable('2023-01-01'),
            new DateTimeImmutable('2024-06-01'),
        );

        self::assertTrue($periodo->estaVencido(new DateTimeImmutable('2026-01-01')));
        self::assertFalse($periodo->estaAtivo(new DateTimeImmutable('2026-01-01')));
        self::assertTrue($periodo->estaAtivo(new DateTimeImmutable('2024-06-01')));
    }

    #[Test]
    public function periodoConcessivoTerminaDozeMesesAposOAquisitivo(): void
    {
        $periodo = $this->calculator->calcularPeriodoAquisitivo(
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-06-01'),
        );

        self::assertSame('2025-12-31', $periodo->fimPeriodoConcessivo()->format('Y-m-d'));
        self::assertFalse($periodo->concessivoExpirado(new DateTimeImmutable('2025-12-31')));
        self::assertTrue($periodo->concessivoExpirado(new DateTimeImmutable('2026-01-01')));
    }

    #[Test]
    public function periodoComFimAntesDoInicioLancaExcecao(): void
    {
        $this->expectException(DadoInvalidoException::class);

        new PeriodoAquisitivo(
            inicio: new DateTimeImmutable('2025-01-01'),
            fim: new DateTimeImmutable('2024-12-31'),
            numero: 1,
        );
    }
}
