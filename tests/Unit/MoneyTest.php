<?php declare(strict_types=1);

namespace RhEngine\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RhEngine\Exception\DadoInvalidoException;
use RhEngine\ValueObject\Money;

#[CoversClass(Money::class)]
final class MoneyTest extends TestCase
{
    /**
     * @return iterable<string, array{string, int}>
     */
    public static function valoresEmReais(): iterable
    {
        yield 'inteiro' => ['200', 20000];
        yield 'uma casa decimal' => ['10.5', 1050];
        yield 'duas casas decimais' => ['1234.56', 123456];
        yield 'zero' => ['0.00', 0];
    }

    #[Test]
    #[DataProvider('valoresEmReais')]
    public function criaAPartirDeReais(string $reais, int $centavos): void
    {
        self::assertSame($centavos, Money::deReais($reais)->centavos);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function valoresInvalidos(): iterable
    {
        yield 'vírgula' => ['10,50'];
        yield 'três casas decimais' => ['1.234'];
        yield 'negativo' => ['-5.00'];
        yield 'texto' => ['abc'];
    }

    #[Test]
    #[DataProvider('valoresInvalidos')]
    public function rejeitaFormatoInvalido(string $valor): void
    {
        $this->expectException(DadoInvalidoException::class);

        Money::deReais($valor);
    }

    #[Test]
    public function naoAceitaCentavosNegativos(): void
    {
        $this->expectException(DadoInvalidoException::class);

        Money::deCentavos(-1);
    }

    #[Test]
    public function operacoesSaoImutaveis(): void
    {
        $original = Money::deReais('100.00');

        $soma = $original->somar(Money::deCentavos(50));

        self::assertSame(10000, $original->centavos);
        self::assertSame(10050, $soma->centavos);
        self::assertTrue(Money::zero()->igual(Money::deCentavos(0)));
    }

    #[Test]
    public function evitaErroDeArredondamentoDeFloat(): void
    {
        // Em float, 0.1 + 0.2 !== 0.3. Em centavos inteiros a soma é exata.
        $soma = Money::deReais('0.10')->somar(Money::deReais('0.20'));

        self::assertTrue($soma->igual(Money::deReais('0.30')));
    }

    #[Test]
    public function formataEmReal(): void
    {
        self::assertSame('R$ 1.234,56', Money::deCentavos(123456)->formatar());
    }
}
