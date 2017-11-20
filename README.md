### Tech Stack

Framework: Symfony (lighter framework could be used) + FOSRestBundle
Unit tests: PHPSpec
run unit tests:

```vendor/bin/phpspec run```

Functional testing: dredd tests generated from swagger documentation
run dredd tests:

```dredd --language=vendor/bin/dredd-hooks-php --hookfiles=./hooks*.php```

Documentation: Swagger 

### Architecture

DDD - domain login in DomainModel dir. Overkill for this small project but might be usefull in real life large project with
several bussiness rules.

### Must have

- [x] products CRUD
- [x] validation
- [x] product pagination
- [x] default products
- [x] cart create
- [x] cart product crud
- [x] cart limit
- [x] cart total price

### Nice to have

- Security: JWT
- Cart: event sourcing
