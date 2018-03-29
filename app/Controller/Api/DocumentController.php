<?php

namespace App\Controller\Api;

use App\Model\Document;
use Simple\Mvc\Controller;
use Simple\Http\Request;

class DocumentController extends Controller
{
    /**
     * @return array
     * @throws \ErrorException
     */
    public function create()
    {
        $input = self::input();

        $input['requestMethod'] = empty($input['requestMethod']) ? 'GET' : $input['requestMethod'];
        $input['requestContentType'] = empty($input['requestContentType']) ? '1' : $input['requestContentType'];
        $input['requestHeader'] = empty($input['requestHeader']) ? '[]' : $input['requestHeader'];
        $input['requestBody'] = empty($input['requestBody']) ? '[]' : $input['requestBody'];

        $document = new Document();
        $id = $document->insert($input, true);

        return self::success(['id' => $id]);
    }

    /**
     * @param $id
     * @return array
     * @throws \ErrorException
     */
    public function data($id)
    {
        $document = new Document();
        $data = $document->find($id);

        return self::success($data);
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function update()
    {
        $input = self::input();
        $input['requestMethod'] = empty($input['requestMethod']) ? 'GET' : $input['requestMethod'];
        $input['requestContentType'] = empty($input['requestContentType']) ? '1' : $input['requestContentType'];
        $input['requestHeader'] = empty($input['requestHeader']) ? '[]' : json_encode($input['requestHeader']);
        $input['requestBody'] = empty($input['requestBody']) ? '[]' : json_encode($input['requestBody']);

        $documentId = $input['documentId'];

        $document = new Document();

        $update = $input;
        unset($update['documentId']);

        $curl = new Request();

        $requestUrl = $input['requestUrl'];
        $requestMethod = $input['requestMethod'];
        $requestHeader = $input['requestHeader'];
        $requestBody = $input['requestBody'];
        $requestContentType = $input['requestContentType'];

        $isJson = $requestContentType == 3 ? true : false;

        foreach (json_decode($requestHeader, true) as $item) {
            if ($item['enable'] == true) {
                $curl->setHeader($item['key'], $item['value']);
            }
        }

        $sendData = [];

        foreach (json_decode($requestBody, true) as $item) {
            if ($item['enable'] == true) {
                $sendData[$item['key']] = $item['value'];
            }
        }

        $sendData = $requestContentType == 2 ? http_build_query($sendData) : $sendData;

        call_user_func_array([$curl, strtolower($requestMethod)], [$requestUrl, $sendData, $isJson]);

        $response = $curl->response;
        $responseHttpStatusCode = $curl->httpStatusCode;

        if (json_decode($response)) {
            $responseType = 'json';
        } else {
            $responseType = 'html';
            $file = __DIR__ . '/../../../public/response/html/'.$documentId.'.html';
            file_put_contents($file, $response);
        }

        $update['response'] = $response;
        $update['responseType'] = $responseType;
        $update['responseHttpCodeStatus'] = $responseHttpStatusCode;
        $update['updateTime'] = time();
        $document->where('id', '=', $documentId)->update($update);

        $data = [
            'id' => $documentId,
            'responseType' => $responseType,
            'responseHttpCodeStatus' => $responseHttpStatusCode,
            'response' => $response
        ];

        return self::success($data);
    }
}