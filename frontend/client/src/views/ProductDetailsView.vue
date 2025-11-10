<template>
<section class="container">
    <div class="py-4">
        BREADCRUMB
    </div>
    <div class="py-4" v-if="product">
        <div class="row">
            <div class="col-md-6">
                <div class="border rounded overflow-hidden text-center mb-3 bg-light main-image-wrapper">
                    <img
                    :src="selectedImage || product.thumbnail"
                    alt="Ürün Görseli"
                    class="main-image img-fluid"
                    />
                </div>
                
                <div class="position-relative mt-2">
                    <button v-if="product.images.lenght > 5"
                        class="btn btn-light position-absolute top-50 start-0 translate-middle-y shadow-sm"
                        id="thumb-prev"
                        type="button"
                        @click="document.getElementById('thumb-inner').scrollBy({ left: -500, behavior: 'smooth' })"
                        >
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    
                    <div class="overflow-hidden mx-5">
                        <div class="d-flex flex-nowrap" id="thumb-inner" style="gap: 8px;">
                            <div
                            v-for="(img, index) in product.images"
                            :key="index"
                            class="thumb-wrapper flex-shrink-0"
                            @click="selectedImage = img.image_url"
                            >
                            <img
                            :src="img.image_url"
                            alt="Ürün küçük görseli"
                            class="thumb-image img-fluid"
                            />
                        </div>
                    </div>
          </div>
          
          <!-- Sağ ok -->
          <button v-if="product.images.lenght > 5"
            class="btn btn-light position-absolute top-50 end-0 translate-middle-y shadow-sm"
            id="thumb-next"
            type="button"
            @click="document.getElementById('thumb-inner').scrollBy({ left: 500, behavior: 'smooth' })"
            >
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>
</div>

<!-- SAĞ KISIM: Ürün bilgileri -->
<div class="col-md-6">
    <h2 class="fw-bold">{{ product.name }}</h2>
    <p class="text-muted">{{ product.description }}</p>
    <h4 class="text-danger mb-3">₺{{ product.price }}</h4>
    
    <div class="mb-4">
        <p class="fw-semibold mb-1">Ürün Seçenekleri</p>
        <div class="border rounded p-3 text-center text-muted">
            Henüz mevcut değil
        </div>
    </div>
    
    <div class="border-top pt-3 mb-3">
        <p class="text-muted mb-0">
            Teslimat: 2-3 iş günü içinde kargoda
        </p>
    </div>
    
    <button class="btn btn-primary btn-lg w-100">
        Sepete Ekle
    </button>
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
  height: 400px;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f8f9fa;
}

.main-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.thumb-wrapper {
  width: 100px;
  height: 100px;
  border: 2px solid transparent;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
  transition: border-color 0.2s;
}

.thumb-wrapper:hover {
  border-color: #0d6efd;
}

.thumb-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

#thumb-prev,
#thumb-next {
  z-index: 10;
  width: 38px;
  height: 38px;
  border-radius: 50%;
}

#thumb-inner {
  width: max-content;
  transition: all 0.3s ease;
}

.mx-5 {
  max-width: 520px;
  overflow-x: hidden;
}

</style>