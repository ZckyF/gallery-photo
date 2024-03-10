export default defineNuxtRouteMiddleware((to, from) => {
  const authToken = useCookie('auth_token'); // Ganti 'auth_token' dengan key yang sesuai

  if (authToken.value) {
    // Jika user sudah login dan mencoba ke halaman login atau join, arahkan ke '/'
    if (to.path === '/login' || to.path === '/join') {
      return navigateTo('/');
    }
  } else {
    // Jika user tidak memiliki token dan mencoba ke halaman lain selain login/join, arahkan ke '/login'
    if (to.path !== '/login' && to.path !== '/join') {
      return navigateTo('/login');
    }
  }
});
