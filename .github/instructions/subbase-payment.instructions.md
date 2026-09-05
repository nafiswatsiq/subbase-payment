---
description: "Use when designing or implementing the Subbase Payment plugin, payment gateways, Laravel package installation, Composer optional SDKs, subscription payment migrations, webhooks, events, or Filament UI extensions."
name: "Subbase Payment Plugin"
---
# Subbase Payment Plugin

## Goals and Boundaries

Build `nafiswatsiq/subbase-payment` as a Laravel/Filament package separate from `nafiswatsiq/subbase`. The `subbase` core must not depend on payment SDKs or this payment package. Every change must preserve `subbase` installations that do not use payments.

Primary goals:

- Isolate gateways behind replaceable drivers.
- Install a gateway SDK only when that driver is selected.
- Provide concise installation and configuration for Laravel users.
- Keep package code safe from direct changes inside the `vendor` directory.
- Integrate with `subbase` models, subscription lifecycle, database, and Filament through documented extension points.

## Implementation Order

Work in the following small slices and validate each slice before continuing:

1. **Package foundation**
   - Add a separate package `composer.json` with `nafiswatsiq/subbase` as a required dependency.
   - Register the service provider through Composer auto-discovery.
   - Put code under the `Nafiswatsiq\\SubbasePayment` namespace and separate `config`, `database`, `routes`, and `src`.
   - Provide `SubbasePaymentPlugin` to register Filament resources (e.g. `SubscriptionPaymentResource`).
   - Use `SubbasePaymentPermission::allows()` for authorization checks in Filament resources.
   - Add the test runner and installation documentation early.

2. **Contract and manager**
   - Create `PaymentGatewayInterface` with `charge`, `cancel`, and `handleWebhook` operations.
   - Use explicit DTOs/value objects or return types for payment results; do not expose raw SDK objects to package callers.
   - `PaymentManager` must select the driver from `config('subbase-payment.driver')`, resolve registered drivers, and throw a clear exception for unknown or unconfigured drivers.
   - Provide driver registration so additional gateways do not require changes to the manager.

3. **Gateway drivers**
   - Implement `MidtransGateway` as the first driver only after the contract is stable.
   - Isolate all SDK calls inside the driver; `subbase` `Plan` and `Subscription` models must not know about Midtrans details.
   - Read price, currency, plan, and subscription data through the existing `subbase` model APIs. Do not assume a single currency.
   - Store transaction identifiers and checkout URLs as stable data, not SDK objects.
   - Declare gateway SDKs as Composer `suggest`/optional dependencies where appropriate; document the package required by each driver.
   - Stripe uses Checkout Sessions (hosted payment page) with HMAC-SHA256 webhook signature verification; amounts are converted to minor units with zero-decimal currency handling.

4. **Smart installer**
   - Create `InstallPaymentCommand` with a `--driver` option and a non-interactive mode suitable for CI.
   - Validate the driver before changing application files.
   - Publish config and migrations without overwriting user files.
   - Update `.env` idempotently: do not duplicate keys, print secrets, or overwrite existing values without explicit confirmation.
   - Composer dependency installation must use a traceable process and report failures. Avoid background processes that make Artisan report success before Composer finishes; if background execution is retained, wait for the result and propagate the exit code.
   - `php artisan subbase-payment:install --help` and `--no-interaction` must work without prompts.

5. **Database and lifecycle**
   - Add package migrations for the `subbase`-configured subscription table with nullable `gateway_driver`, `gateway_transaction_id`, and `payment_status` columns and relevant indexes.
   - Respect table and model names from `subbase` configuration; do not hard-code application models.
   - Migrations must be reversible and safe to publish repeatedly.
   - Define a minimal payment state machine (`pending`, `paid`, `failed`, `canceled`, or documented equivalents) and explicitly map gateway states to subscription states.
   - Event listeners must be idempotent, must not create duplicate charges, and must not activate a subscription before payment is verified.
   - Dispatch slow external gateway operations through queues; avoid unnecessary network calls inside database transactions.

6. **Webhook**
   - Provide a configurable webhook route/controller that does not depend on a Filament session.
   - Verify the gateway signature/authenticity before processing the payload.
   - Validate payloads, persist the event or transaction ID for idempotency, and use a database transaction when updating a subscription.
   - Do not trust client status or redirect URLs. Only a verified webhook may change a subscription to active/paid.
   - Return appropriate HTTP statuses and never expose credentials or sensitive payloads in logs.
   - Test valid payloads, invalid signatures, missing transactions, webhook retries, and invalid state transitions.

7. **Filament extension**
   - Integrate through hooks/extension points available on `subbase` resources/pages rather than copying or modifying core resources.
   - Show actions such as checking payment status only when the payment package and driver are available.
   - Ensure actions honor Filament authorization and never activate a subscription locally without gateway verification.
   - If no core extension point exists, add a small backward-compatible extension API to `subbase` and document it as a dependency contract.
   - The UI must continue working when no SDK or driver is active.

8. **Service provider and wiring**
   - Create `PaymentServiceProvider` for config, migrations, routes, commands, event listeners, and the manager binding.
   - Bind the manager as a singleton only for safely shareable dependencies; do not store mutable transaction state in the singleton.
   - Separate registration from boot logic and respect `runningInConsole()` for console-only assets/commands.
   - Ensure the package can be removed or disabled without breaking core subscription features.

## Integration Contracts to Preserve

- Use table and model names from `subbase` configuration, especially the user-replaceable subscription model.
- Do not depend on columns or events that do not exist in `subbase`; create an official contract/event or a clear fallback when needed.
- Do not call a gateway when a subscription lacks a valid plan/price/currency.
- All charge/cancel/webhook operations must be traceable by transaction ID, but secrets and tokens must never be logged.
- Callers must be able to distinguish configuration failures, network failures, and payment statuses.

## Minimum Validation

Before declaring the feature complete, run the narrowest checks first, followed by the full package test suite:

- PHP lint/static analysis and syntax checks;
- unit tests for the manager, driver mapping, DTOs, and state mapping;
- installer command tests for valid/invalid drivers, idempotent `.env` updates, and optional dependencies;
- migration tests on a fresh database and a database with existing subscriptions;
- webhook feature tests for signatures, idempotency, and authorization;
- Filament integration tests for actions shown/hidden according to configuration;
- `composer validate` and package installation tests from an example Laravel application.

Document the commands run and every external dependency that must be simulated or mocked.

## User Documentation

The package README must explain basic installation, driver selection, required SDK packages, `.env` keys, migrations, webhook URL, queue/worker setup, event lifecycle, how to add a driver, and how to disable payments without deleting subscription data. Mark features that require gateway-specific configuration and never include real secrets in examples.
