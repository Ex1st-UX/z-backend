<?php

namespace App\Http;

class Request
{

    /**
     * @return array
     */
    public static function Post(): array
    {
        $post = $_POST;

        foreach ($post as $key => $item) {
            $arPost[$key] = trim(htmlspecialchars($item));
        }

        return $arPost;
    }

    /**
     * @return array
     */
    public static function Get(): array
    {
        $get = $_GET;

        if (isset($get['codes'])) {
            foreach ($get['codes'] as $code) {
                $arGet['codes'][] = trim(strip_tags($code));
            }

            unset($get['codes']);
        }

        if (isset($get['date'])) {
            foreach ($get['date'] as $key => $item) {
                $arGet['date'][$key] = trim($item);
            }

            unset($get['date']);
        }

        foreach ($get as $key => $item) {
            $arGet[$key] = trim(strip_tags($item));
        }

        return $arGet;
    }

    /**
     * @param string $text
     * @return void
     */
    public static function Error(string $text): void
    {
        $error = [
            'code' => 400,
            'error' => $text
        ];

        self::Response($error, $error['code']);
    }

    /**
     * @param $error - текст ответа
     * @param $code - код ответа http
     * @return void
     */
    public static function Response($mess, $code = 200)
    {
        echo json_encode($mess);

        http_response_code($code);
        die();
    }
}