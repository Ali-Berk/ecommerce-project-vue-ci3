<template>
  <section class="container mt-4">
    <h3 class="mb-3">Yeni Ürün Ekle</h3>

    <div v-if="message" :class="`alert ${messageType}`" role="alert">{{ message }}</div>

    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="form-label">Başlık</label>
        <input v-model="form.title" type="text" class="form-control" required />
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Fiyat (TL)</label>
          <input v-model.number="form.price" type="number" step="0.01" class="form-control" required />
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Kategori</label>
          <select v-model="form.category_fk" class="form-select" required>
            <option value="">Seçin</option>
            <option v-for="c in categories" :key="c.category_id" :value="c.category_id">{{ c.category_name }}</option>
          </select>
        </div>

        <div class="col-md-4 mb-3">
          <label class="form-label">Stok Adedi</label>
          <input v-model.number="form.stock" type="number" class="form-control" min="0" required />
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Kısa Açıklama</label>
        <textarea v-model="form.description" class="form-control" rows="3"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Thumbnail (URL)</label>
        <input v-model="form.thumbnail" type="url" class="form-control" placeholder="https://..." />
      </div>

      <div class="mb-3">
        <label class="form-label">Resimler (birden fazla yükleyebilirsiniz)</label>
        <input ref="filesInput" @change="onFilesChange" type="file" class="form-control" multiple accept="image/*" />
      </div>

      <div v-if="previews.length" class="mb-3 d-flex gap-2 flex-wrap">
        <div v-for="(p, i) in previews" :key="i" class="preview-item">
          <img :src="p" alt="" style="width:100px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #ddd" />
        </div>
      </div>

      <div class="d-flex gap-2">
        <button :disabled="loading" class="btn btn-primary" type="submit">
          {{ loading ? 'Gönderiliyor...' : 'Ürünü Ekle' }}
        </button>
        <button class="btn btn-secondary" type="button" @click="resetForm">Temizle</button>
      </div>
    </form>
  </section>
</template>

<script>
import axios from "axios";
import { useProductStore } from "../store/ProductsStore"; // senin store yolu neyse o
export default {
  name: "AdminAddProduct",
  data() {
    return {
      form: {
        title: "",
        price: null,
        category_fk: "",
        stock: 0,
        description: "",
        thumbnail: ""
      },
      files: [],        // File list
      previews: [],     // preview urls
      loading: false,
      message: "",
      messageType: "alert-success",
      categories: []
    };
  },
  methods: {
    async loadCategoriesFromApi() {
      try {
        const res = await axios.get("http://localhost:8080/api/get_categories"); 
        this.categories = Array.isArray(res.data) ? res.data : (res.data.categories || []);
      } catch (err) {
        console.error("Kategori yüklenemedi:", err);
        this.categories = [];
      }
    },
    onFilesChange(e) {
      const inputFiles = Array.from(e.target.files || []);
      this.files = inputFiles;
      this.previews = [];
      inputFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = ev => {
          this.previews.push(ev.target.result);
        };
        reader.readAsDataURL(file);
      });
    },
    resetForm() {
      this.form = { title: "", price: null, category_fk: "", stock: 0, description: "", thumbnail: "" };
      this.files = [];
      this.previews = [];
      this.$refs.filesInput && (this.$refs.filesInput.value = "");
      this.message = "";
    },
    async submit() {
     
      if (!this.form.title || !this.form.price || !this.form.category_fk) {
        this.messageType = "alert-danger";
        this.message = "Başlık, fiyat ve kategori zorunludur.";
        return;
      }

      this.loading = true;
      this.message = "";

      try {
        const fd = new FormData();
        fd.append("title", this.form.title);
        fd.append("price", this.form.price);
        fd.append("category_fk", this.form.category_fk);
        fd.append("stock", this.form.stock);
        fd.append("description", this.form.description || "");
        fd.append("thumbnail", this.form.thumbnail || "");

        // dosyaları ekle (backend order: files[])
        this.files.forEach((f) => {
          fd.append("images[]", f, f.name);
        });

        // API endpoint adını backendine göre ayarla
        const res = await axios.post("http://localhost:8080/api/addProduct", fd, {
          headers: { "Content-Type": "application/json" },
          withCredentials: true
        });
        console.log(res);
        if (res.data && (res.data.status === "success")) {
          this.messageType = "alert-success";
          this.message = "Ürün başarıyla eklendi.";
          // store'u yenile (varsa)
          try {
            const store = useProductStore();
            if (store && typeof store.loadCategory === "function") {
              await store.loadCategory();
            }
          } catch (e) {
            console.log(e);
          }
          this.resetForm();
        } else {
          this.messageType = "alert-danger";
          this.message = res.data.message;
        }
      } catch (err) {
        console.error(err);
        this.messageType = "alert-danger";
        this.message = "İstek sırasında bir hata oluştu.";
      } finally {
        this.loading = false;
      }
    }
  },
  mounted() {
    this.loadCategoriesFromApi();
  }
};
</script>

<style scoped>
.preview-item {
  display: inline-block;
}
</style>
