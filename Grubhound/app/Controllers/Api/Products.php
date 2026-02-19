<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProductModel;

class Products extends BaseController
{
    public function index()
    {
        $request = \Config\Services::request();
        $categoryId = $request->getGet('category_id');
        $includeHidden = $request->getGet('include_hidden');

        $model = new ProductModel();

        if ($categoryId) {
            $model->where('category_id', $categoryId);
        }

        if (!$includeHidden) {
            $model->where('is_available', 1);
        }

        // Default sort
        $model->orderBy('category_id', 'ASC')->orderBy('name', 'ASC');

        $items = $model->findAll();

        return $this->response->setJSON(['success' => true, 'items' => $items]);
    }
}
