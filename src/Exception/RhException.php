<?php declare(strict_types=1);

namespace RhEngine\Exception;

use RuntimeException;

/**
 * Exceção base do domínio. Toda violação de regra de negócio estende esta classe,
 * permitindo que a aplicação capture erros de domínio com um único catch.
 */
abstract class RhException extends RuntimeException {}
