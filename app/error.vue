<template>
  <section class="error_section">
    <p class="error_section_subtitle">Thanks. You just broke it all !</p>
    <h1 class="error_title">
      <p>404</p>
      404
    </h1>
    <a href="/" class="btn">get me out of here</a>
  </section>
</template>

<script lang="ts" setup>
const title: any = document.querySelector('.error_title');

//////// Light //////////
document.onmousemove = function (e) {
  let x = e.pageX - window.innerWidth / 2;
  let y = e.pageY - window.innerHeight / 2;

  title.style.setProperty('--x', x + 'px');
  title.style.setProperty('--y', y + 'px');
};

////////////// Shadow ///////////////////
title.onmousemove = function (e) {
  let x = e.pageX - window.innerWidth / 2;
  let y = e.pageY - window.innerHeight / 2;

  let rad = Math.atan2(y, x).toFixed(2);
  let length = Math.round(Math.sqrt(Math.pow(x, 2) + Math.pow(y, 2)) / 10);

  let x_shadow = Math.round(length * Math.cos(rad));
  let y_shadow = Math.round(length * Math.sin(rad));

  title.style.setProperty('--x-shadow', -x_shadow + 'px');
  title.style.setProperty('--y-shadow', -y_shadow + 'px');
};
</script>

<style lang="scss" scoped>
$primary-color: #26c6da;
$secondary-color: #29b6f6;
$black: #343434;
$dark-black: #1a1a1a;
$gradient: linear-gradient(to top, $primary-color, $secondary-color);
$black-gradient: linear-gradient(-225deg, $dark-black, $black);
* {
  margin: 0;
  padding: 0;
}
a {
  text-decoration: none;
}
body {
  font-family: 'Source Sans Pro', sans-serif;
  font-weight: 600;
  color: $black;
}
.error_section {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  height: 100vh;
  background-image: $black-gradient;
  &_subtitle {
    color: $secondary-color;
    text-transform: uppercase;
    letter-spacing: 5pt;
    font-weight: 500;
    font-size: 0.8rem;
    margin-bottom: -5em;
  }
  .error_title {
    --x-shadow: 0;
    --y-shadow: 0;
    --x: 50%;
    --y: 50%;
    font-size: 15rem;
    transition: all 0.2s ease;
    position: relative;
    padding: 2rem;
    &:hover {
      transition: all 0.2s ease;
      text-shadow: var(--x-shadow) var(--y-shadow) 10px $dark-black;
    }
    p {
      position: absolute;
      top: 2rem;
      left: 2rem;
      background-image: radial-gradient(circle closest-side, rgba(white, 0.05), transparent);
      background-position: var(--x) var(--y);
      background-repeat: no-repeat;
      text-shadow: none;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      transition: all 0.1s ease;
    }
  }
}
.btn {
  padding: 0.8em 1.5em;
  border-radius: 99999px;
  background-image: $gradient;
  box-shadow: 0px 2px 5px 0px rgba(0, 0, 0, 0.2), inset 0px -2px 5px 0px rgba(0, 0, 0, 0.2);
  border: none;

  cursor: pointer;

  text-shadow: 0px 1px $black;
  color: white;
  text-transform: uppercase;
  letter-spacing: 1.5pt;
  font-family: 'Source Sans Pro', sans-serif;
  font-size: 0.8rem;
  font-weight: 700;

  transition: ease-out 0.2s all;

  &:hover {
    text-shadow: 0px 1px 1px $black;
    transform: translateY(-5px);
    box-shadow: 0px 4px 15px 2px rgba(0, 0, 0, 0.1), inset 0px -3px 7px 0px rgba(0, 0, 0, 0.2);
    transition: ease-out 0.2s all;
  }
}
</style>
