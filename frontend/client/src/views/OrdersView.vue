<template>
  <section class="p-4">
    <div
      v-if="OrdersStore.status_data === 'error'"
      class="alert alert-danger shadow-sm rounded-3 text-center fw-semibold"
    >
      Siparişler yüklenirken bir hata oluştu. Lütfen sayfayı yenileyin.
    </div>

    <div v-else class="orders-container bg-white p-3 rounded-3 shadow-sm">
      <h2 class="mb-4 text-primary fw-bold">Siparişlerim</h2>

      <div class="table-responsive">
        <table class="table align-middle text-center table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th scope="col">Sipariş Kodu</th>
              <th scope="col">Adres</th>
              <th scope="col">Ürünler</th>
              <th scope="col">Tutar</th>
              <th scope="col">Durum</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in OrdersStore.order_data" :key="order.order_id">
              <td class="fw-semibold text-primary">{{ order.order_id }}</td>
              <td class="text-muted">{{ order.order_address }}</td>
              <td>
                <span class="badge bg-light text-dark px-3 py-2 shadow-sm">
                  {{ order.product_titles }}
                </span>
              </td>
              <td class="fw-bold text-success">{{ order.total_price }} ₺</td>
              <td>
                <span
                  :class="order.status === 'tamamlandı'
                    ? 'badge bg-success bg-opacity-75'
                    : 'badge bg-warning text-dark bg-opacity-75'"
                >
                  {{ order.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<script>
import { useOrdersStore } from '@/store/OrdersStore';

export default {
  computed: {
    OrdersStore() {
      return useOrdersStore();
    },
  },
  mounted() {
    this.OrdersStore.get_orders();
  },
};
</script>

<style scoped>
.orders-container {
  border: 1px solid #dee2e6;
}

table {
  border-radius: 10px;
  overflow: hidden;
}

th {
  background-color: #f8f9fa !important;
  color: #495057;
  font-weight: 600;
  font-size: 0.95rem;
}

td {
  vertical-align: middle;
  font-size: 0.95rem;
}

tr:hover {
  background-color: #f1f5ff !important;
  transition: all 0.2s ease-in-out;
}

.badge {
  font-size: 0.85rem;
  border-radius: 8px;
}

.alert {
  font-size: 1.05rem;
}
</style>
