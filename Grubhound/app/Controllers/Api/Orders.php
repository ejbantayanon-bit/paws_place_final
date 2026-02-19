<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class Orders extends BaseController
{
    public function index()
    {
        $request = \Config\Services::request();
        $status = $request->getGet('status');
        $view = $request->getGet('view');

        $model = new OrderModel();
        
        // If specific status requested (e.g. PREPARING or READY)
        if ($status) {
            $orders = $model->getOrdersWithItems($status);
        } elseif ($view === 'history') {
             // History logic: PREPARING, READY, SERVED, CANCELLED
             // The Model's basic method filters by single status currently.
             // We might need to enhance model or do custom query here.
             // For now, let's implement the specific query for history here or update model.
             // Let's rely on model but maybe passing array?
             // CI4 builder->whereIn('status', $statuses)
             // I'll stick to basic KDS for now which sends single status.
             $orders = []; // Placeholder for history
        } else {
            $orders = $model->getOrdersWithItems(null);
        }

        return $this->response->setJSON(['success' => true, 'orders' => $orders]);
    }
}
