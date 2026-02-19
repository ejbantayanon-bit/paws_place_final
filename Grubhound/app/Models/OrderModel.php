<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'order_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pre_order_code', 'final_code', 'customer_name', 'order_source',
        'total_amount', 'status', 'time_placed', 'time_paid'
    ];

    // Dates
    protected $useTimestamps = false; // We manage time_placed manually or via DB default
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'time_placed';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get orders with their items and payment info
     */
    public function getOrdersWithItems($status = null, $limit = 50)
    {
        $builder = $this->builder();
        $builder->select('orders.*');
        
        if ($status) {
            $builder->where('status', $status);
        } else {
             // If no status, maybe latest? logic from old file: 
             // "Default: Fetch latest 50 orders of any status"
             // But for history view it filters differently.
             // letting controller handle complex filters if needed, simpler here.
        }
        
        $builder->orderBy('time_placed', 'DESC');
        if ($limit) $builder->limit($limit);
        
        $orders = $builder->get()->getResultArray();
        
        if (empty($orders)) return [];

        $db = \Config\Database::connect();

        foreach ($orders as &$order) {
            // Get Items
            $itemBuilder = $db->table('order_items oi');
            $itemBuilder->select('oi.order_item_id, oi.menu_item_id, mi.name, oi.quantity, oi.price_at_sale, oi.modifiers');
            $itemBuilder->join('menu_items mi', 'oi.menu_item_id = mi.item_id', 'left');
            $itemBuilder->where('oi.order_id', $order['order_id']);
            $order['order_items'] = $itemBuilder->get()->getResultArray();

            // Get Payment (Cash Paid)
            $payBuilder = $db->table('payments');
            $payBuilder->select('amount');
            $payBuilder->where('order_id', $order['order_id']);
            $payBuilder->orderBy('payment_id', 'DESC');
            $payBuilder->limit(1);
            $payment = $payBuilder->get()->getRowArray();
            
            $order['cash_paid'] = $payment ? (float)$payment['amount'] : (float)$order['total_amount'];
        }

        return $orders;
    }
}
