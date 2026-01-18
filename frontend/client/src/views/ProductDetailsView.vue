<template>
  <section class="container my-5" v-if="product">
    
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Anasayfa</a></li>
        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Kategori</a></li>
        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ product.name }}</li>
      </ol>
    </nav>

    <div class="row g-5">
      <div class="col-lg-7">
        <div class="gallery-container position-relative">
          
          <div class="main-image-wrapper shadow-sm mb-4">
            <img
              :src="selectedImage || product.thumbnail"
              alt="Ürün Görseli"
              class="main-image"
            />
            <!-- <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill">Yeni</span> -->
          </div>
          
          <div class="position-relative px-4">
            <button v-if="product.images && product.images.length > 4"
                class="btn btn-white nav-btn start-0"
                type="button"
                @click="document.getElementById('thumb-inner').scrollBy({ left: -120, behavior: 'smooth' })">
                <i class="bi bi-chevron-left"></i>
            </button>
            
            <div class="thumb-scroller" id="thumb-inner">
                <div class="d-flex gap-3">
                    <div
                    v-for="(img, index) in product.images"
                    :key="index"
                    class="thumb-wrapper"
                    :class="{ 'active-thumb': (selectedImage === img.image_url) || (!selectedImage && index === 0) }"
                    @click="selectedImage = img.image_url"
                    >
                    <img
                    :src="img.image_url"
                    alt="Ürün küçük görseli"
                    class="thumb-image"
                    />
                </div>
              </div>
            </div>
          
            <button v-if="product.images && product.images.length > 4"
              class="btn btn-white nav-btn end-0"
              type="button"
              @click="document.getElementById('thumb-inner').scrollBy({ left: 120, behavior: 'smooth' })">
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>

        </div>
      </div>

      <div class="col-lg-5">
        <div class="product-info-sticky">
          
          <h1 class="display-6 fw-bold mb-2 text-dark">{{ product.name }}</h1>
          
          <div class="d-flex align-items-center gap-3 mb-4">
            <h2 class="price-tag mb-0">₺{{ product.price }}</h2>
            <span class="text-muted text-decoration-line-through fs-5" v-if="false">₺9999</span> </div>

          <p class="text-muted description-text mb-5">{{ product.description }}</p>
          
          <div class="mb-4 p-3 bg-light rounded-3 border border-light">
             <div class="d-flex align-items-center gap-2 text-muted">
                <i class="bi bi-info-circle"></i>
                <span>Standart Boyut / Özel Tasarım</span>
             </div>
          </div>
          
          <div class="d-flex gap-3 mb-4">
            <button class="btn btn-dark btn-lg flex-grow-1 rounded-pill py-3 shadow-lg btn-add-cart">
               Sepete Ekle
            </button>
            <button class="btn btn-outline-secondary btn-lg rounded-circle btn-fav">
               <i class="bi bi-heart"></i>
            </button>
          </div>

          <div class="features small text-muted">
            <div class="d-flex align-items-center gap-3 mb-2">
                <i class="bi bi-truck fs-5 text-dark"></i>
                <span>2-3 iş günü içinde kargoda</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-shield-check fs-5 text-dark"></i>
                <span>%100 Güvenli Ödeme ve İade Garantisi</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios'
import { useRoute } from 'vue-router'

export default {
    name: 'ProductDetail',
    data() {
        return {
            product: null,
            selectedImage: null,
        }
    },
    mounted() {
        const route = useRoute()
    const slug = route.params.slug

    axios
      .get(`http://localhost:8080/api/detail/${slug}`)
      .then((res) => {
        this.product = res.data.data
      })
      .catch((err) => {
        console.error('Ürün yüklenemedi:', err)
      })
  },
}
</script>

<style scoped>
.main-image-wrapper {
  height: 500px;
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #f0f0f0;
}

.main-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.main-image-wrapper:hover .main-image {
  transform: scale(1.02);
}

.thumb-scroller {
  overflow-x: hidden;
  white-space: nowrap;
  padding: 5px 0;
  scroll-behavior: smooth;
}

.thumb-wrapper {
  width: 80px;
  height: 80px;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  opacity: 0.6;
  transition: all 0.2s ease;
  background: #fff;
}

.thumb-wrapper:hover {
  opacity: 1;
}

.thumb-wrapper.active-thumb {
  border-color: #000;
  opacity: 1;
  transform: translateY(-2px);
}

.thumb-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 35px;
  height: 35px;
  border-radius: 50%;
  background: white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #eee;
  z-index: 10;
  transition: all 0.2s;
}

.nav-btn:hover {
  background: #000;
  color: #fff;
}

.product-info-sticky {
  position: sticky;
  top: 100px;
}

.price-tag {
  font-size: 2rem;
  font-weight: 800;
  color: #000;
  letter-spacing: -1px;
}

.description-text {
  font-size: 1rem;
  line-height: 1.7;
}

.btn-add-cart {
  font-weight: 600;
  letter-spacing: 0.5px;
  transition: transform 0.2s;
}

.btn-add-cart:hover {
  transform: translateY(-2px);
  background-color: #222;
}

.btn-fav {
  width: 58px;
  height: 58px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-color: #ddd;
}

.btn-fav:hover {
  background-color: #ffe5e5;
  border-color: #ffcccc;
  color: #dc3545;
}

@media (max-width: 991px) {
  .main-image-wrapper {
    height: 350px;
  }
  .product-info-sticky {
    position: static;
  }
}
</style>