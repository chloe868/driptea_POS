<template>
    <div class="sudlanan">
         <div>
         <v-btn icon style="margin-right: 1%;"  @click="previous()">
                <v-icon >mdi-home</v-icon>
            </v-btn>
    </div>
        <div class="row firstRow">
            <div class="col-md-6">
                <center>
                    <v-card class="ml-10">
                        <center>
                            <img v-if="customerType === 'walkin'" style="width: 70px; height: 50px; border: solid 1px black" src="@/assets/walkin.jpg">
                            <img v-if="customerType === 'foodpanda'" style="width: 70px; height: 50px;" src="@/assets/foodpanda1.png">
                            <img v-if="customerType === 'grab'" style="width: 70px; height: 50px;" src="@/assets/grab2.png">
                            <img v-if="customerType === 'fb'" style="width: 70px; height: 50px;" src="@/assets/fb1.png"><br>
                            <img v-if="customerType === 'online'" style="width: 70px; height: 50px;" src="@/assets/logo.png"><br>
                            <span v-if="error" style="color: red; font-style: italic">All data are required!</span>
                            <table class="table table-responsive table-bordered overline" id="myTable">
                                <tr>
                                    <th style="width: 45%;">Product Name</th>
                                    <th>Add&nbsp;ons</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th style="width: 15px;">❌</th>
                                </tr>
                                <tbody>
                                    <tr v-for="(item, index) in tableData" :key="index">
                                        <td>{{item.order_product[0].productName}}</td>
                                        <td>{{getAddOns(item.same_order)}}</td>
                                        <td>{{item.choosenPrice}}</td>
                                        <td>{{item.quantity}}</td>
                                        <td>{{item.subTotal}}</td>
                                        <td>
                                            <button style="font-size: 10px" type="button" aria-expanded="false" @click="deleteOrder(item.id)">❌</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </center>
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6 overline" style="text-align:left;">
                                <p v-if="customerType === 'fb' || customerType === 'online'" style="display: inline;">Subtotal:&emsp;&emsp;&emsp;</p>
                                <p v-if="customerType === 'fb' || customerType === 'online'" style="display: inline;">₱ {{getSubTotal()}}</p><br>
                                <p v-if="customerType === 'fb' || customerType === 'online'" style="display: inline;">Delivery&nbsp;Fee:&emsp;</p>
                                <input v-if="customerType === 'fb' || customerType === 'online'" style="display: inline;" type="number" placeholder="₱ 0.00" v-model="fee">
                                <p style="display: inline;" class="pStyle">Total:&emsp;&emsp;&emsp;&emsp;</p>
                                <p style="display: inline;" class="pStyle">₱ {{convertTotalPrice()}}</p><br>
                                <p v-if="customerType !== 'fb'  && customerType !== 'online'" style="display: inline;" class="pStyle">Amount:&emsp;&emsp;&nbsp;&nbsp;&nbsp;</p>
                                <input v-if="customerType !== 'fb'  && customerType !== 'online'" style="display: inline;" type="number" placeholder="₱ 0.00" v-model="cash"><br>
                                <p v-if="customerType !== 'fb'  && customerType !== 'online'" style="display: inline;" class="pStyle">Change:&emsp;&emsp;&emsp;</p>
                                <p v-if="customerType !== 'fb'  && customerType !== 'online'" style="display: inline;" class="pStyle">₱ {{convertChange()}}</p>
                                <div >
                                 <button class="btn btn-primary checkout overline" @click="checkoutOrder">Checkout</button>
                                </div>
                            </div>
                        </div>
                    </v-card>
                </center>
            </div>
            <div class="col-md-6">
                <div class="dataStyle">
                    <div class="row">
                        <div class="col-md-5 secondCol" v-for="(item, index) in data" :key="index">
                          <v-card class="elevation-5" max-width="250" height="250">
                            <v-img  max-width="250" height="250" :src="item.image" @click="redirect(item.productCategory)"></v-img>
                          </v-card>

                        </div>
                    </div>
                </div>
            </div>
            <receipt v-if="receiptShow" :showData="receiptData"></receipt>
            <loading v-if="loadingShow"></loading>
       </div>
    </div>
