<template>
  <section class="container mt-4">
    <h3 class="mb-3">Ürün Düzenle</h3>

    <div v-if="message" :class="`alert ${messageType}`">{{ message }}</div>

    <form v-if="loaded" @submit.prevent="updateProduct">
      <div class="mb-3">
        <label class="form-label">Başlık</label>
        <input v-model="form.title" type="text" class="form-control" required />
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Fiyat (₺)</label>
          <input v-model.number="form.price" type="number" step="0.01" class="form-control" required />
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Kategori</label>
          <select v-model="form.category" class="form-select" required>
            <option value="">Seçin</option>
            <option v-for="c in categories" :key="c.category_id" :value="c.category_name">
              {{ c.category_name }}
            </option>
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Stok Adedi</label>
          <input v-model.number="form.stock" type="number" min="0" class="form-control" />
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Açıklama</label>
        <textarea v-model="form.description" class="form-control" rows="3"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Thumbnail (URL)</label>
        <input v-model="form.thumbnail" type="url" class="form-control" placeholder="https://..." />
        <img
          v-if="form.thumbnail"
          :src="form.thumbnail"
          class="mt-2 border rounded"
          style="width:150px; height:100px; object-fit:cover;"
        />
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit" :disabled="loading">
          {{ loading ? 'Güncelleniyor...' : 'Kaydet' }}
        </button>
        <router-link class="btn btn-secondary" to="/dashboard/products">Geri</router-link>
      </div>
    </form>
    <p v-else class="text-muted">Yükleniyor...</p>
    </section>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '../store/ProductsStore'

const route = useRoute()
const store = useProductStore()

const form = reactive({
  product_id: '',
  title: '',
  price: 0,
  stock: 0,
  description: '',
  thumbnail: '',
  category:'',
  category_fk:1
})

const message = ref('')
const messageType = ref('alert-success')
const loading = ref(false)
const loaded = ref(false)

const categories = computed(() => store.categories)

function loadProductFromStore() {
  const id = route.params.slug;
  const p = (store.products).find(p => String(p.product_id) == id)
  if (p && categories) {
    form.product_id = p.product_id
    form.title = p.title ?? ''
    form.price = p.price ?? 0
    form.category = p.category ?? ''
    form.category_fk = 0
    form.stock = p.stock ?? 0
    form.description = p.description ?? ''
    form.thumbnail = p.thumbnail ?? ''
    loaded.value = true
  } else {
    message.value = 'Ürün bulunamadı.'
    messageType.value = 'alert-danger'
  }
}

async function updateProduct() {
  loading.value = true
  try {
    await store.updateProduct({ ...form })
    messageType.value = 'alert-success'
    message.value = 'Ürün başarıyla güncellendi.'
  } catch (err) {
    console.error(err)
    messageType.value = 'alert-danger'
    message.value = 'Güncelleme hatası.'
  } finally {
    loading.value = false
  }
}

onMounted(loadProductFromStore)
</script>
