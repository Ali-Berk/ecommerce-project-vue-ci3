<template>
  <section class="container mt-4" style="max-width: 900px;">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">

        <h3 class="mb-4 fw-semibold">Ürün Düzenle</h3>

        <div v-if="message" :class="`alert ${messageType} mb-4`">{{ message }}</div>

        <form v-if="loaded" @submit.prevent="updateProduct" class="row g-4">

          <div class="col-12">
            <label class="form-label fw-medium">Başlık</label>
            <input v-model="form.title" type="text" class="form-control form-control-lg" required />
          </div>

          <div class="col-md-4">
            <label class="form-label fw-medium">Fiyat (₺)</label>
            <input v-model.number="form.price" type="number" step="0.01" class="form-control" required />
          </div>

          <div class="col-md-4">
            <label class="form-label fw-medium">Kategori</label>
            <select v-model="form.category" class="form-select" required>
              <option value="">Seçin</option>
              <option v-for="c in categories" :key="c.category_id" :value="c.category_name">
                {{ c.category_name }}
              </option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-medium">Stok Adedi</label>
            <input v-model.number="form.stock" type="number" min="0" class="form-control" />
          </div>

          <div class="col-12">
            <label class="form-label fw-medium">Açıklama</label>
            <textarea v-model="form.description" class="form-control" rows="3"></textarea>
          </div>

          <div class="col-12">
            <label class="form-label fw-medium">Thumbnail (URL)</label>
            <input
              v-model="form.thumbnail"
              type="url"
              class="form-control"
              placeholder="https://..."
            />
            <img
              v-if="form.thumbnail"
              :src="form.thumbnail"
              class="mt-3 rounded border"
              style="width: 160px; height: 110px; object-fit: cover;"
            />
          </div>

          <div class="col-12">
            <label class="form-label fw-medium">Ürün Resimleri</label>

            <div
              v-for="image in form.images"
              :key="image.image_id"
              class="image-box p-3 border rounded mb-3 bg-light"
            >
              <div class="row g-3">
                <div class="col-md-3 d-flex align-items-center justify-content-center">
                  <img
                    :src="image.image_url"
                    :alt="image.alt_text"
                    class="rounded border"
                    style="width: 100%; object-fit: cover; max-height: 140px;"
                  />
                </div>

                <div class="col-md-9">
                  <label class="form-label">Resim URL</label>
                  <input type="text" v-model="image.image_url" class="form-control mb-3" />

                  <label class="form-label">Alt Yazı</label>
                  <input type="text" v-model="image.alt_text" class="form-control" />
                </div>
                <button @click="deleteImage(image.image_id)" type="button" class="image-box-item btn btn-danger">Resmi Sil</button>
              </div>
            </div>

            <div class="p-3 border rounded bg-white">
              <label class="form-label fw-medium">Yeni Resim Ekle</label>
              <input type="text" placeholder="Resim URL" class="form-control mb-2" v-model="form.newImage.image_url"/>
              <input type="text" placeholder="Alt Yazı" class="form-control" v-model="form.newImage.alt_text"/>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label fw-medium">Aktiflik Durumu</label>
            <select v-model="form.active" class="form-select">
              <option :value="1">Aktif</option>
              <option :value="0">Pasif</option>
            </select>
          </div>

          <div class="col-12 d-flex justify-content-end gap-2 mt-3">
            <button class="btn btn-primary px-4" type="submit" :disabled="loading">
              {{ loading ? 'Güncelleniyor...' : 'Kaydet' }}
            </button>

            <router-link class="btn btn-outline-secondary px-4" to="/dashboard/products">
              Geri
            </router-link>
          </div>
        </form>

        <p v-else class="text-muted">Yükleniyor...</p>
      </div>
    </div>
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
  category_fk:1,
  active: 1,
  images:{},
  newImage:{image_url:undefined,alt_text:undefined}
})

const message = ref('')
const messageType = ref('alert-success')
const loading = ref(false)
const loaded = ref(false)

const categories = computed(() => store.categories)

function loadProductFromStore() {
  const id = route.params.slug;
  const p = (store.products).find(p => String(p.product_id) == id)
  form.active = p.active ?? 1
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
    form.images = p.images ?? ''
  } else {
    message.value = 'Ürün bulunamadı.'
    messageType.value = 'alert-danger'
  }
}

async function updateProduct() {
  loading.value = true
  try {
    const clonedData = JSON.parse(JSON.stringify(form));
    await store.updateProduct(clonedData);
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

async function deleteImage(image_id) {
  store.deleteProductImage(this.route.params.slug ,image_id);
}

onMounted(loadProductFromStore)
</script>


<style scoped>
  .image-box{
    position:relative;
  }

  .image-box-item{
    position: absolute;
    top: -10px;
    right: 20px;
    width: 20%;
    padding: 6px;
  }
</style>
