### Tech Stack

Framework: Symfony (lighter framework could be used) + FOSRestBundle
Unit tests: PHPSpec
Functional testing: dredd tests generate from swagger dodumentation and DSL tests using codeception
Documentation: Swagger 

### Architecture

DDD - domain login in DomainModel dir. Overkill for this small project but might be usefull in real life large project with
several bussiness rules.

### Must have

- products CRUD
- validation
- product pagination
- default products
- cart create
- cart product crud
- cart limit
- cart total price

### Nice to have

- Security: JWT
- Cart: event sourcing
