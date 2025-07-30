<?php

namespace App\Services;

use App\Traits\ConsumeExternalService;

class ServicesApi
{
    use ConsumeExternalService;

    /**
     * The base uri to consume authors service
     * @var string
     */
    public $baseUri;

    /**
     * Authorization secret to pass to author api
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri =env('BASE_URL') ;
        $this->secret = config('services.auth.secret');
    }

    public function sendMessage($data)
    {
        return $this->performRequest("POST", 'send-message', $data);
    }

    // // DRIVER
    // public function login($data)
    // {
    //     return $this->performRequest("POST", 'api/v1/admin/login', $data);
    // }

    // public function getListDriver()
    // {
    //     return $this->performRequest("GET", 'api/v1/admin/driver');
    // }
    // public function changeStatusAktif($id, $status)
    // {
    //     return $this->performRequest("GET", 'api/v1/driver/' . $id . '/activation/' . $status);
    // }

    // public function getDriver($id)
    // {
    //     return $this->performRequest("GET", 'api/v1/admin/driver/' . $id);
    // }

    // public function updatedDriver($data, $id)
    // {
    //     return $this->performRequest("POST", 'api/v1/driver/' . $id, $data);
    // }

    // // STORE
    // public function getListStore()
    // {
    //     return $this->performRequest("GET", 'api/v1/store/admin');
    // }


    // public function getStore($id)
    // {
    //     return $this->performRequest("GET", 'api/v1/admin/store/' . $id);
    // }

    // public function updatedStore($data, $id)
    // {
    //     return $this->performRequest("POST", 'api/v1/store/' . $id, $data);
    // }

    // public function changeStatusAktifStore($id, $status)
    // {
    //     return $this->performRequest("GET", 'api/v1/store/' . $id . '/activation/' . $status);
    // }

    // public function getListProductStore($id)
    // {
    //     return $this->performRequest("GET", 'api/v1/store/' . $id);
    // }

    // // PRODUCT
    // public function changeStatusDeleteProduct($id, $status)
    // {
    //     return $this->performRequest("GET", "api/v1/product/" . $id . "/" . $status);
    // }
    // public function updateProduct($id, $data)
    // {
    //     return $this->performRequest("POST", "api/v1/product/" . $id, $data);
    // }

    // // Dashboard
    // public function dashboard()
    // {
    //     return $this->performRequest("GET", "api/v1/admin/dashboard");
    // }

    // public function sawCustomer()
    // {
    //     return $this->performRequest("GET", "api/v1/admin/customerPromo");
    // }

    // public function giftPromoCustomer($data)
    // {
    //     return $this->performRequest("POST", "api/v1/admin/promo",$data);
    // }

    // public function listBenefit()
    // {
    //     return $this->performRequest("GET", "api/v1/admin/listBenefit");
    // }

    // public function getListPromoCustomer(){
    //     return $this->performRequest("GET","api/v1/admin/listPromo");
    // }
    // public function changePasswordAdmin($data){
    //     return $this->performRequest("POST","api/v1/admin/changepass",$data);
    // }


    // // CUSTOMER
    // public function getListCustomer()
    // {
    //     return $this->performRequest("GET", 'api/v1/customer');
    // }
    // public function getTransactionCustomer($id)
    // {
    //     return $this->performRequest("GET", 'api/v1/admin/transaction/customer/'.$id);
    // }
    // public function updateCustomer($data,$id)
    // {
    //     return $this->performRequest("POST", 'api/v1/admin/customer/'.$id,$data);
    // }


    // // Transaction
    // public function getListTransactionFromAdmin(){
    //     return $this->performRequest("GET","api/v1/admin/transaction/admin");
    // }

    // // Detailtransaction
    // public function getDetailTransaction($notrans){
    //     return $this->performRequest("GET","/api/v1/admin/detailTransaction/".$notrans);
    // }


    // // Management
    // public function getManagementSystem(){
    //     return $this->performRequest("GET","/api/v1/management");
    // }
    // public function updateManagementSystem($data){
    //     return $this->performRequest("POST","/api/v1/management",$data);
    // }

    // // request saldo
    // public function requestSaldo(){
    //     return $this->performRequest("GET","/api/v1/admin/saldo");
    // }
    // public function updateRequestSaldoStore($id,$status){
    //     return $this->performRequest("GET","/api/v1/admin/saldoStore/".$id."/".$status);
    // }
    // public function updateRequestSaldoDriver($id,$status){
    //     return $this->performRequest("GET","/api/v1/admin/saldoDriver/".$id."/".$status);
    // }
    // public function chartDashboard(){
    //     return $this->performRequest("GET","/api/v1/admin/chartdashboard");
    // }
}
