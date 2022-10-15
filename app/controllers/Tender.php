<?php

namespace App\Api;

use App\Http;

class Tender
{
    const API_KEY = '123jnfjakfk124u091fn';

    protected $request;

    function __construct($method)
    {
        $this->$method();
    }

    protected function createItem()
    {
        $this->request = Http\Request::Post();
        $requiredParams = ['code', 'number', 'status', 'name'];

        if ($this->isPossibleRequest($requiredParams)) {
            $Tender = \R::dispense('tenders');

            $Tender->ext_code = $this->request['code'];
            $Tender->number = $this->request['number'];
            $Tender->status = $this->request['status'];
            $Tender->name = $this->request['name'];
            $Tender->date_change = date("d.m.y") . ' ' . date("H:i:s");

            if (!$this->isItemExistByCode($this->request['code'])) {
                $id = \R::store($Tender);

                Http\Request::Response(['code' => 200, 'text' => 'success']);
            } else {
                Http\Request::Error('code must be unique');
            }
        }
    }

    protected function getItem()
    {
        $this->request = Http\Request::Get();
        $requiredParams = ['code'];

        if ($this->isPossibleRequest($requiredParams)) {
            $Tender = $this->getItemByCode($this->request['code']);

            if ($Tender->id) {
                $arItemResponse = $this->fillItemToArray($Tender);

                Http\Request::Response($arItemResponse);
            } else {
                Http\Request::Error("tender doesn't exist");
            }
        }
    }

    protected function getItemsList()
    {
        $this->request = Http\Request::Get();
        $requiredParams = ['codes'];

        if ($this->isPossibleRequest($requiredParams)) {
            foreach ($this->request['codes'] as $code) {

                $arDate = $this->getDateQuery();

                // фильтр по названию
                if (!empty($this->request['name']) && empty($this->request['date'])) {
                    $query = 'ext_code = ? AND name LIKE ?';
                    $values = [
                        $code,
                        '%' . $this->request['name'] . '%'
                    ];
                } // фильтр по дате
                elseif (!empty($this->request['date']) && empty($this->request['name'])) {
                    $query = 'ext_code = ?' . $arDate['query'];
                    $values = [
                        $code,
                        $arDate['value']
                    ];
                } // фильтр по дате и названию
                elseif (!empty($this->request['date']) && !empty($this->request['name'])) {
                    $query = 'ext_code = ? AND name LIKE ?' . $arDate['query'];
                    $values = [
                        $code,
                        '%' . $this->request['name'] . '%',
                        $arDate['value']
                    ];
                } else {
                    $query = 'ext_code = ?';
                    $values = [$code];
                }

                $res = \R::findOne('tenders', $query, $values);

                if ($res) {
                    $arItems[] = $this->fillItemToArray($res);
                }
                else {
                    Http\Request::Error('didnt find anything');
                }
            }
        }

        Http\Request::Response([
            'code' => 200,
            'items' => $arItems
        ]);
    }

    /**
     * Вставляет полученный тендер из БД в результирующий массив
     * @param \RedBeanPHP\OODBBean $res
     * @return void
     */
    protected function fillItemToArray(\RedBeanPHP\OODBBean $res)
    {
        $item = [
            'ext_code' => $res->ext_code,
            'number' => $res->number,
            'status' => $res->status,
            'name' => $res->name,
            'date_change' => $res->date_change
        ];

        return $item;
    }

    protected function getDateQuery()
    {
        if ($this->request['date']['symbol'] == '=') {
            $dateValue = '%' . $this->request['date']['value'] . '%';
            $dateQuery = ' AND date_change LIKE ?';
        } else {
            $dateValue = $this->request['date']['value'];
            $dateQuery = ' AND date_change ' . $this->request['date']['symbol'] . ' ?';
        }

        return [
            'query' => $dateQuery,
            'value' => $dateValue
        ];
    }

    protected function isPossibleRequest($requiredParams = []): bool
    {
        $this->checkParams($requiredParams);

        if ($this->checkAuthorization()) {
            return true;
        } else {
            return false;
        }
    }

    protected function checkAuthorization()
    {
        $bearer = getallheaders()['Authorization'];

        if ($bearer == self::API_KEY) {
            return true;
        } else {
            Http\Request::Error("API_KEY not match");
        }
    }

    protected function isItemExistByCode($code): bool
    {
        $res = \R::findOne('tenders', 'ext_code = ?', [$code]);

        if ($res)
            return true;
        else
            return false;
    }

    /**
     * @param $code - внешний код тендера
     * @return \RedBeanPHP\OODBBean - ORM объект тендера в БД
     */
    protected function getItemByCode($code): \RedBeanPHP\OODBBean
    {
        $res = \R::findOne('tenders', 'ext_code = ?', [$code]);

        if ($res) {
            return $res;
        } else {
            return new \RedBeanPHP\OODBBean();
        }
    }

    /**
     * Проверяет наличие обязательных параметров в запросе
     * @param array $paramsTypes - обязательные параметры
     * @return void
     */
    protected function checkParams(array $paramsTypes)
    {
        foreach ($paramsTypes as $paramType) {
            if (!array_key_exists($paramType, $this->request)) {
                Http\Request::Error($paramType . ' is requiered param');
            }
        }
    }
}