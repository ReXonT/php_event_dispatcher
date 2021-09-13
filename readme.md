# EventDispatcher

### Перед началом
В классе EventDispatcher есть метод initConfig, в котором можно задать свой первоначальный сбор конфига pub/sub
Также в методе asyncCall можно реализовать возможность запустить слушателя при установленном флаге async

### Использование
Для инициализации диспетчера и вызова слушателей на конкретное событие, используйте конструкцию:
```php
EventDispatcher::dispatch(EventInterface $event, bool $async = false);
```
>Реализация этого EventDispatcher основана на PSR-14 с изменениями: 
>typehint object -> на EventInterface; nonstatic -> static методы + async. Для удобства взаимодействия.

Вы можете сами описать ```EventInterface``` или воспользоваться базовым классом 
```...\EventDispatcher\Event```, в котором уже определено всё необходимое (рекомендуется).
### Event
Правильное использование базового класса ```...\EventDispatcher\Event``` для создания нового события:
```php
use ...\EventDispatcher\Event;

class BotStarted extends Event
{
    public function __construct(array $params = [], object $emitter = null, bool $stop_propagation = false)
    {
        parent::__construct(self::class, $params, $emitter, $stop_propagation);
    }
}
```

### Названия событий
Название события === название класса этого события через ```::class```.

Класс ```Listeners``` должен реализовывать интерфейс ```ListenersInitInterface``` с его методом ```init()```. 
Данная связка класс-метод позволит вам добавлять своих слушателей, не пересекаясь с другими модулями системы.

Пример:
```php
use ...\EventDispatcher\Enums\Priority;
use ...\EventDispatcher\EventDispatcher;
use ...\EventDispatcher\Interfaces\ListenersInitInterface;

class Listeners implements ListenersInitInterface
{
    public function init()
    {
        EventDispatcher::attach(WebhookTGReceived::class, MyListener::class, Priority::HIGH);
    }
}
```
Внутри ```init()``` добавляются слушатели в общий конфиг при первой инициализации диспетчера
### Добавление слушателей событий
Вы можете добавлять слушатели как внутри ```init()``` (для стартовой инициализации), так и в любом другом методе.  
Слушатель в конфигуратор задается как **строковое значение** полного *имени класса*, 
либо в виде массива ```['ListenerClass', 'Method']```.  
Рекомендуется использовать свойство ```::class``` для получения имени класса. 

**Доступно 3 варианта добавления слушателя:**

С явным указанием всех параметров и полным названием класса слушателя. 
```php
EventDispatcher::attach(
    WebhookTGReceived::class, 
    My\Own\Listener::class, 
    Priority::HIGH
);
```
Группа слушателей события:
```php
EventDispatcher::group(WebhookTGReceived::class, function (ListenersGroup $group) {
    $group->attach(Namespace\Listener1::class, Priority::MEDIUM);
    $group->attach(Namespace\Listener2::class, Priority::HIGH);
});
```

Группа событий для слушателя:
```php
EventDispatcher::eventsGroup(BotsListener::class, function (EventsGroup $group) {
    $group->attach(TriggerMessageNew::class);
    $group->attach(TriggerCommentNew::class);
});
```

Также, вы можете указывать не только класс слушателя, но и конкретный метод. В поле listener ставьте массив вида ```['ListenerClass', 'Method']```  

Пример:
```php
EventDispatcher::attach(TriggerMessageNew::class, [Test::class, "testCustomMethod"]);
```
В таком случае будет выполнен метод ```testCustomMethod``` класса ```Test```, вместо стандартного ```on```  
**Важно:** кастомный метод должен получать только ```EventInterface $event```

### Обработчики полученных событий (слушатели)
Ваш класс слушателя может реализовывать интерфейс ```ListenerInterface``` с его методом ```on(EventInterface $event)```, или, если указывали кастомный метод при добавлении (см. выше), то имплементация интерфейса не нужна. В него будут приходить все события, на которые данный слушатель подписан.  

Пример:
```php
use ...\EventDispatcher\Interfaces\EventInterface;
use ...\EventDispatcher\Interfaces\ListenerInterface;

class MyListener implements ListenerInterface
{
    public function on(EventInterface $event)
    {
        switch ($event->name()) {
            case BotStarted::class:
                // logic for BotStarted event
                break;
            case TriggerMessageNew::class:
                // logic for TriggerMessageNew event
                break;
        }
    }
}
```
### Общие слушатели
Вы можете задавать слушатели на все события через указание в добавлении слушателя названия события из константы ```Events::ALL```  

Пример:
```php
EventDispatcher::attach(
    Events::ALL, 
    ...\Listener\Logger::class, 
    Priority::HIGH
);
```
### Остановка обработки события
Если вам необходимо закончить обработку события на конкретном слушателе, используйте метод ```$event->stopPropagation(bool $flag = true)```, где ```$event``` - реализация интерфейса ```EventInterface``` или основного базового класса ```...\EventDispatcher\Event```
### Проверка остановленной обработки события
Для проверки флага остановки обработки события используйте метод ```$event->isPropagationStopped()```. В данном случае рекомендуется использовать ```$event``` как реализованный потомок базового класса ```Event```. Но также есть возможность определить своё событие от интерфейса ```StoppableEventInterface``` (PSR-14).
### Приоритет выполнения | Ранг
Вы можете назначать приоритеты выполнения слушателям. Для этого создан ```Enums\Priority``` с константами типа ```integer```. Чем выше число, тем выше будет находиться слушатель в общем стеке. Ранг (приоритет) задается в методе ```attach``` третьим параметром. В примерах выше показана реализация. Если ранг явно не указан - присваивается значение ```Priority::DEFAULT```.