<?php

namespace App\Services\WB;

use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface;

class Wildberries
{
    private const DEFAULT_HEADER = [
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
    ];

    private array $urls = [
        'standard'  => 'https://suppliers-api.wildberries.ru/',
        'statistic' => 'https://statistics-api.wildberries.ru/',
        'advert'    => 'https://advert-api.wb.ru/',
    ];

    private array $keys = [
          'standard'  => null,
          'statistic' => null,
          'advert'    => null,
    ];

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    /**
     * Получение информации по номенклатурам, их ценам, скидкам и промокодам. Если не указывать фильтры, вернётся весь товар.
     *
     * @param int $quantity 2 - товар с нулевым остатком, 1 - товар с ненулевым остатком, 0 - товар с любым остатком
     * @return ResponseInterface
     * @throws Exception
     */
    public function getInfo(int $quantity = 0): ResponseInterface
    {
        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['standard']],
            'query'   => [
                'quantity' => $quantity,
            ]
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['standard'].'public/api/v1/info', $props);
    }

    /**
     * Возвращает список товаров поставщика с их остатками
     *
     * @param int $skip Задает сколько записей пропустить (для пагинации)
     * @param int $take Задает сколько записей выдать (для пагинации)
     * @param string|null $search Выполняет поиск по всем полям таблицы
     * @throws Exception
     */
    public function getStocks(int $skip, int $take, string $search = null): ResponseInterface
    {
        $props = [
            'headers'  => self::DEFAULT_HEADER + ['Authorization' => $this->keys['standard']],
            'query'    => [
                'skip' => $skip,
                'take' => $take,
                'search' => $search,
            ]
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['standard'].'api/v2/stocks', $props);
    }

    /**
     * Возвращает список сборочных заданий поставщика.
     *
     * @param int $skip Задает сколько записей пропустить (для пагинации)
     * @param int $take Задает сколько записей выдать (для пагинации)
     * @param DateTime $date_start С какой даты вернуть сборочные задания (заказы) (в формате RFC3339)
     * @param DateTime|null $date_end По какую дату вернуть сборочные задания (заказы) (в формате RFC3339)
     * @param int|null $status Заказы какого статуса нужны
     * @param int|null $id Идентификатор сборочного задания, если нужно получить данные по какому-то определенному заказу.
     * @param bool $is_UTC
     * @return ResponseInterface
     * @throws Exception
     */
    public function getOrders(
        int $skip,
        int $take,
        DateTime $date_start,
        DateTime $date_end = null,
        int $status = null,
        int $id = null,
        bool $is_UTC = false): ResponseInterface
    {
        $date_start = $date_start->format($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s');
        $date_end = $date_end ?->format($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s');

        return (new WildberriesRequest)->makeRequest(
            'api/v2/orders',
            array_diff(compact('skip', 'take', 'date_start', 'date_end', 'status', 'id'), [''])
        );
    }

//    /**
//     * Возвращает список сборочных заданий поставщика.
//     *
//     * @param string $status Заказы какого статуса нужны
//     * @return mixed
//     */
//    public function getSupplies(string $status = 'ACTIVE'): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'api/v2/supplies',
//            compact('status'),
//        )))->data;
//    }

    /**
     * Возвращает список сборочных заданий поставщика.
     *
     * @return mixed
     */
//    public function getWarehouses(): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'api/v2/warehouses'
//        )))->data;
//    }

    /**
     * Возвращает список товаров поставщика с их остатками
     *
     * @param DateTime $dateFrom Дата в формате RFC3339. Можно передать дату или дату со временем.
     *                           Время можно указывать с точностью до секунд или миллисекунд.
     *                           Литера Z в конце строки означает, что время передается в UTC-часовом поясе.
     *                           При ее отсутствии время считается в часовом поясе МСК (UTC+3).
     *
     *                           Примеры:
     *                             - 2019-06-20
     *                             - 2019-06-20T00:00:00Z
     *                             - 2019-06-20T23:59:59
     *                             - 2019-06-20T00:00:00.12345Z
     *                             - 2019-06-20T00:00:00.12345
     *                             - 2017-03-25T00:00:00
     * @param bool $is_UTC
     * @return ResponseInterface
     * @throws Exception
     */
    public function getSupplierIncomes(DateTime $dateFrom, bool $is_UTC = false): ResponseInterface
    {
        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['statistic']],
            'query'   => [
                'dateFrom' => $dateFrom->format($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s')
            ]
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['statistic'].'api/v1/supplier/incomes', $props);
    }

    /**
     * Продажи.
     * Важно: гарантируется хранение данных по заказам не более 90 дней от даты заказа. Данные обновляются раз в 30 минут. Точное время обновления информации в сервисе можно увидеть в поле lastChangeDate.
     * Для идентификации товаров из одного заказа, а также продаж по ним, следует использовать поле gNumber (строки с одинаковым значением этого поля относятся к одному заказу) и номер уникальной позиции в заказе odid (rid).
     * Для расчета окончательной стоимости (суммы к оплате) следует пользоваться следующей формулой:
     * PriceWithDiscount = totalPrice * ((100 – discountPercent)/100) * ((100 – promoCodeDiscount)/100) *((100 – spp)/100)
     *
     * @param DateTime $dateFrom Дата в формате RFC3339. Можно передать дату или дату со временем.
     *                           Время можно указывать с точностью до секунд или миллисекунд.
     *                           Литера Z в конце строки означает, что время передается в UTC-часовом поясе.
     *                           При ее отсутствии время считается в часовом поясе МСК (UTC+3).
     *
     *                           Примеры:
     *                             - 2019-06-20
     *                             - 2019-06-20T00:00:00Z
     *                             - 2019-06-20T23:59:59
     *                             - 2019-06-20T00:00:00.12345Z
     *                             - 2019-06-20T00:00:00.12345
     *                             - 2017-03-25T00:00:00
     * @param int $flag Default: 0
     *                 Если параметр flag=0 (или не указан в строке запроса), при вызове API возвращаются данные,
     *                 у которых значение поля lastChangeDate (дата время обновления информации в сервисе) больше
     *                 или равно переданному значению параметра dateFrom. При этом количество возвращенных строк данных
     *                 варьируется в интервале от 0 до примерно 100 000.
     *                 Если параметр flag=1, то будет выгружена информация обо всех заказах или продажах с датой,
     *                 равной переданному параметру dateFrom (в данном случае время в дате значения не имеет).
     *                 При этом количество возвращенных строк данных будет равно количеству всех заказов или продаж,
     *                 сделанных в указанную дату, переданную в параметре dateFrom.
     * @param bool $is_UTC
     * @return ResponseInterface
     * @throws Exception
     */
    public function getSupplierSales(DateTime $dateFrom, int $flag = 0, bool $is_UTC = false): ResponseInterface
    {
        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['statistic']],
            'query'   => [
                'flag' => $flag,
                'dateFrom' => $dateFrom->format($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s')
            ]
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['statistic'].'api/v1/supplier/sales', $props);

//        $params = compact('flag');
//        $params['dateFrom'] = $dateFrom->format($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s');
//        return (new WildberriesData(
//            $this->getResponse('api/v1/supplier/sales',
//                $params,
//                true
//            )))->data;
    }

    /** MBA-8 ~5m
     * С помощью данного метода можно получить список категорий товаров по текстовому фильтру (названию категории).
     *
     * @param string|null $name Поиск по названию категории
     *                          Example: name=3D
     * @param int|null $top Количество запрашиваемых значений
     *                      Example: top=50
     * @throws Exception
     */
    public function getObjectAll(string $name = null, int $top = null): ResponseInterface
    {
        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['standard']],
            'query'   => ['top' => $top]
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['standard'].'content/v1/object/all', $props);
    }

    public function getCountAdv(): mixed
    {
        //TODO
//        return (new WildberriesRequest)->makeRequest($this->getResponse('adv/v0/count'));
    }

    /**
     * Список РК
     * Получение списка РК поставщика
     *
     * @param int|null $status
     * @param int|null $type
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $order
     * @param string|null $direction
     * @throws Exception
     */
    public function getAdverts(
        int $status = null,
        int $type = null,
        int $limit = null,
        int $offset = null,
        string $order = null,
        string $direction = null,
    ): ResponseInterface {

        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['advert']],
            'query'   => [
                'status' => $status,
                'type'   => $type,
                'limit'  => $limit,
                'offset' => $offset,
                'order'  => $order,
                'direction' => $direction,
            ],
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['advert'].'adv/v0/adverts', $props);
    }

    /**
     * Информация о РК
     * Получение информации об одной РК
     *
     * @param int id
     * @return ResponseInterface
     * @throws Exception
     */
    public function getAdvert(int $id): ResponseInterface
    {
        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['advert']],
            'query'   => ['id' => $id],
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['advert'].'adv/v0/advert', $props);
    }

    /**
     * Список ставок
     * Получение списка РК поставщика
     *
     * @param int type
     * @param int param
     * @throws Exception
     */
    public function getCpm(int $type, int $param): ResponseInterface
    {
        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['advert']],
            'query'   => [
                'type'  => $type,
                'param' => $param,
            ],
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['advert'].'adv/v0/cpm', $props);
    }

    /** MBA-6 ~5m
     * Метод позволяет получить список НМ и список ошибок которые произошли во время создания КТ.
     * ВАЖНО: Для того чтобы убрать НМ из ошибочных, надо повторно сделать запрос с исправленными ошибками на создание КТ.
     *
     * @return mixed
     */
