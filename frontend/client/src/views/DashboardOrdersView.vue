<template>
  <section class="container mt-4">
    <h3 class="mb-3">Sipariş Yönetimi</h3>

    <div class="filters mb-4">
      <input v-model="search" placeholder="Kullanıcı veya adres ara..." class="form-control mb-2" />
      <select v-model="statusFilter" class="form-select mb-2">
        <option value="">Tüm Durumlar</option>
        <option value="pending">Beklemede</option>
        <option value="shipped">Kargolandı</option>
        <option value="completed">Tamamlandı</option>
        <option value="canceled">İptal Edildi</option>
      </select>
    </div>

    <table class="table table-bordered align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Kullanıcı</th>
          <th>Toplam</th>
          <th>Adres</th>
          <th>Durum</th>
          <th>Tarih</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="o in filteredOrders" :key="o.order_id">
          <td>{{ o.order_id }}</td>
          <td>{{ o.user_name }}</td>
          <td>{{ o.price }} ₺</td>
          <td>{{ o.order_address }}</td>
          <td>
            <span
              :class="{
                'text-warning': o.status === 'pending',
                'text-primary': o.status === 'shipped',
                'text-success': o.status === 'completed',
                'text-danger': o.status === 'canceled'
              }"
            >
              {{ o.status }}
            </span>
          </td>
          <td>{{ formatDate(o.created_at) }}</td>
          <td>
            <button class="btn btn-info btn-sm me-2" @click="viewDetails(o)">Detay</button>
            <button class="btn btn-danger btn-sm" @click="deleteOrder(o.order_id)">Sil</button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-if="!filteredOrders.length" class="text-muted mt-3 text-center">Hiç sipariş bulunamadı.</p>
  </section>
</template>

<script>
import axios from "axios";

export default {
  name: "DashboardOrdersView",
  data() {
    return {
      orders: [],
      search: "",
      statusFilter: ""
    };
  },
  computed: {
    filteredOrders() {
      return this.orders.filter(o => {
        const matchSearch =
        //   o.user_name.toLowerCase().inclusdes(this.search.toLowerCase()) ||
          o.order_address.toLowerCase().includes(this.search.toLowerCase());
        const matchStatus = this.statusFilter ? o.status === this.statusFilter : true;
        return matchSearch && matchStatus;
      });
    }
  },
  methods: {
    async loadOrders() {
      try {
        const res = await axios.get("http://localhost:8080/api/get_all_orders");
        this.orders = res.data.orders;
      } catch (err) {
        console.log("Siparişler yüklenemedi:", err);
      }
    },
    deleteOrder(id) {
      if (confirm("Bu siparişi silmek istediğine emin misin?")) {
        axios
          .post("http://localhost:8080/api/delete_order", { id })
          .then(() => {
            this.orders = this.orders.filter(o => o.order_id !== id);
          })
          .catch(err => console.log("Silme hatası:", err));
      }
    },
    viewDetails(o) {
      alert(`Sipariş #${o.order_id} detay sayfasına yönlendirilecek (örnek).`);
    },
    formatDate(date) {
      return new Date(date).toLocaleString("tr-TR");
    }
  },
  mounted() {
    this.loadOrders();
  }
};
</script>

<style scoped>
.filters {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 10px;
}
</style>
