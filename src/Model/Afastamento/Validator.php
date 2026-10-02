<?php declare(strict_types=1);

namespace RhEngine\Model\Afastamento;

use DateTimeImmutable;
use RhEngine\Enum\MotivoAfastamento;
use RhEngine\Exception\DadoInvalidoException;

final class Validator
{
    /**
     * O motivo já chega validado pelo tipo (enum): valores fora do domínio
     * falham em MotivoAfastamento::from() antes de alcançar esta regra.
     */
    public function validar(
        int $idPessoa,
        MotivoAfastamento $motivo,
        DateTimeImmutable $inicio,
        DateTimeImmutable $fim,
    ): void {
        if ($idPessoa <= 0) {
            throw new DadoInvalidoException('Pessoa inválida para cadastro de afastamento.');
        }

        if ($inicio > $fim) {
            throw new DadoInvalidoException('Data de início não pode ser posterior à data de fim.');
        }
    }

    public function desbloqueiaHorario(MotivoAfastamento $motivo, bool $eComissionado): bool
    {
        return $eComissionado || $motivo->desbloqueiaHorario();
    }
}
