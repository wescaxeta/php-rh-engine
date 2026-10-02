<?php declare(strict_types=1);

namespace RhEngine\ValueObject;

use RhEngine\Exception\DadoInvalidoException;

/**
 * Valor monetário em centavos (inteiro), evitando erros de arredondamento de float
 * em cálculos financeiros. Imutável: toda operação retorna uma nova instância.
 */
final readonly class Money
{
    private function __construct(
        public int $centavos,
    ) {
        if ($centavos < 0) {
            throw new DadoInvalidoException('Valor monetário não pode ser negativo.');
        }
    }

    public static function deCentavos(int $centavos): self
    {
        return new self($centavos);
    }

    /**
     * Aceita string decimal com ponto ("1234.56") para não depender de float na entrada.
     */
    public static function deReais(string $valor): self
    {
        if (preg_match('/^(\d+)(?:\.(\d{1,2}))?$/', $valor, $partes) !== 1) {
            throw new DadoInvalidoException(sprintf('Valor monetário inválido: "%s".', $valor));
        }

        $centavos = str_pad($partes[2] ?? '', 2, '0');

        return new self((int) $partes[1] * 100 + (int) $centavos);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function somar(self $outro): self
    {
        return new self($this->centavos + $outro->centavos);
    }

    public function multiplicar(int $fator): self
    {
        return new self($this->centavos * $fator);
    }

    /**
     * Aplica um percentual com arredondamento half-up no centavo.
     */
    public function percentual(int $percentual): self
    {
        return new self(intdiv($this->centavos * $percentual + 50, 100));
    }

    public function igual(self $outro): bool
    {
        return $this->centavos === $outro->centavos;
    }

    public function formatar(): string
    {
        return 'R$ ' . number_format($this->centavos / 100, 2, ',', '.');
    }
}
