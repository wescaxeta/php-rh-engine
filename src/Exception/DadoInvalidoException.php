<?php declare(strict_types=1);

namespace RhEngine\Exception;

/**
 * Entrada que viola uma invariante do domínio (id inválido, intervalo de datas
 * invertido, valor monetário negativo etc.).
 */
final class DadoInvalidoException extends RhException {}
