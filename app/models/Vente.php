<?php
class Vente {
    private $product_id;
    private $user_id;
    private $quantity;
    private $sale_date;
    private $client_info;

    public function __construct($product_id, $user_id, $quantity, $sale_date, $client_info) {
        $this->product_id = $product_id;
        $this->user_id = $user_id;
        $this->quantity = $quantity;
        $this->sale_date = $sale_date;
        $this->client_info = $client_info;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function getSaleDate() {
        return $this->sale_date;
    }

    public function getClientInfo() {
        return $this->client_info;
    }
}