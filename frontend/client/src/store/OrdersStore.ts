import { defineStore } from 'pinia';
import axios from "axios";
import { stringifyQuery } from 'vue-router';

interface Order_Item{
    id:number;
	title:string;
	price:number;
	qty:number;
	thumbnail:string;
}
interface Order {
    order_id: number;
    order_items_id: any;
    total_price: number;
    status: string;
    order_mail: string;
    order_name: string;
	order_address:string;
	order_tel:string;
    user_name: string;
    order_date: string;
	items: Order_Item;
}

export const useOrdersStore = defineStore('Orders', {
    state: () => ({
        order_data: [] as Order[],
        active_order_data: Number,
        status_data: null as string | null,
        selected_order: null as Order | null,
    }),
    getters: {
        activeOrderCount(state): number {
            const targetStatuses = ["Sipariş Alındı", "Hazırlanıyor", "Kargoya Verildi", "Teslim Edildi", "İptal Edildi"]; 
            return state.order_data.filter(order => 
                targetStatuses.includes(order.status)
            ).length;
        }
    },
    actions: {
        async get_orders() {
            try {
                const res = await axios.get("http://localhost:8080/api/orders", { withCredentials: true });
                console.log(res.data);
                const formattedData = res.data.data.map((order: any) => {
                    return {
                        ...order,
                        order_date: new Date(order.order_date).toLocaleDateString('tr-TR', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        })
                    };
                });
                this.order_data = formattedData;
                this.status_data = res.data.status;

            } catch (error) {
                console.error("Siparişler çekilemedi:", error);
                this.status_data = 'error';
            }
        },

        find_order(order_id: number) {
            const selection = this.order_data.find(o => o.order_id == order_id) || null;
            this.selected_order = selection || null;
            return selection;
        },

        update_order(data:Order){
            axios.post("http://localhost:8080/api/update_order", data, {withCredentials:true});
        },

        delete_order(order_id:number){
            axios.post("http://localhost:8080/api/delete_order", order_id, {withCredentials:true}).catch(err => console.log(err));
        }
    },
})