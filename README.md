### Tech Stack

Framework: Symfony (lighter framework could be used)
FOSRestBundle  
PHPSpec  
[Dredd](http://dredd.org/en/latest/)  
[Swagger](https://swagger.io/)  
[Prooph](http://getprooph.org/)

### Requirments

- php 7.1
- mysql 5.7

### Instalation

```
composer install 
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### Testing

run DomainModel unit tests:

```vendor/bin/phpspec run```

Functional testing: [Dredd](http://dredd.org/en/latest/) tests generated from swagger documentation,
run dredd tests:

```dredd --language=vendor/bin/dredd-hooks-php --hookfiles=./hooks*.php```

### Documentation

Documentation: Swagger 

generate documentation:

```php bin/console swagger:generate > swagger.json ```

### Architecture

DDD - domain logic in DomainModel dir. Overkill for this small project but might be usefull in real life large project with
several bussiness rules.

Domain logic tests located under src/DomainModel/spec

Product entity is managed by ORM.

Cart aggregate is event sourced using [Prooph](http://getprooph.org/)

### Security

Security was not part of this task. In real life access to cart should be restricted,  eg. OAUTH server could be used for authentication 
