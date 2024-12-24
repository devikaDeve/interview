# Skaleet Interview

# Usage

## Prerequisites

- Install PHPStorm.
- Install Docker and/or PHP 8.0 with Composer.

## Installation

- Clone the project: `git clone git@gitlab.com:skaleet-public/interview/interview.git`
- Navigate to the directory: `cd interview`
- Install dependencies: `composer install` or `docker-compose run install`

# Evaluation Criteria
For this exercise, your priority is to develop code that is readable, tested, and maintainable.
We will assess your knowledge of SOLID principles, your skills in automated testing, hexagonal architecture, and tactical patterns of Domain Driven Design.

It is not necessary to have implemented all business rules to pass this test.
We prefer a candidate who does not implement all the rules but delivers code they are proud of.

## Exercise #1: Pay by Card

### Use Case Description

A customer visits a merchant and wishes to make a payment using a credit card. They place the card on the payment terminal, and a request is sent to the system to validate the transaction.

You must implement the business logic when such a request is processed by the system.
Here is the list of business rules to implement:
- The input amount must be strictly positive.
- The currency of the impacted accounts and the payment must be the same.
- The customer's account is debited by the transaction amount.
- The merchant's account is credited with the transaction amount.
- The transaction date is the current date at the time of payment.

**Note:** Amounts are modeled in cents. So, `100` represents `1.00 €`.

### Acceptance Criteria

The account balances are updated based on the transaction parameters. The transaction is recorded along with the movements made on the accounts.

### Example 1

- A customer has a balance of €150.
- A merchant has a balance of €2,500.
- The bank has a balance of €10,000.

The customer makes a payment of €15.36.

|                 | Customer's Account | Merchant's Account |
|-----------------|--------------------|--------------------|
| *initial balance* | €150              | €2,500             |
| *payment*        | -€15.36           | +€15.36            |
| *final balance*  | €134.64           | €2,515.36          |


## Existing Environment

### Available Classes
- The described behavior should be implemented in the `PayByCardCommandHandler::handle()` method.
- The project exposes a CLI command (`PayByCardCli`) to execute the use case.
- The project provides a class `PayByCardCommandHandlerTest` and a `phpunit.xml` file for running unit tests for the project (and calculating code coverage).

### Constraints
- The `PayByCardCommand` class should not be modified.
- The name and parameters provided to the `PayByCardCommandHandler::handle()` method should not be modified.
- The behavior and signature of existing methods in the `InMemoryDatabase` class should not be modified. It is possible to add new methods if necessary.
- Besides the specified classes above, any other class can be modified/added/deleted.

### Run Tests

- `./vendor/bin/phpunit`

or

- `docker-compose run test`

### Execute the Use Case

- `php bin/console.php pay {clientAccountNumber} {amount} {currency} {merchantAccountNumber}`

or

- `docker-compose run console pay {clientAccountNumber} {amount} {currency} {merchantAccountNumber}`

### Manage the Database
This is a database in the form of a file containing serialized PHP objects. The file is named `db` at the root of the project.

Two commands can interact with it:
- `php bin/console.php database:dump` or `docker-compose run console database:dump`: to view its content.
- `php bin/console.php database:clear` or `docker-compose run console database:clear`: to reset it to its initial state.

## Exercise #2: Going Further - Fee Management
This exercise is not to be completed during the technical test. It is for internal training purposes.

### Use Case Description

In addition to the use case developed in exercise 1, add the following business rules:

- A fee of 2% of the transaction amount is applied during the operation. The merchant's account is debited, and the bank's account is credited with the fee amount.
- Fees are capped at a maximum of €3.
- The customer's balance cannot be less than €0.
- The merchant's balance cannot exceed €3,000.
- The merchant's balance cannot be less than -€1,000.

### Example 2

- A customer has a balance of €150.
- A merchant has a balance of €2,500.
- The bank has a balance of €10,000.

The customer makes a payment of €15.36.

|                 | Bank's Account | Customer's Account | Merchant's Account |
|-----------------|----------------|--------------------|--------------------|
| *initial balance* | €10,000        | €150               | €2,500             |
| *payment*        |                | -€15.36            | +€15.36            |
| *fees*           | +€0.31         |                    | -€0.31             |
| *final balance*  | €10,000.31     | €134.64            | €2,515.05          |