</template>
<style scoped>
.dataStyle{
    height: 700px;
    overflow-y: scroll;
}
.checkout{
    margin-top: 3%;
    height: 40% !important;
    font-size: 20px; width: 200px;
}
.btn{
    height: 33px;
    width: 150px;
}
input{
    /* height: 35px; */
    /* margin-top: -50px; */
    /* margin-bottom: 15px; */
    width: 80px;
    border-radius: 5px;
}
::-webkit-scrollbar {
  width: 1px;
}
.pStyle{
    font-weight: bold;
}
p{
    margin-top: -10%;
}
.firstRow{
    margin-bottom: 5%;
    margin-top: 3%;
}
.sudlanan{
    background-color:white;
}
table{
    height: 450px;
    width: 100%;
}
.table tr{
   text-align: center;
}
th {
   width: 100%;
}
@media screen and (max-width: 800px) {
    input{
        width: 60px;
        margin-left: -15px;
    }
    
    table{
        height: 350px;
        width: 100%;
    }
    .dataStyle{
        height: 490px !important;
        overflow-y: scroll;
    }
}
</style>
<script>
import AUTH from '../../services/auth'
import ROUTER from '../../router'
import receipt from '../order/receipt.vue'
import config from '../../config.js'
import loading from '../../basic/loading.vue';
export default {
    data(){
        return{
            data: null,
            tableData: null,
            customerType: this.$route.params.image,
            config: config,
            deliveryFee: 0,
            totalPrice: 0,
            incash: 0,
            change: 0,
            subTotalPrice: 0,
            cash: null,
            fee: 0,
            error: false,
            receiptShow: false,
            receiptData: null,
            loadingShow: false
        }
    },
    components: {
        receipt,
        loading
    },
    mounted(){
        this.retrieveCategory()
        this.retrieveProduct()
    },
    methods: {
        hideReceipt(){
            this.receiptShow = false
        },
        addingFee(){
            this.deliveryFee = this.fee
        },
        addingIncash(){
            this.incash = this.cash
        },
        convertTotalPrice(){
            if(this.subTotalPrice === 0){
                this.getSubTotal()
            }
            if(this.fee !== '' || this.fee > 0){
                let total = this.subTotalPrice + parseInt(this.fee)
                this.totalPrice = total
                return parseInt(total).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
            }else{
                let total = this.subTotalPrice
                this.totalPrice = total
                return parseInt(total).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
            }
        },
        convertChange(){
            if(this.cash > this.totalPrice){
                let amountChange = this.cash - this.totalPrice
                return amountChange.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
            }else{
                return this.change.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
            }
        },
        retrieveCategory(){
            this.loadingShow = true
            this.$axios.post(AUTH.url + 'retrieveCategory', {}, AUTH.config).then(res => {
                if(res.data.status){
                    AUTH.deauthenticate()
                }
                this.data = res.data.addCategory
                this.loadingShow = false
            })
        },
        getSubTotal(){
            if(this.tableData != null){
                let total = 0
                this.tableData.forEach(element => {
                    total += element.subTotal
                });
                this.subTotalPrice = total
                return parseInt(total).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
            }
        },
        redirect(param){
            ROUTER.push('/chosenCategory/'+param).catch(()=>{})
        },
        retrieveProduct(){
            this.loadingShow = true
            if(this.customerType === 'online'){
                let params = {
                    id: localStorage.getItem('customerId')
                }
                this.$axios.post(AUTH.url + 'getOrder', params, AUTH.config).then(res => {
                    if(res.data.status){
                        AUTH.deauthenticate()
                    }
                    this.tableData = res.data.order
                    this.loadingShow = false
                })
            }else{
                let params = {
                    id: localStorage.getItem('customerId')
                }
                this.$axios.post(AUTH.url + 'retrieveOrder', params, AUTH.config).then(res => {
                    if(res.data.status){
                        AUTH.deauthenticate()
                    }
                    this.tableData = res.data.order
                    this.loadingShow = false
                })
            }
        },
        getAddOns(item){
            let storeAddOns = ""
            let index = item.length
            item.forEach(el => {
                if(item.indexOf(el) >= (index - 1)){
                    storeAddOns += el.addOns
                }else{
                    storeAddOns += el.addOns + ", "
                }
            })
            return storeAddOns
        },
        deleteOrder(prodId){
            this.loadingShow = true
            this.$axios.post(AUTH.url + 'deleteOrder', {id: prodId}, AUTH.config).then(res => {
                if(res.data.status){
                    AUTH.deauthenticate()
                }
                this.retrieveProduct()
                this.loadingShow = false
            })
        },
        checkoutMethod(){
            this.loadingShow = true
            let params = {
                id: localStorage.getItem('customerId'),
                status: 'complete'
            }
            this.$axios.post(AUTH.url + 'updateStatus', params, AUTH.config).then(res => {
                if(res.data.status){
                    AUTH.deauthenticate()
                }
                let params = {
                    customerId: localStorage.getItem('customerId'),
                    cashierId: localStorage.getItem('cashierId') ? localStorage.getItem('cashierId') : localStorage.getItem('adminId'),
                    subTotal: parseInt(this.getSubTotal()),
                    deliveryFee: this.fee,
                    total: parseInt(this.convertTotalPrice()),
                    incash: this.cash,
                    change: parseInt(this.convertChange()),
                    order: this.tableData
                }
                this.$axios.post(AUTH.url + 'addCheckout', params, AUTH.config).then(res => {
                    if(res.data.status){
                        AUTH.deauthenticate()
                    }
                    let low = 0
                    let high = 0
                    let over = 0
                    this.tableData.forEach(el => {
                        if(el.size === 'lowDose'){
                            low += el.quantity
                        }else if(el.size === 'highDose'){
                            high += el.quantity
                        }else if(el.size === 'overDose'){
                            over += el.quantity
                        }
                    })
                    let param = {
                        usedCupsLowDose: low,
                        usedCupsHighDose: high,
                        usedCupsOverDose: over
                    }
                    this.$axios.post(AUTH.url + 'updateRemainingCups', param, AUTH.config).then(response => {
                        if(response.data.status){
                            AUTH.deauthenticate()
                        }
                        let parameter = {
                            id: res.data.storeCheckouts.id,
                        }
                        this.$axios.post(AUTH.url + 'retrieveCheckouts', parameter, AUTH.config).then(response => {
                            if(response.data.status){
                                AUTH.deauthenticate()
                            }
                            this.loadingShow = false
                            this.receiptData = response.data.storeOrder
                            this.receiptShow = true
                        })
                    })
                })
            })
        },
        checkoutOrder(){
            if(this.customerType !== 'fb' && this.customerType !== 'online'){
                if(this.convertTotalPrice() !== null && this.cash !== null && this.convertChange() >= 0){
                    this.error = false
                    this.checkoutMethod()
                }else{
                    this.error = true
                }
            }else{
                if(this.getSubTotal() > 0 && this.fee !== '' && this.convertTotalPrice() > 0){
                    this.error = false
                    this.checkoutMethod()
                }else{
                    this.error = true
                }
            }
        },
         previous(){
             let type = localStorage.getItem("customerType");

            ROUTER.push('/casherDashboard').catch(() => {})
        }
    }
}
</script>
