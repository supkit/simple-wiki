<?php

namespace App\Controller\Api;

use App\Model\Collection;
use App\Model\Category;
use App\Model\Document;
use Simple\Mvc\Controller;

class CategoryController extends Controller
{
    /**
     * @param $collectionId
     * @return array
     * @throws \ErrorException
     */
    public function list($collectionId)
    {
        $collection = new Collection();
        $category = new Category();
        $document = new Document();

        $data['collection'] = [];
        $data['category'] = [];

        $collectionData = $collection->where('id', '=', $collectionId)->select()->fetch();

        $categoryList = $category->where('collectionId', '=', $collectionId)->select()->fetchAll();
        $categoryIds = array_column($categoryList, 'id');

        $data['collection'] = $collectionData;


        if (empty($categoryIds)) {
            $documentList = [];
        } else {
            $documentList = $document->where('categoryId', 'IN', $categoryIds)->select()->fetchAll();
        }

        foreach ($categoryList as $i => $item) {
            $categoryList[$i]['item'] = [];
            foreach ($documentList as $doc) {
                if ($item['id'] == $doc['categoryId']) {
                    $doc['request'] = json_decode($doc['request']);
                    array_push($categoryList[$i]['item'], $doc);
                }
            }
        }
        $data['category'] = $categoryList;

        return self::success($data);
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            return [];
        }

        $input = file_get_contents('php://input');
        $input = json_decode($input, true);

        $input['icon'] = empty($input['icon']) ? 'md md-folder' : $input['icon'];

        $category = new Category();
        $id = $category->insert($input, true);

        return self::success(['id' => $id]);
    }
}