//    public function getCardsErrorList(): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/cards/error/list'
//        )))->data;
//    }

    /** MBA-9 ~5m
     * С помощью данного метода можно получить список всех родительских категорий товаров.
     *
     * @return mixed
     */
    public function getObjectParentAll(): mixed
    {
        return (new WildberriesData($this->getResponse(
            'content/v1/object/parent/all'
        )))->data;
    }

    /** MBA-10 ~ 5m
     * С помощью данного метода можно получить список характеристик которые можно или нужно заполнить при создании КТ в подкатегории определенной родительской категории.
     *
     * @param string|null $name Поиск по родительской категории
     *                          Example: name=Косухи
     * @return mixed
     */
//    public function getObjectCharacteristicsListFilter(string $name = null): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/object/characteristics/list/filter',
//            array_diff(compact('name',), [''])
//        )))->data;
//    }

    /** MBA-11 ~5m
     * С помощью данного метода можно получить список характеристик которые можно или нужно заполнить при создании КТ для определенной категории товаров.
     *
     * @param string $objectName Поиск по наименованию категории
     *                           Example: Носки
     * @return mixed
     */
//    public function getObjectCharacteristicsObjectName(string $objectName): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/object/characteristics/' . $objectName,
//            [
//                'objectName' => $objectName,
//            ]
//        )))->data;
//    }

    /** MBA-12 ~5m
     * Получение значения характеристики цвет.
     *
     * @return mixed
     */
