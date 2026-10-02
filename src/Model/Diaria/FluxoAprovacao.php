<?php declare(strict_types=1);

namespace RhEngine\Model\Diaria;

use RhEngine\Enum\SituacaoDiaria;
use RhEngine\Exception\TransicaoInvalidaException;

/**
 * Máquina de estados do fluxo de aprovação de diárias:
 * Solicitada → Aprovada Núcleo → Aprovada → Paga, com rejeição possível
 * enquanto a diária ainda não recebeu a aprovação final.
 */
final class FluxoAprovacao
{
    public function avancar(SituacaoDiaria $situacaoAtual): SituacaoDiaria
    {
        return $situacaoAtual->proxima()
            ?? throw TransicaoInvalidaException::semAvanco($situacaoAtual);
    }

    public function rejeitar(SituacaoDiaria $situacaoAtual): SituacaoDiaria
    {
        if (!$situacaoAtual->podeSerRejeitada()) {
            throw TransicaoInvalidaException::semRejeicao($situacaoAtual);
        }

        return SituacaoDiaria::Rejeitada;
    }

    public function podeAvancar(SituacaoDiaria $situacao): bool
    {
        return !$situacao->ehFinal();
    }
}
