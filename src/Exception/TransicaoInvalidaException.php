<?php declare(strict_types=1);

namespace RhEngine\Exception;

use BackedEnum;

final class TransicaoInvalidaException extends RhException
{
    public static function semAvanco(BackedEnum $situacao): self
    {
        return new self(sprintf('Situação "%s" não permite avanço no fluxo.', $situacao->value));
    }

    public static function semRejeicao(BackedEnum $situacao): self
    {
        return new self(sprintf('Situação "%s" não pode ser rejeitada.', $situacao->value));
    }
}