//    public function getDirectoryColors(): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/directory/colors'
//        )))->data;
//    }

    /** MBA-13 ~5m
     * Получение значения характеристики пол.
     *
     * @return mixed
     */
//    public function getDirectoryKinds(): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/directory/kinds'
//        )))->data;
//    }

    /** MBA-14 ~5m
     * Получение значения характеристики Страна Производства.
     *
     * @return mixed
     */
//    public function getDirectoryCountries(): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/directory/countries'
//        )))->data;
//    }

    /** MBA-15 ~5m
     * Получение значения характеристики Коллекции.
     *
     * @param int|null $top Количество запрашиваемых значений (максимум 5000)
     *                      Example: top=1
     * @param string|null $pattern Поиск по наименованию значения характеристики
     *                    Example: pattern=зим
     * @return mixed
     */
//    public function getDirectoryCollections(int $top = null, string $pattern = null): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/directory/collections',
//            array_diff(compact('top', 'pattern'), [''])
//        )))->data;
//    }

    /** MBA-16 ~5m
     * Получение значения характеристики Сезон.
     *
     * @return mixed
     */
//    public function getDirectorySeasons(): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/directory/seasons'
//        )))->data;
//    }

    /** MBA-17 ~5m
     * Получение значения характеристики Комплектация.
     *
     * @param int|null $top Количество запрашиваемых значений (максимум 5000)
     *                      Example: top=1
     * @param string|null $pattern Поиск по наименованию значения характеристики
     *                             Example: pattern=USB
     * @return mixed
     */
//    public function getDirectoryContents(int $top = null, string $pattern = null): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/directory/contents',
//            array_diff(compact('top', 'pattern'), [''])
//        )))->data;
//    }

    /** MBA-18 ~5m
     * Получение значения характеристики Состав.
     *
     * @param int|null $top Количество запрашиваемых значений (максимум 5000)
     *                      Example: top=1
     * @param string|null $pattern Поиск по наименованию значения характеристики
     *                             Example: pattern=хлопок
     * @return mixed
     */
//    public function getDirectoryConsists(int $top = null, string $pattern = null): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/directory/consists',
//            array_diff(compact('top', 'pattern'), [''])
//        )))->data;
//    }

    /** MBA-19 ~5m
     * Получение значения характеристики Бренд.
     *
     * @param int|null $top Количество запрашиваемых значений (максимум 5000)
     *                      Example: top=1
     * @param string|null $pattern Поиск по наименованию значения характеристики
     *                             Example: pattern=USB
     * @return mixed
     */
