<template>
  <div id="categoryCarousel" class="carousel slide mt-3" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div v-for="(categories, cIndex) in category" :key="categories.category">
        <div v-for="(img, iIndex) in categories.images" :key="iIndex" :class="['carousel-item', { active: cIndex === 0 && iIndex === 0 }]">
          <img :src="img" class="d-block w-100 hero-img" alt="Kategori Görseli">
        </div>
      </div>
    </div>

    <!-- Kontroller -->
    <button class="carousel-control-prev" type="button" data-bs-target="#categoryCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#categoryCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>
</template>

<script>
export default {
  name: 'CarouselComponent',
  props: ['category']
}
</script>

<style>
.hero-img {
  object-fit: cover;
  max-height: 700px;
}
</style>
