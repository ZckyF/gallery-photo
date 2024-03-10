<template>
  <div class="about-container">
    <header>
      <h1>{{ aboutTitle }}</h1>
    </header>
    <section>
      <p>{{ aboutContent }}</p>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      // aboutTitle: 'an***g',
      // aboutContent: 'Ini adalah tugas projek yang menjengkelkan,merepotkan,dan sulit. i hate this task, bullshit',
    };
  },
};
</script>

<style scoped>
.about-container {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  background-color: #f5f5f5;
}

header {
  color: #fff;
  text-align: center;
  padding: 1em;
}

section {
  max-width: 800px;
  margin: 2em auto;
  padding: 20px;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}

h1 {
  color: #333;
}

p {
  line-height: 1.6;
  color: #555;
}

footer {
  text-align: center;
  padding: 1em;
  background-color: #333;
  color: #fff;
}
</style>