//    public function getDirectoryBrands(int $top = null, string $pattern = null): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/directory/brands',
//            array_diff(compact('top', 'pattern'), [''])
//        )))->data;
//    }

    /** MBA-20 ~5m
     * С помощью данного метода можно получить список ТНВЭД кодов по имени категории и фильтру по тнвэд коду.
     *
     * @param string|null $objectName Поиск по наименованию категории
     *                    Example: objectName=Блузки
     * @param string|null $tnvedsLike Поиск по коду ТНВЭД
     *                                Example: tnvedsLike=4203100001
     * @return mixed
     */
//    public function getDirectoryTnved(string $objectName = null, string $tnvedsLike = null): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            'content/v1/directory/tnved',
//            array_diff(compact('objectName', 'tnvedsLike'))
//        )))->data;
//    }

    /** MBA-28 ~5m
     * Возвращает штрихкод поставки в заданном формате: pdf или svg.
     * Штрихкод генерируется в формате code-128.
     * Массив байтов передаётся закодированным в base64.
     *
     * @param string $id Идентификатор поставки.
     *                   Example: WB-GI-1234567
     * @param string $type Формат штрихкода.
     *                     Enum: "pdf" "svg"
     * @return mixed
     */
//    public function getSuppliesIdBarcode(string $id, string $type): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            '/api/v2/supplies/' . $id . '/barcode',
//            [
//                'type' => $type,
//            ]
//        )))->data;
//    }

    /** MBA-29 ~5m
     * Список заказов, закреплённых за поставкой.
     *
     * @param string $id Идентификатор поставки.
     *                   Example: WB-GI-1234567
     * @return mixed
     */
