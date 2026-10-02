# Changelog

Formato baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/),
versionamento segundo [SemVer](https://semver.org/lang/pt-BR/).

## [Unreleased]

### Adicionado
- `Money`: value object monetário em centavos (inteiro), imutável.
- `Diaria\FluxoAprovacao`: máquina de estados com avanço e rejeição.
- Período concessivo em `PeriodoAquisitivo` (`fimPeriodoConcessivo`, `concessivoExpirado`).
- Exceções de domínio específicas com named constructors.
- PHPStan (nível max + strict rules), PHP-CS-Fixer (PER-CS 2.0), Docker, Makefile.
- CI com jobs separados de qualidade e testes (matriz PHP 8.4/8.5) e relatório de cobertura.

### Alterado
- `Diaria\Calculator::calcularValor` recebe e retorna `Money` em vez de `float`.
- Fluxo de aprovação saiu de `Diaria\Calculator` para `Diaria\FluxoAprovacao` (SRP).
- `RhException` passou a ser abstrata (base da hierarquia de exceções).
- Testes reescritos com atributos do PHPUnit (`#[Test]`, `#[DataProvider]`, `#[CoversClass]`).

### Corrigido
- Período aquisitivo para admissões no fim do mês (ex.: 31/01 e 29/02) — `modify('+N months')` estourava para o mês seguinte.
- Referência anterior à admissão agora lança exceção em vez de retornar período incorreto.

## [0.1.0]

### Adicionado
- Regras iniciais de férias, diárias e afastamentos.
