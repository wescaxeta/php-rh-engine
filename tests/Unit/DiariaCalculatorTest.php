<?php declare(strict_types=1);

namespace RhEngine\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RhEngine\Exception\DadoInvalidoException;
use RhEngine\Model\Diaria\Calculator;
use RhEngine\ValueObject\Money;

#[CoversClass(Calculator::class)]
final class DiariaCalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    /**
     * @return iterable<string, array{string, int, bool, int}>
     */
    public static function valores(): iterable
    {
        yield 'com pernoite' => ['200.00', 3, true, 60000];
        yield 'sem pernoite é metade' => ['200.00', 1, false, 10000];
        yield 'metade de valor ímpar arredonda o centavo' => ['100.01', 1, false, 5001];
        yield 'vários dias sem pernoite' => ['180.50', 4, false, 36100];
    }

    #[Test]
    #[DataProvider('valores')]
    public function calculaValor(string $valorDiaria, int $dias, bool $comPernoite, int $centavosEsperados): void
    {
        $valor = $this->calculator->calcularValor(Money::deReais($valorDiaria), $dias, $comPernoite);

        self::assertSame($centavosEsperados, $valor->centavos);
    }

    #[Test]
    public function quantidadeDeDiasZeroLancaExcecao(): void
    {
        $this->expectException(DadoInvalidoException::class);

        $this->calculator->calcularValor(Money::deReais('200.00'), quantidadeDias: 0, comPernoite: true);
    }
}
