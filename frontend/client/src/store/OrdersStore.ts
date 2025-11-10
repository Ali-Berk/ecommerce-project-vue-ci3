import {defineStore} from 'pinia';
import axios from "axios";

export const useOrdersStore = defineStore('Orders', {
    state: () => ({
        // products:[{product_id:1,title:"err",thumbnail:"https://developers.elementor.com/docs/assets/img/elementor-placeholder-image.png",price:1}]
        order_data:{},
        status_data:null
    }),

    actions:{
        async get_orders(){
            await axios.get("http://localhost:8080/api/orders", {withCredentials:true})
            .then(res => {this.order_data = res.data.order; this.status_data = res.data.status;});      
    }
  },
})