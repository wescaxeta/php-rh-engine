<?php declare(strict_types=1);

namespace RhEngine\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RhEngine\Enum\SituacaoDiaria;
use RhEngine\Exception\TransicaoInvalidaException;
use RhEngine\Model\Diaria\FluxoAprovacao;

#[CoversClass(FluxoAprovacao::class)]
#[CoversClass(SituacaoDiaria::class)]
#[CoversClass(TransicaoInvalidaException::class)]
final class FluxoAprovacaoDiariaTest extends TestCase
{
    private FluxoAprovacao $fluxo;

    protected function setUp(): void
    {
        $this->fluxo = new FluxoAprovacao();
    }

    #[Test]
    public function percorreFluxoCompletoAtePaga(): void
    {
        $situacao = SituacaoDiaria::Solicitada;

        $situacao = $this->fluxo->avancar($situacao);
        self::assertSame(SituacaoDiaria::AprovadaNucleo, $situacao);

        $situacao = $this->fluxo->avancar($situacao);
        self::assertSame(SituacaoDiaria::Aprovada, $situacao);

        $situacao = $this->fluxo->avancar($situacao);
        self::assertSame(SituacaoDiaria::Paga, $situacao);
    }

    /**
     * @return iterable<string, array{SituacaoDiaria}>
     */
    public static function situacoesFinais(): iterable
    {
        yield 'paga' => [SituacaoDiaria::Paga];
        yield 'rejeitada' => [SituacaoDiaria::Rejeitada];
    }

    /**
     * @return iterable<string, array{SituacaoDiaria}>
     */
    public static function situacoesIntermediarias(): iterable
    {
        yield 'solicitada' => [SituacaoDiaria::Solicitada];
        yield 'aprovada núcleo' => [SituacaoDiaria::AprovadaNucleo];
        yield 'aprovada' => [SituacaoDiaria::Aprovada];
    }

    #[Test]
    #[DataProvider('situacoesFinais')]
    public function situacaoFinalNaoAvanca(SituacaoDiaria $situacao): void
    {
        self::assertFalse($this->fluxo->podeAvancar($situacao));

        $this->expectException(TransicaoInvalidaException::class);
        $this->expectExceptionMessage('não permite avanço');

        $this->fluxo->avancar($situacao);
    }

    #[Test]
    #[DataProvider('situacoesIntermediarias')]
    public function situacaoIntermediariaPodeAvancar(SituacaoDiaria $situacao): void
    {
        self::assertTrue($this->fluxo->podeAvancar($situacao));
    }

    #[Test]
    public function rejeitaAntesDaAprovacaoFinal(): void
    {
        self::assertSame(SituacaoDiaria::Rejeitada, $this->fluxo->rejeitar(SituacaoDiaria::Solicitada));
        self::assertSame(SituacaoDiaria::Rejeitada, $this->fluxo->rejeitar(SituacaoDiaria::AprovadaNucleo));
    }

    #[Test]
    public function naoRejeitaDiariaJaAprovada(): void
    {
        $this->expectException(TransicaoInvalidaException::class);
        $this->expectExceptionMessage('não pode ser rejeitada');

        $this->fluxo->rejeitar(SituacaoDiaria::Aprovada);
    }

    #[Test]
    public function todaSituacaoTemRotulo(): void
    {
        foreach (SituacaoDiaria::cases() as $situacao) {
            self::assertNotSame('', $situacao->rotulo());
        }
    }
}
