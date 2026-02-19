<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    public function index()
    {
        $model = new CategoryModel();
        $categories = $model->where('is_active', 1)
                            ->orderBy('sort_order', 'ASC')
                            ->findAll();
                            
        return $this->response->setJSON(['success' => true, 'categories' => $categories]);
    }
}
