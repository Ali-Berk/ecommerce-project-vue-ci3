import {defineStore} from 'pinia';
import axios from "axios";
interface Order {
    order_id: number;
    order_items_id:number;
    total_price:number;
    status:string;
    order_mail:string;
    order_name:string;
    user_name:string;
}
export const useOrdersStore = defineStore('Orders', {
    state: () => ({
        // products:[{product_id:1,title:"err",thumbnail:"https://developers.elementor.com/docs/assets/img/elementor-placeholder-image.png",price:1}]
        order_data:[] as Order[],
        status_data:null,
        selected_order: null as Order | null ,
    }),

    actions:{
        async get_orders(){
            await axios.get("http://localhost:8080/api/orders", {withCredentials:true})
            .then(res => {this.order_data = res.data.data; this.status_data = res.data.status;});      
        },

        find_order(order_id:number){
            const selection = this.order_data.find(o => o.order_id == order_id) || null;
            this.selected_order = selection || null;
        }
  },
})