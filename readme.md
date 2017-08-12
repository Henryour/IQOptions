## Introduce
Для накатывания БД использовать миграшку:

```php artisan migrate```

## Consumers
Жевалка чисел, полученных из AMQP

```php artisan crawler:run```

Консюмер изменений БД

```php artisan jsonb:read```

## API
Добавление массива чисел в очередь кролика

```POST /api/push-number```
```@param int[] numbers```

Добавление числа в очередь

```GET /api/push-number/{number}```
```@param int number```

## Unit testing
```Tests\Unit\PushNumbersTest```