//    public function getSuppliesIdOrders(string $id): mixed
//    {
//        return (new WildberriesData($this->getResponse(
//            '/api/v2/supplies/' . $id . '/orders'
//        )))->data;
//    }

    /** MBA-22 ~5m
     * Склад. Данные обновляются раз в сутки. Сервис статистики не хранит историю остатков товаров, поэтому получить данные об остатках товаров на прошедшую, не сегодняшнюю, дату - невозможно.
     *
     * @param DateTime $dateFrom Дата в формате RFC3339. Можно передать дату или дату со временем.
     *                           Время можно указывать с точностью до секунд или миллисекунд.
     *                           Литера Z в конце строки означает, что время передается в UTC-часовом поясе.
     *                           При ее отсутствии время считается в часовом поясе МСК (UTC+3).
     *
     *                           Примеры:
     *                             - 2019-06-20
     *                             - 2019-06-20T00:00:00Z
     *                             - 2019-06-20T23:59:59
     *                             - 2019-06-20T00:00:00.12345Z
     *                             - 2019-06-20T00:00:00.12345
     *                             - 2017-03-25T00:00:00
     * @param bool $is_UTC
     * @return ResponseInterface
     * @throws Exception
     */
    public function getSupplierStocks(DateTime $dateFrom, bool $is_UTC = false): ResponseInterface
    {
        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['statistic']],
            'query'   => [
                'dateFrom' => $dateFrom->format($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s'),
            ]
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['statistic'].'api/v1/supplier/stocks', $props);
    }

    /** MBA-23 ~5m
     * Заказы.
     * Важно: гарантируется хранение данных по заказам не более 90 дней от даты заказа. Данные обновляются раз в 30 минут. Точное время обновления информации в сервисе можно увидеть в поле lastChangeDate.
     *  Для идентификации товаров из одного заказа, а также продаж по ним, следует использовать поле gNumber (строки с одинаковым значением этого поля относятся к одному заказу) и номер уникальной позиции в заказе odid (rid).
     *
     * @param DateTime $dateFrom Дата в формате RFC3339. Можно передать дату или дату со временем.
     *                           Время можно указывать с точностью до секунд или миллисекунд.
     *                           Литера Z в конце строки означает, что время передается в UTC-часовом поясе.
     *                           При ее отсутствии время считается в часовом поясе МСК (UTC+3).
     *
     *                           Примеры:
     *                             - 2019-06-20
     *                             - 2019-06-20T00:00:00Z
     *                             - 2019-06-20T23:59:59
     *                             - 2019-06-20T00:00:00.12345Z
     *                             - 2019-06-20T00:00:00.12345
     *                             - 2017-03-25T00:00:00
     * @param int $flag Default: 0
     *                 Если параметр flag=0 (или не указан в строке запроса), при вызове API возвращаются данные,
     *                 у которых значение поля lastChangeDate (дата время обновления информации в сервисе) больше
     *                 или равно переданному значению параметра dateFrom. При этом количество возвращенных строк данных
     *                 варьируется в интервале от 0 до примерно 100 000.
     *                 Если параметр flag=1, то будет выгружена информация обо всех заказах или продажах с датой,
     *                 равной переданному параметру dateFrom (в данном случае время в дате значения не имеет).
     *                 При этом количество возвращенных строк данных будет равно количеству всех заказов или продаж,
     *                 сделанных в указанную дату, переданную в параметре dateFrom.
     * @param bool $is_UTC
     * @return ResponseInterface
     * @throws Exception
     */
    public function getSupplierOrders(DateTime $dateFrom, int $flag = 0, bool $is_UTC = false): ResponseInterface
    {
        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['statistic']],
            'query'   => [
                'flag' => $flag,
                'dateFrom' => $dateFrom->format($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s'),
            ]
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['statistic'].'api/v1/supplier/orders', $props);
    }

    /** MBA-25 ~5m
     * Отчет о продажах по реализации.
     * В отчете доступны данные за последние 3 месяца.
     * В случае отсутствия данных за указанный период метод вернет null.
     *
     * @param DateTime $dateFrom Дата в формате RFC3339. Можно передать дату или дату со временем.
     *                           Время можно указывать с точностью до секунд или миллисекунд.
     *                           Литера Z в конце строки означает, что время передается в UTC-часовом поясе.
     *                           При ее отсутствии время считается в часовом поясе МСК (UTC+3).
     *
     *                           Примеры:
     *                             - 2019-06-20
     *                             - 2019-06-20T00:00:00Z
     *                             - 2019-06-20T23:59:59
     *                             - 2019-06-20T00:00:00.12345Z
     *                             - 2019-06-20T00:00:00.12345
     *                             - 2017-03-25T00:00:00
     * @param DateTime $dateTo Конечная дата отчета
     * @param int $limit Default: 0
     *                   Максимальное количество строк отчета, возвращаемых методом. Не может быть более 100 000.
     * @param int $rrdid Уникальный идентификатор строки отчета. Необходим для получения отчета частями.
     *                   Загрузку отчета нужно начинать с rrdid = 0 и при последующих вызовах API передавать
     *                   в запросе значение rrd_id из последней строки, полученной в результате предыдущего вызова.
     *                   Таким образом для загрузки одного отчета может понадобиться вызывать API до тех пор, пока
     *                   количество возвращаемых строк не станет равным нулю.
     * @param bool $is_UTC
     * @return ResponseInterface
     * @throws Exception
     */
    public function getSupplierReportDetailByPeriod(
        DateTime $dateFrom,
        DateTime $dateTo,
        int $rrdid = 0,
        int $limit = 0,
    ): ResponseInterface {

        $props = [
            'headers' => self::DEFAULT_HEADER + ['Authorization' => $this->keys['statistic']],
            'query'   => [
                'dateFrom' => $dateFrom->format('Y-m-d'),//($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s'),
                'dateTo'   => $dateTo->format('Y-m-d'),//->format($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s'),
                'limit'    => $limit,
                'rrdid'    => $rrdid,
            ]
        ];

        return (new WildberriesRequest)
            ->makeRequest($this->urls['statistic'].'api/v1/supplier/reportDetailByPeriod', $props);
    }

    /** MBA-26 ~5m
     * Отчет по КиЗам.
     * КИЗ — это контрольный идентификационный знак. Он представляет собой маркировку, похожую на QR-код, который проставляется на некоторых товарах. Его можно отсканировать с помощью специального приложения и убедиться в качестве и оригинальности товара.
     * Сканирование КИЗов доступно как продавцу, так и покупателю, а также всем остальным участникам процесса продажи.
     *
     * @param DateTime $dateFrom Дата в формате RFC3339. Можно передать дату или дату со временем.
     *                           Время можно указывать с точностью до секунд или миллисекунд.
     *                           Литера Z в конце строки означает, что время передается в UTC-часовом поясе.
     *                           При ее отсутствии время считается в часовом поясе МСК (UTC+3).
     *
     *                           Примеры:
     *                             - 2019-06-20
     *                             - 2019-06-20T00:00:00Z
     *                             - 2019-06-20T23:59:59
     *                             - 2019-06-20T00:00:00.12345Z
     *                             - 2019-06-20T00:00:00.12345
     *                             - 2017-03-25T00:00:00
     * @param bool $is_UTC
     * @return array
     */
//    public function getSupplierExciseGoods(DateTime $dateFrom, bool $is_UTC = false): array
//    {
//        return (new WildberriesData($this->getResponse(
//            'api/v1/supplier/excise-goods',
//            [
//                'dateFrom' => $dateFrom->format($is_UTC ? 'Y-m-d\TH:i:s\Z' : 'Y-m-d\TH:i:s'),
//            ],
//            true
//        )))->data;
//    }
}
