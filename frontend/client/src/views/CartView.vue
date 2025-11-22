<template>
  <section class="container my-5">
    <h2 class="mb-4">Sepetim</h2>

    <div v-if="UserStore.cart.length === 0">
      <p>Sepetiniz şu anda boş.</p>
      <router-link to="/" class="btn btn-primary mt-3">Alışverişe Başla</router-link>
    </div>

    <div v-else>
      <div v-for="item in UserStore.cart" :key="item.id" class="d-flex justify-content-between align-items-center mb-3 p-2 shadow-sm rounded">
        <img :src="item.thumbnail" alt="Ürün" style="width:80px; height:80px; object-fit:cover;">
        
        <div class="flex-grow-1 ms-3 d-flex flex-column">
          <p class="mb-1">{{ item.title }}</p>
          <small>{{ item.price }} TL</small>
          <div class="d-flex align-items-center mt-1 qty-input-wrapper">
            <button class="btn btn-outline-secondary btn-sm" @click="decreaseQty(item)">-</button>
            <input type="number" v-model.number="item.qty" min="1" class="form-control form-control-sm text-center mx-1" style="width:60px;">
            <button class="btn btn-outline-secondary btn-sm" @click="increaseQty(item)">+</button>
          </div>
        </div>

        <div>
          <strong>{{ item.qty * item.price }} TL</strong>
        </div>
      </div>

      <hr>
      <div class="d-flex justify-content-between align-items-center mt-3">
        <h4>Toplam: {{ totalPrice }} TL</h4>
        <div>
          <router-link to="/checkout" class="btn btn-success me-2">Alışverişi Tamamla</router-link>
          <router-link to="/" class="btn btn-primary">Alışverişe Devam Et</router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';
import { useUserStore } from '../store/UserStore';

export default defineComponent({
  name: 'CartView',
  setup() {
    const UserStore = useUserStore();

    const increaseQty = (item: any) => {
      item.qty += 1;
    };

    const decreaseQty = (item: any) => {
      item.qty = Math.max(1, item.qty - 1);
    };

    const totalPrice = computed(() =>
      UserStore.cart.reduce((acc, item) => acc + item.price * item.qty, 0)
    );

    return { UserStore, increaseQty, decreaseQty, totalPrice };
  }
});
</script>

<style scoped>
.qty-input-wrapper button {
  border-radius: 5px;
  padding: 0 0.5rem;
  font-weight: bold;
}

.qty-input-wrapper input {
  border-radius: 5px;
  border: 1px solid #ced4da;
  height: 30px;
}
</style>
