# Документация
### API_KEY - 123jnfjakfk124u091fn
## Адрес метода: /api/v1/tender/create.php (Добавить тендер)
Метод:
<pre>POST</pre>
Заголовки: 
<pre>
Authorization: <_API_KEY_>
Content-Type: application/x-www-form-urlencoded
</pre>
Параметры: 
<pre>
<strong>code</strong> (обязательный) - внешний код тендера
Пример: 67180142

<strong>number</strong> (обязательный) - номер тендера
Пример: 412356

<strong>name</strong> (обязательный) - название тендера
Пример: Лабораторный тендер

<strong>status</strong> (обязательный) - статус тендера
Пример: Открыто
</pre>

Пример ответа (JSON):
<pre>
{
    "code": 200,
    "text": "success"
}
</pre>

## Адрес метода: /api/v1/tender/get.php (Получить тендер)
Метод:
<pre>GET</pre>
Заголовки: 
<pre>
Authorization: <_API_KEY_>
</pre>
Параметры: 
<pre>
<strong>code</strong> (обязательный) - внешний код тендера
Пример: 152448048
</pre>

Пример ответа (JSON):
<pre>
{
    "ext_code": "152448048",
    "number": "10917-2",
    "status": "",
    "name": "Запрос скидок наПоставка каната полипроп. крученого диам. 6мм для БФ ОАО Компания",
    "date_change": "14.08.2022 19:23:58"
}
</pre>

## Адрес метода: /api/v1/tender/list.php (Получить список тендеров)
Метод:
<pre>GET</pre>
Заголовки: 
<pre>
Authorization: <_API_KEY_>
</pre>
Параметры: 
<pre>
<strong>codes</strong> (обязательный, массив) - внешний код тендера
Пример: ?codes[]=67180142&codes[]=152432699
</pre>

<pre>
<strong>name</strong> (необязательный) - дополнительный фильтр по названию тендера (LIKE %_name_%)
Пример: ?name=посуда
</pre>

<pre>
<strong>date</strong> (необязательный, ассоциативный массив) - дополнительный фильтр по дате изменения тендера

<strong>при запросе фильтрации по дате необходимо передать следующие элементы массива:</strong>

date["symbol"] = <=|<|=|>|>= (направление для фильтрации)
date["value"] = 14.08.2022|14.08.2022 12:00:00
</pre>

Пример запроса:
<pre>
z-test.loc/api/v1/tender/list.php?codes[]=152467180&codes[]=152467336&date[symbol]=>=&date[value]=14.08.2022
</pre>

Пример ответа (JSON):
<pre>
"code": 200,
    "items": [
        {
            "ext_code": "152467180",
            "number": "17660-2",
            "status": "Закрыто",
            "name": "Лабороаторная посуда",
            "date_change": "14.08.2022 19:25:14"
        },
        {
            "ext_code": "152467336",
            "number": "18138",
            "status": "Отменено",
            "name": "Приобретение оборудования Регион-DXE для ОАО Компания г.Череповец",
            "date_change": "14.08.2022 19:25:14"
        }
    ]
}
</pre>
