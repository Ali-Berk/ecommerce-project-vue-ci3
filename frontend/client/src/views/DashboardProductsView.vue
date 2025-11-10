<template>
  <section class="container mt-4">
    <div class="d-flex justify-content-between mb-4 align-items-center">
        <h3 class="mb-3">Ürün Yönetimi</h3>
        <router-link to="/dashboard/addProduct" class="btn btn-success px-3">Ürün Ekle</router-link>
    </div>

    <!-- Filtre Alanı -->
    <div class="filters mb-4">
      <input
        v-model="search"
        placeholder="Ürün adı ara..."
        class="form-control mb-2"
      />
      <select v-model="selectedCategory" class="form-select mb-2">
        <option value="">Tüm Kategoriler</option>
        <option v-for="cat in categories" :key="cat">{{ cat }}</option>
      </select>
      <select v-model="stockFilter" class="form-select">
        <option value="">Tüm Stoklar</option>
        <option value="in">Stokta</option>
        <option value="out">Stokta Yok</option>
      </select>
    </div>

    <!-- Ürün Tablosu -->
    <table class="table table-bordered align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Görsel</th>
          <th>Başlık</th>
          <th>Kategori</th>
          <th>Fiyat</th>
          <th>Stok</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in filteredProducts" :key="p.product_id">
          <td>{{ p.product_id }}</td>
          <td>
            <img
              :src="p.thumbnail"
              alt="ürün görseli"
              width="60"
              class="rounded"
            />
          </td>
          <td>{{ p.title }}</td>
          <td>{{ p.category }}</td>
          <td>{{ p.price }} ₺</td>
          <td>
            <span :class="p.stock > 0 ? 'text-success' : 'text-danger'">
              {{ p.stock > 0 ? "Stokta" : "Tükendi" }}
            </span>
          </td>
          <td>
            <router-link class="btn btn-warning btn-sm me-2" :to="`/dashboard/products/${p.product_id}`">Düzenle</router-link>
            
            <button
              class="btn btn-danger btn-sm"
              @click="deleteProduct(p.product_id)"
            >
              Sil
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-if="!filteredProducts.length" class="text-muted mt-3 text-center">
      Hiç ürün bulunamadı.
    </p>
  </section>
</template>

<script>
import { onMounted, ref, computed } from "vue";
import { useProductStore } from "../store/ProductsStore";
import axios from "axios";

export default {
  name: "DashboardProductsView",

  setup() {
    const productStore = useProductStore();
    const { products, loadCategory } = productStore;

    const search = ref("");
    const selectedCategory = ref("");
    const stockFilter = ref("");

    onMounted(() => {
      loadCategory();
    });

    const categories = computed(() => {
      const set = new Set(products.map((p) => p.category));
      return [...set];
    });

    const filteredProducts = computed(() =>
      products.filter((p) => {
        const matchTitle = p.title
          .toLowerCase()
          .includes(search.value.toLowerCase());
        const matchCategory = selectedCategory.value
          ? p.category === selectedCategory.value
          : true;
        const matchStock =
          stockFilter.value === "in"
            ? p.stock > 0
            : stockFilter.value === "out"
            ? p.stock <= 0
            : true;
        return matchTitle && matchCategory && matchStock;
      })
    );

    function deleteProduct(id) {
      
      if (confirm("Ürünü silmek istediğine emin misin?")) {
        axios
          .post("http://localhost:8080/api/delete_product", { 'product_id' : id })
          .then(() => {
            productStore.products = productStore.products.filter(
              (p) => p.product_id !== id
            );
          })
          .catch((err) => console.log("Silme hatası:", err));
      }
    }

    function editProduct(p) {
      console.log(p);
    }

    return {
      products,
      search,
      selectedCategory,
      stockFilter,
      categories,
      filteredProducts,
      deleteProduct,
      editProduct,
    };
  },
};
</script>

<style scoped>
.filters {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 10px;
}
</style>