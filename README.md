# php-rh-engine

Motor de regras para gestão de Recursos Humanos — férias, diárias e afastamentos.

Projeto demonstrativo baseado em experiência real com sistema multi-tenant em produção
(PHP 8.2 + PostgreSQL, 13 estados brasileiros). Recria os domínios de negócio sem nenhum
código ou dado proprietário.

## Funcionalidades

### Férias
- Cálculo de período aquisitivo com base na data de admissão
- Controle de saldo com múltiplos gozos parciais
- Validação de parcelas: mínimo de 10 dias, máximo de 3 parcelas por período
- Detecção de período vencido

### Diárias
- Cálculo de valor com e sem pernoite (50% do valor diário)
- Fluxo de aprovação em múltiplos níveis: Solicitada → Aprovada Núcleo → Aprovada → Paga
- Proteção contra avanço em situações finais (Paga, Rejeitada)

### Afastamentos
- Validação de dados obrigatórios (id_pessoa, datas, motivo via enum)
- Regra de desbloqueio de horário por motivo e por cargo comissionado

## Stack

- PHP 8.2 (tipagem estrita, enums nativos, `readonly`, constructor promotion)
- Arquitetura em camadas: Controller → Model → Entity
- PSR-12 / PSR-4
- PHPUnit 11 para testes de regra de negócio

## Estrutura

```
src/
├── Enum/
│   ├── SituacaoFerias.php
│   ├── SituacaoDiaria.php
│   └── MotivoAfastamento.php
├── Exception/
│   └── RhException.php
└── Model/
    ├── Ferias/
    │   ├── Calculator.php
    │   └── PeriodoAquisitivo.php
    ├── Diaria/
    │   └── Calculator.php
    └── Afastamento/
        └── Validator.php
tests/
└── Unit/
    ├── FeriasCalculatorTest.php
    ├── DiariaCalculatorTest.php
    └── AfastamentoValidatorTest.php
```

## Como rodar

```bash
git clone https://github.com/caxetaw/php-rh-engine
cd php-rh-engine
composer install
```

## Testes

```bash
./vendor/bin/phpunit --testdox
```

## Contexto de origem

Desenvolvido como demonstração de padrões aplicados em sistema agropecuário estadual,
operando em ambiente multi-tenant (fork por estado, 13 UFs), onde regras de negócio
de RH variam conforme legislação estadual e tipo de vínculo funcional.
