<?php declare(strict_types=1);

namespace RhEngine\Model\Afastamento;

use DateTimeImmutable;
use RhEngine\Enum\MotivoAfastamento;
use RhEngine\Exception\RhException;

class Validator
{
    private const MOTIVOS_QUE_DESBLOQUEIAM_HORARIO = [
        MotivoAfastamento::AfastamentoServico,
    ];

    public function validar(
        int $idPessoa,
        MotivoAfastamento $motivo,
        DateTimeImmutable $inicio,
        DateTimeImmutable $fim
    ): void {
        if ($idPessoa <= 0) {
            throw new RhException('Pessoa inválida para cadastro de afastamento.');
        }

        if ($inicio > $fim) {
            throw new RhException('Data de início não pode ser posterior à data de fim.');
        }
    }

    public function desbloqueiaHorario(MotivoAfastamento $motivo, bool $eComissionado): bool
    {
        if ($eComissionado) {
            return true;
        }

        return in_array($motivo, self::MOTIVOS_QUE_DESBLOQUEIAM_HORARIO, strict: true);
    }
}
