<?php declare(strict_types=1);

namespace RhEngine\Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use RhEngine\Enum\MotivoAfastamento;
use RhEngine\Exception\RhException;
use RhEngine\Model\Afastamento\Validator;

class AfastamentoValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testIdPessoaInvalidoLancaExcecao(): void
    {
        $this->expectException(RhException::class);
        $this->expectExceptionMessage('Pessoa inválida');

        $this->validator->validar(
            idPessoa: 0,
            motivo: MotivoAfastamento::LicencaMedica,
            inicio: new DateTimeImmutable('2026-01-01'),
            fim: new DateTimeImmutable('2026-01-10'),
        );
    }

    public function testDataInicioMaiorQueFimLancaExcecao(): void
    {
        $this->expectException(RhException::class);
        $this->expectExceptionMessage('Data de início não pode ser posterior');

        $this->validator->validar(
            idPessoa: 1,
            motivo: MotivoAfastamento::LicencaMedica,
            inicio: new DateTimeImmutable('2026-01-10'),
            fim: new DateTimeImmutable('2026-01-01'),
        );
    }

    public function testComissionadoSempreDesbloqueiaHorario(): void
    {
        $this->assertTrue(
            $this->validator->desbloqueiaHorario(MotivoAfastamento::LicencaMedica, eComissionado: true)
        );
    }

    public function testAfastamentoServicoDesbloqueiaHorarioNaoComissionado(): void
    {
        $this->assertTrue(
            $this->validator->desbloqueiaHorario(MotivoAfastamento::AfastamentoServico, eComissionado: false)
        );
    }

    public function testLicencaMedicaNaoDesbloqueiaHorarioNaoComissionado(): void
    {
        $this->assertFalse(
            $this->validator->desbloqueiaHorario(MotivoAfastamento::LicencaMedica, eComissionado: false)
        );
    }
}
