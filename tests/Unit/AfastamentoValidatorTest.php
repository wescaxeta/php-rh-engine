<?php declare(strict_types=1);

namespace RhEngine\Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RhEngine\Enum\MotivoAfastamento;
use RhEngine\Exception\DadoInvalidoException;
use RhEngine\Model\Afastamento\Validator;

#[CoversClass(Validator::class)]
#[CoversClass(MotivoAfastamento::class)]
final class AfastamentoValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    #[Test]
    public function idPessoaInvalidoLancaExcecao(): void
    {
        $this->expectException(DadoInvalidoException::class);
        $this->expectExceptionMessage('Pessoa inválida');

        $this->validator->validar(
            idPessoa: 0,
            motivo: MotivoAfastamento::LicencaMedica,
            inicio: new DateTimeImmutable('2026-01-01'),
            fim: new DateTimeImmutable('2026-01-10'),
        );
    }

    #[Test]
    public function dataInicioMaiorQueFimLancaExcecao(): void
    {
        $this->expectException(DadoInvalidoException::class);
        $this->expectExceptionMessage('Data de início não pode ser posterior');

        $this->validator->validar(
            idPessoa: 1,
            motivo: MotivoAfastamento::LicencaMedica,
            inicio: new DateTimeImmutable('2026-01-10'),
            fim: new DateTimeImmutable('2026-01-01'),
        );
    }

    #[Test]
    public function afastamentoDeUmDiaEValido(): void
    {
        $this->expectNotToPerformAssertions();

        $this->validator->validar(
            idPessoa: 1,
            motivo: MotivoAfastamento::Obito,
            inicio: new DateTimeImmutable('2026-01-10'),
            fim: new DateTimeImmutable('2026-01-10'),
        );
    }

    /**
     * @return iterable<string, array{MotivoAfastamento, bool, bool}>
     */
    public static function regrasDeDesbloqueio(): iterable
    {
        foreach (MotivoAfastamento::cases() as $motivo) {
            yield $motivo->name . ' / comissionado' => [$motivo, true, true];
        }

        yield 'afastamento a serviço / efetivo' => [MotivoAfastamento::AfastamentoServico, false, true];
        yield 'licença médica / efetivo' => [MotivoAfastamento::LicencaMedica, false, false];
        yield 'licença-prêmio / efetivo' => [MotivoAfastamento::LicencaPremio, false, false];
    }

    #[Test]
    #[DataProvider('regrasDeDesbloqueio')]
    public function aplicaRegraDeDesbloqueioDeHorario(
        MotivoAfastamento $motivo,
        bool $eComissionado,
        bool $esperado,
    ): void {
        self::assertSame($esperado, $this->validator->desbloqueiaHorario($motivo, $eComissionado));
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function codigosInexistentes(): iterable
    {
        yield 'zero' => [0];
        yield 'após o último' => [7];
        yield 'negativo' => [-1];
    }

    #[Test]
    #[DataProvider('codigosInexistentes')]
    public function motivoForaDoDominioNaoEAceito(int $codigo): void
    {
        self::assertNull(MotivoAfastamento::tryFrom($codigo));
    }

    #[Test]
    public function todoMotivoTemRotulo(): void
    {
        foreach (MotivoAfastamento::cases() as $motivo) {
            self::assertNotSame('', $motivo->rotulo());
        }
    }
}
