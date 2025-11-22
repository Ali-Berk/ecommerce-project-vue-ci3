<template>
  <section class="container my-5">
    <h2 class="mb-4">Ödeme / Checkout</h2>

    <div v-if="UserStore.cart.length === 0">
      <p>Sepetiniz boş. Önce alışveriş yapın.</p>
      <router-link to="/" class="btn btn-primary mt-3">Alışverişe Başla</router-link>
    </div>

    <div v-else class="row">
      <!-- Sipariş Formu -->
      <div class="col-md-6">
        <h4>Fatura Bilgileri</h4>
        <form @submit.prevent="submitOrder">
          <div class="mb-3">
            <label class="form-label">Ad Soyad</label>
            <input type="text" class="form-control" v-model="customer.name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">E-posta</label>
            <input type="email" class="form-control" v-model="customer.mail" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Adres</label>
            <textarea class="form-control" v-model="customer.address" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn btn-success w-100">Siparişi Onayla</button>
        </form>
      </div>

      <!-- Sepet Özeti -->
      <div class="col-md-6">
        <h4>Sepetiniz</h4>
        <div v-for="item in UserStore.cart" :key="item.id" class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <p class="mb-0">{{ item.title }} x {{ item.qty }}</p>
            <small>{{ item.price }} TL / adet</small>
          </div>
          <div>
            <strong>{{ item.qty * item.price }} TL</strong>
          </div>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
          <strong>Toplam:</strong>
          <strong>{{ totalPrice }} TL</strong>
        </div>
      </div>
    </div>
  </section>
</template>

<script lang="ts">
import { defineComponent, reactive, computed } from 'vue';
import { useUserStore } from '../store/UserStore';
import axios from 'axios';

export default defineComponent({
  name: 'CheckoutView',
  setup() {
    const UserStore = useUserStore();
    const customer = reactive({
        name: UserStore.user?.name || '',
        mail: UserStore.user?.mail || '',
        address: UserStore.user?.address || ''
    });

    const totalPrice = computed(() =>
      UserStore.cart.reduce((acc, item) => acc + item.price * item.qty, 0)
    );

    const submitOrder = async () => {
      try {
        const orderData = {
          customer,
          items: UserStore.cart,
          total: totalPrice.value
        };
        await axios.post('http://localhost:8080/api/createOrder', orderData, { withCredentials: true });
        alert('Siparişiniz başarıyla oluşturuldu!');
        UserStore.cart = [];
      } catch (error) {
        console.error(error);
        alert('Sipariş oluşturulurken bir hata oluştu.');
      }
    };

    return { UserStore, customer, totalPrice, submitOrder };
  }
});
</script>

<style scoped>
textarea {
  resize: none;
}
</style>
