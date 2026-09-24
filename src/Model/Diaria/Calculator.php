<?php declare(strict_types=1);

namespace RhEngine\Model\Diaria;

use RhEngine\Enum\SituacaoDiaria;
use RhEngine\Exception\RhException;

class Calculator
{
    private const PERCENTUAL_SEM_PERNOITE = 0.5;

    public function calcularValor(
        float $valorDiariaPadrao,
        int $quantidadeDias,
        bool $comPernoite
    ): float {
        $valorUnitario = $comPernoite
            ? $valorDiariaPadrao
            : $valorDiariaPadrao * self::PERCENTUAL_SEM_PERNOITE;

        return round($valorUnitario * $quantidadeDias, 2);
    }

    public function proximaSituacao(SituacaoDiaria $situacaoAtual): SituacaoDiaria
    {
        return match ($situacaoAtual) {
            SituacaoDiaria::Solicitada     => SituacaoDiaria::AprovadaNucleo,
            SituacaoDiaria::AprovadaNucleo => SituacaoDiaria::Aprovada,
            SituacaoDiaria::Aprovada       => SituacaoDiaria::Paga,
            default => throw new RhException(sprintf(
                'Situação "%s" não permite avanço no fluxo.',
                $situacaoAtual->value
            )),
        };
    }

    public function podeAvancar(SituacaoDiaria $situacao): bool
    {
        return in_array($situacao, [
            SituacaoDiaria::Solicitada,
            SituacaoDiaria::AprovadaNucleo,
            SituacaoDiaria::Aprovada,
        ], strict: true);
    }
}
